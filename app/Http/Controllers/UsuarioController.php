<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use App\Models\Permiso;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UsuarioController extends Controller
{
    public function index()
    {
        $usuarios = Usuario::with('rol')->get();
        $usuarios = $usuarios->map(function ($usuario) {
            return [
                'id' => $usuario->id,
                'nombre' => $usuario->nombre,
                'apellido' => $usuario->apellido,
                'email' => $usuario->email,
                'documento' => $usuario->documento,
                'telefono' => $usuario->telefono,
                'rol' => $usuario->rol ? $usuario->rol->nombre : 'Sin rol',
                'imagen_path' => $usuario->imagen_path
            ];
        });

        return response()->json($usuarios);
    }

    public function show($id)
    {
        $usuario = Usuario::find($id);
        if (!$usuario) {
            return response()->json(['message' => 'Usuario no encontrado'], 404);
        }
        return response()->json($usuario, 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'documento' => 'required|string|max:50',
            'email' => 'required|email|unique:usuarios,email',
            'telefono' => 'nullable|string|max:50',
            'contraseña' => 'required|string|min:5|same:confirmar_contraseña',
            'confirmar_contraseña' => 'required|string|min:5',
            'rol_id' => 'nullable|integer|exists:roles,id',
            'imagen' => 'nullable|image|max:2048',
        ], [
            'email.unique' => 'El correo electrónico ya está registrado.',
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'El correo electrónico no tiene un formato válido.',
            'contraseña.same' => 'Las contraseñas no coinciden.',
            'confirmar_contraseña.required' => 'Debes confirmar tu contraseña.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error de validación.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $validated = $validator->validated();

        $path = null;
        if ($request->hasFile('imagen')) {
            $path = $request->file('imagen')->store('usuarios', 'public');
        }

        $usuario = Usuario::create([
            'nombre' => $validated['nombre'],
            'apellido' => $validated['apellido'],
            'documento' => $validated['documento'] ?? null,
            'email' => $validated['email'],
            'telefono' => $validated['telefono'] ?? null,
            'contraseña' => Hash::make($validated['contraseña']),
            'rol_id' => $validated['rol_id'] ?? null,
            'estado' => true,
            'imagen_path' => $path,
        ]);

        return response()->json([
            'message' => 'Usuario creado exitosamente',
            'usuario' => $usuario
        ], 201);
    }

    public function storeAdmin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'documento' => 'nullable|string|max:50',
            'email' => 'required|email|unique:usuarios,email',
            'telefono' => 'nullable|string|max:50',
            'rol_id' => 'nullable|integer|exists:roles,id',
            'imagen' => 'nullable|image|max:2048',
        ], [
            'email.unique' => 'El correo electrónico ya está registrado.',
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'El correo electrónico no tiene un formato válido.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error de validación.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $validated = $validator->validated();

        $imageName = null;
        if ($request->imagen_base64) {
            $imageData = $request->imagen_base64;
            $image = str_replace('data:image/png;base64,', '', $imageData);
            $image = str_replace('data:image/jpeg;base64,', '', $image);
            $image = str_replace(' ', '+', $image);

            $imageName = 'usuarios/' . uniqid() . '.png';
            \Storage::disk('public')->put($imageName, base64_decode($image));
        }

        $usuario = Usuario::create([
            'nombre' => $validated['nombre'],
            'apellido' => $validated['apellido'],
            'documento' => $validated['documento'] ?? null,
            'email' => $validated['email'],
            'telefono' => $validated['telefono'] ?? null,
            'contraseña' => Hash::make('ligadeajedrez'), // Contraseña por defecto para admin
            'rol_id' => $validated['rol_id'] ?? null,
            'estado' => true,
            'imagen_path' => $imageName,
        ]);

        return response()->json([
            'message' => 'Usuario creado exitosamente',
            'usuario' => $usuario
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $usuario = Usuario::find($id);
        if (!$usuario) {
            return response()->json(['message' => 'Usuario no encontrado'], 404);
        }

        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'documento' => 'nullable|string|max:50',
            'email' => 'required|email|unique:usuarios,email,' . $id,
            'telefono' => 'nullable|string|max:50',
            'contraseña' => 'required|string|min:6',
            'rol_id' => 'nullable|integer|exists:roles,id',
            'imagen' => 'nullable|image|max:2048',
        ], [
            'email.unique' => 'El correo electrónico ya está registrado.',
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'El correo electrónico no tiene un formato válido.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error de validación.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $validated = $validator->validated();

        if ($request->imagen_base64) {
            $imageData = $request->imagen_base64;
            $image = str_replace('data:image/png;base64,', '', $imageData);
            $image = str_replace('data:image/jpeg;base64,', '', $image);
            $image = str_replace(' ', '+', $image);

            $imageName = 'usuarios/' . uniqid() . '.png';
            \Storage::disk('public')->put($imageName, base64_decode($image));

            $usuario->imagen_path = $imageName;
        }

        $plainPassword = $request->input('contraseña');

        $usuario->update([
            'nombre' => $validated['nombre'],
            'apellido' => $validated['apellido'],
            'documento' => $validated['documento'] ?? $usuario->documento,
            'email' => $validated['email'],
            'telefono' => $validated['telefono'] ?? $usuario->telefono,
            'contraseña' => isset($plainPassword) ? Hash::make($plainPassword) : $usuario->contraseña,
            'rol_id' => $validated['rol_id'] ?? $usuario->rol_id,
            'imagen_path' => $usuario->imagen_path,
        ]);

        return response()->json([
            'message' => 'Usuario actualizado correctamente',
            'usuario' => $usuario
        ], 200);
    }


    public function destroy($id)
    {
        $usuario = Usuario::find($id);
        if (!$usuario) {
            return response()->json(['message' => 'Usuario no encontrado'], 404);
        }

        $usuario->delete();

        return response()->json(['message' => 'Usuario eliminado correctamente'], 200);
    }

    public function permisosUsuario($id)
    {
        $usuario = Usuario::findOrFail($id);

        $permisosRol = $usuario->rol->permisos->map(function ($permiso) {
            return [
                'id' => $permiso->id,
                'nombre' => $permiso->nombre,
                'descripcion' => $permiso->descripcion
            ];
        });

        $permisosUsuario = $usuario->permisos->map(function ($permiso) {
            return [
                'id' => $permiso->id,
                'nombre' => $permiso->nombre,
                'descripcion' => $permiso->descripcion
            ];
        });

        return response()->json([
            'usuario_id' => $usuario->id,
            'nombre_rol' => $usuario->rol->nombre,
            'permisos_rol' => $permisosRol,
            'permisos_usuario' => $permisosUsuario,
        ]);
    }

    public function permisosDisponibles($id)
    {
        $usuario = Usuario::findOrFail($id);
        $permisosAsignados = $usuario->permisos->pluck('id')->toArray();

        $permisos = Permiso::whereNotIn('id', $permisosAsignados)->get(['id', 'nombre', 'descripcion']);

        return response()->json($permisos);
    }

    public function misPermisos()
    {
        $usuario = auth()->user();

        if (!$usuario) {
            return response()->json(['error' => 'No autenticado'], 401);
        }

        // Obtenemos permisos por rol y directos
        $rolePermisos = $usuario->rol->permisos ?? collect();
        $userPermisos = $usuario->permisos ?? collect();

        // Unimos y filtramos duplicados
        $permisos = $rolePermisos->merge($userPermisos)->unique('id')->pluck('nombre');

        return response()->json([
            'usuario_id' => $usuario->id,
            'permisos' => $permisos,
        ]);
    }
}
