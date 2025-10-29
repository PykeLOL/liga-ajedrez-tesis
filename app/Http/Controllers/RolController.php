<?php

namespace App\Http\Controllers;

use App\Models\Rol;
use App\Models\Permiso;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class RolController extends Controller
{
    public function index()
    {
        $roles = Rol::all();
        return response()->json($roles);
    }

    public function show($id)
    {
        $rol = Rol::find($id);
        if (!$rol) {
            return response()->json(['message' => 'Rol no encontrado'], 404);
        }
        return response()->json($rol, 200);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255|unique:roles,nombre',
            'descripcion' => 'nullable|string|max:500',
        ]);

        $rol = Rol::create([
            'nombre' => $validated['nombre'],
            'descripcion' => $validated['descripcion'] ?? null,
        ]);

        return response()->json($rol, 201);
    }

    public function update(Request $request, $id)
    {
        log::info("Request: " . json_encode($request->all()));
        $rol = Rol::find($id);
        if (!$rol) {
            return response()->json(['message' => 'Rol no encontrado'], 404);
        }

        $validated = $request->validate([
            'nombre' => 'sometimes|string|max:255|unique:roles,nombre,' . $id,
            'descripcion' => 'nullable|string|max:500',
        ]);

        if (isset($validated['nombre'])) {
            $rol->nombre = $validated['nombre'];
        }
        if (array_key_exists('descripcion', $validated)) {
            $rol->descripcion = $validated['descripcion'];
        }
        $rol->save();

        return response()->json($rol, 200);
    }

    public function destroy($id)
    {
        $rol = Rol::find($id);
        if (!$rol) {
            return response()->json(['message' => 'Rol no encontrado'], 404);
        }
        $rol->delete();
        return response()->json(['message' => 'Rol eliminado'], 200);
    }

    public function permisosRol($id)
    {
        $rol = Rol::findOrFail($id);

        $permisosRol = $rol->permisos->map(function ($permiso) {
            return [
                'id' => $permiso->id,
                'nombre' => $permiso->nombre,
                'descripcion' => $permiso->descripcion
            ];
        });

        return response()->json([
            'rol_id' => $rol->id,
            'nombre_rol' => $rol->nombre,
            'permisos_rol' => $permisosRol,
        ]);
    }

    public function permisosDisponibles($id)
    {
        $rol = Rol::findOrFail($id);
        $permisosAsignados = $rol->permisos->pluck('id')->toArray();

        $permisos = Permiso::whereNotIn('id', $permisosAsignados)->get(['id', 'nombre', 'descripcion']);

        return response()->json($permisos);
    }
}
