<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            // aceptamos tanto "password" como "contraseña" en el body
            'password' => 'sometimes|string',
            'contraseña' => 'sometimes|string'
        ]);

        $credentials = $request->only('email', 'password');

        if (!$token = auth('api')->attempt($credentials)) {
            return response()->json([
                'error' => 'Credenciales incorrectas',
                'message' => 'Por favor, verifica tu email y contraseña'
            ], 401);
        }
        $user = auth('api')->user();

        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60,
            'user' => [
                'id'     => $user->id,
                'nombre' => $user->nombre ?? $user->name, // depende de tu columna
                'email'  => $user->email
            ]
        ]);
    }

    public function logout()
    {
        auth()->logout();
        return response()->json(['message' => 'Sesión cerrada']);
    }

    public function profile()
    {
        return response()->json(auth()->user());
    }
}
