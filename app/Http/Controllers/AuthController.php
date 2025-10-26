<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email',
                'password' => 'required|string',
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Credenciales de validación inválidas.',
                'errors' => $e->errors(),
            ], 422);
        }

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
            ],
            'permisos' => $user->getAllPermisos()->pluck('nombre'),
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

    public function refresh()
    {
        try {
            $newToken = auth('api')->refresh();

            return response()->json([
                'access_token' => $newToken,
                'token_type'   => 'bearer',
                'expires_in'   => auth('api')->factory()->getTTL() * 60
            ]);

        } catch (TokenInvalidException $e) {
            return response()->json(['error' => 'Token inválido.'], 401);
        } catch (TokenBlacklistedException $e) {
            return response()->json(['error' => 'Token en lista negra.'], 401);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Token no válido o expirado. Por favor, inicie sesión de nuevo.'], 401);
        }
    }
}
