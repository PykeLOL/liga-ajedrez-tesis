<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use App\Models\Permiso;
use App\Models\Rol;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class PermisoController extends Controller
{
    public function index()
    {
        $permisos = Permiso::with('roles')->get();
        $permisos = $permisos->map(function ($permiso) {
            return [
                'id' => $permiso->id,
                'nombre' => $permiso->nombre,
                'descripcion' => $permiso->descripcion,
            ];
        });

        return response()->json($permisos);
    }

    public function show($id)
    {
        $permiso = Permiso::find($id);
        if (!$permiso) {
            return response()->json(['message' => 'Permiso no encontrado'], 404);
        }
        return response()->json($permiso, 200);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'required|string|max:255',
            'tipo_accion_id'  => 'required|integer|exists:tipo_accion,id',
            'modulo_id'  => 'required|integer|exists:modulos,id',
        ]);

        $permiso = Permiso::create([
            'nombre' => $validated['nombre'],
            'descripcion' => $validated['descripcion'],
            'tipo_accion_id' => $validated['tipo_accion_id'],
            'modulo_id' => $validated['modulo_id'],
        ]);

        return response()->json([
            'message' => 'Permiso creado exitosamente',
            'permiso' => $permiso
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $permiso = Permiso::find($id);
        if (!$permiso) {
            return response()->json(['message' => 'Permiso no encontrado'], 404);
        }

        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'required|string|max:255',
            'tipo_accion_id'  => 'required|integer|exists:tipo_accion,id',
            'modulo_id'  => 'required|integer|exists:modulos,id',
        ]);

        $permiso->update([
            'nombre' => $validated['nombre'],
            'descripcion' => $validated['descripcion'],
            'tipo_accion_id' => $validated['tipo_accion_id'],
            'modulo_id' => $validated['modulo_id'],
        ]);

        return response()->json([
            'message' => 'Permiso actualizado correctamente',
            'permiso' => $permiso
        ], 200);
    }


    public function destroy($id)
    {
        $permiso = Permiso::find($id);
        if (!$permiso) {
            return response()->json(['message' => 'Permiso no encontrado'], 404);
        }

        $permiso->delete();

        return response()->json(['message' => 'Permiso eliminado correctamente'], 200);
    }

    public function quitarPermiso(Request $request)
    {
        try {
            if($request->tipo == 'usuario') {
                $request->validate([
                    'id' => 'required|numeric|exists:usuarios,id',
                    'permiso' => 'required|numeric|exists:permisos,id',
                ]);

                $usuario = Usuario::findOrFail($request->id);
                $usuario->permisos()->detach($request->permiso);
            } else {
                $request->validate([
                    'id' => 'required|numeric|exists:roles,id',
                    'permiso' => 'required|numeric|exists:permisos,id',
                ]);

                $rol = Rol::findOrFail($request->id);
                $rol->permisos()->detach($request->permiso);
            }

            return response()->json(['message' => 'Permiso eliminado correctamente.']);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Ocurrió un error inesperado al procesar la solicitud.',
            ], 500);
        }
    }

    public function asignarPermiso(Request $request)
    {
        try {
            if($request->tipo == 'usuario') {
                $request->validate([
                    'id' => 'required|numeric|exists:usuarios,id',
                    'permiso' => 'required|numeric|exists:permisos,id',
                ]);

                $usuario = Usuario::findOrFail($request->id);
                $usuario->permisos()->detach($request->permiso);
                $permiso = Permiso::findOrFail($request->permiso);

                if ($usuario->permisos()->where('permiso_id', $permiso->id)->exists()) {
                    return response()->json([
                        'message' => 'El permiso ya está asignado a este usuario.'
                    ], 409);
                }

                $usuario->permisos()->attach($permiso->id);

                return response()->json([
                    'message' => 'Permiso asignado exitosamente al usuario.',
                    'usuario' => $usuario->nombre . " " . $usuario->apellido,
                    'permiso' => $permiso->nombre
                ], 201);
            } else {
                $request->validate([
                    'id' => 'required|numeric|exists:roles,id',
                    'permiso' => 'required|numeric|exists:permisos,id',
                ]);

                $rol = Rol::findOrFail($request->id);
                $rol->permisos()->detach($request->permiso);
                $permiso = Permiso::findOrFail($request->permiso);

                if ($rol->permisos()->where('permiso_id', $permiso->id)->exists()) {
                    return response()->json([
                        'message' => 'El permiso ya está asignado a este rol.'
                    ], 409);
                }

                $rol->permisos()->attach($permiso->id);

                return response()->json([
                    'message' => 'Permiso asignado exitosamente al rol.',
                    'rol' => $rol->nombre . " | " . $rol->descripcion,
                    'permiso' => $permiso->nombre
                ], 201);
            }
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Credenciales de validación inválidas.',
                'errors' => $e->errors(),
            ], 422);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'El usuario o permiso especificado no se encontró.',
            ], 404);

        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Ocurrió un error inesperado al procesar la solicitud.',
            ], 500);
        }
    }

    public function asignarPermisoRol(Request $request)
    {
        try {
            $request->validate([
                'rol_id' => 'required|numeric|exists:roles,id',
                'permiso_id' => 'required|numeric|exists:permisos,id',
            ]);

            $rol = Rol::findOrFail($request->rol_id);
            $permiso = Permiso::findOrFail($request->permiso_id);

            if ($rol->permisos()->where('permiso_id', $permiso->id)->exists()) {
                return response()->json([
                    'message' => 'El permiso ya está asignado a este rol.'
                ], 409);
            }

            $rol->permisos()->attach($permiso->id);

            return response()->json([
                'message' => 'Permiso asignado exitosamente al rol.',
                'rol' => $rol->nombre,
                'permiso' => $permiso->nombre
            ], 201);

        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Credenciales de validación inválidas.',
                'errors' => $e->errors(),
            ], 422);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'El rol o permiso especificado no se encontró.',
            ], 404);

        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Ocurrió un error inesperado al procesar la solicitud.',
            ], 500);
        }
    }
}
