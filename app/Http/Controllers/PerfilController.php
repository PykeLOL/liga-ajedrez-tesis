<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class PerfilController extends Controller
{
    public function index()
    {
        $user = auth()->user()->load('rol', 'permisos');
        return response()->json($user);
    }

    public function update(Request $request)
    {
        $usuario = auth()->user();
        $validator = Validator::make($request->all(), [
            'nombre' => 'sometimes|string|max:255',
            'apellido' => 'sometimes|string|max:255',
            'documento' => 'nullable|string|max:50',
            'email' => 'sometimes|email|unique:usuarios,email,' . $usuario->id,
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

        if ($request->imagen_base64) {
            $imageData = $request->imagen_base64;
            $image = str_replace('data:image/png;base64,', '', $imageData);
            $image = str_replace('data:image/jpeg;base64,', '', $image);
            $image = str_replace(' ', '+', $image);

            $imageName = 'usuarios/' . uniqid() . '.png';
            \Storage::disk('public')->put($imageName, base64_decode($image));

            $usuario->imagen_path = $imageName;
        }
        $usuario->update([
            'nombre' => $validated['nombre'] ?? $usuario->nombre,
            'apellido' => $validated['apellido'] ?? $usuario->apellido,
            'documento' => $validated['documento'] ?? $usuario->documento,
            'email' => $validated['email'] ?? $usuario->email,
            'telefono' => $validated['telefono'] ?? $usuario->telefono,
            'rol_id' => $validated['rol_id'] ?? $usuario->rol_id,
            'imagen_path' => $usuario->imagen_path,
        ]);

        return response()->json(['message' => 'Perfil actualizado con éxito', 'user' => $usuario]);
    }

    public function eliminarFoto()
    {
        $user = auth()->user();

        if ($user->imagen_path && Storage::exists($user->imagen_path)) {
            Storage::delete($user->imagen_path);
        }

        $user->imagen_path = null;
        $user->save();

        return response()->json(['message' => 'Foto eliminada correctamente']);
    }

    public function cambiarContrasena(Request $request)
    {
        $request->validate([
            'actual' => 'required',
            'nueva' => 'required|min:4',
        ]);

        $user = auth()->user();

        if (!Hash::check($request->actual, $user->contraseña)) {
            return response()->json(['message' => 'La contraseña actual es incorrecta'], 423);
        }

        if (Hash::check($request->nueva, $user->contraseña)) {
            return response()->json(['message' => 'La nueva contraseña no puede ser igual a la actual'], 422);
        }

        $user->update(['contraseña' => Hash::make($request->nueva)]);

        return response()->json(['message' => 'Contraseña actualizada correctamente']);
    }

    public function misPermisos()
    {
        $usuario = auth()->user();

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

}
