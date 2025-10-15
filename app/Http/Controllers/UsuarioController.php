<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
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
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'documento' => 'nullable|string|max:50',
            'email' => 'required|email|unique:usuarios,email',
            'telefono' => 'nullable|string|max:50',
            'password' => 'sometimes|string|min:6',
            'contraseña' => 'sometimes|string|min:6',
            'rol_id' => 'nullable|integer|exists:roles,id',
            'imagen' => 'nullable|image|max:2048',
        ]);

        $path = null;
        if ($request->hasFile('imagen')) {
            $path = $request->file('imagen')->store('usuarios', 'public');
        }

        $plainPassword = $request->input('password', $request->input('contraseña'));

        $usuario = Usuario::create([
            'nombre' => $validated['nombre'],
            'documento' => $validated['documento'] ?? null,
            'email' => $validated['email'],
            'telefono' => $validated['telefono'] ?? null,
            'contraseña' => Hash::make($plainPassword),
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
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'documento' => 'nullable|string|max:50',
            'email' => 'required|email|unique:usuarios,email',
            'telefono' => 'nullable|string|max:50',
            'rol_id' => 'nullable|integer|exists:roles,id',
            'imagen' => 'nullable|image|max:2048',
        ]);

        $path = null;
        if ($request->hasFile('imagen')) {
            $path = $request->file('imagen')->store('usuarios', 'public');
        }

        $usuario = Usuario::create([
            'nombre' => $validated['nombre'],
            'documento' => $validated['documento'] ?? null,
            'email' => $validated['email'],
            'telefono' => $validated['telefono'] ?? null,
            'contraseña' => Hash::make('ligadeajedrez'), // Contraseña por defecto para admin
            'rol_id' => $validated['rol_id'] ?? null,
            'estado' => true,
            'imagen_path' => $path,
        ]);

        return response()->json([
            'message' => 'Usuario creado exitosamente',
            'usuario' => $usuario
        ], 201);
    }

    public function update(Request $request, $id)
    {
        log::info("Request: " . json_encode($request->all()));
        $usuario = Usuario::find($id);
        if (!$usuario) {
            return response()->json(['message' => 'Usuario no encontrado'], 404);
        }

        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'documento' => 'nullable|string|max:50',
            'email' => 'required|email|unique:usuarios,email,' . $id,
            'telefono' => 'nullable|string|max:50',
            'password' => 'sometimes|string|min:6',
            'contraseña' => 'sometimes|string|min:6',
            'rol_id' => 'nullable|integer|exists:roles,id',
            'imagen' => 'nullable|image|max:2048',
        ]);

        if ($request->imagen_base64) {
            $imageData = $request->imagen_base64;
            $image = str_replace('data:image/png;base64,', '', $imageData);
            $image = str_replace('data:image/jpeg;base64,', '', $image);
            $image = str_replace(' ', '+', $image);

            $imageName = 'usuarios/' . uniqid() . '.png';
            \Storage::disk('public')->put($imageName, base64_decode($image));

            $usuario->imagen_path = $imageName;
        }

        $plainPassword = $request->input('password', $request->input('contraseña'));

        $usuario->update([
            'nombre' => $validated['nombre'],
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
}
