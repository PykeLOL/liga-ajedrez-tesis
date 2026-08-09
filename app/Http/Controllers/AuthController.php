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

        $cookie = cookie(
            'auth_token',
            $token,
            auth('api')->factory()->getTTL(),
            '/',
            null,
            false,
            true,
            false,
            'Lax'
        );

        return response()->json([
            'message' => 'Inicio de sesión exitoso',
            'user' => [
                'id'     => $user->id,
                'nombre' => $user->nombre . ' ' . $user->apellido,
                'email'  => $user->email,
                'rol'    => $user->rol ? $user->rol->nombre : 'Sin rol',
                'imagen_path' => $user->imagen_path,
            ],
            'permisos' => $user->getAllPermisos()->pluck('nombre'),
        ])->withCookie($cookie);
    }

    public function logout()
    {
        auth('api')->logout();
        $forget = cookie('auth_token', null, -1, '/', null, false, true, false, 'Lax');
        return response()->json(['message' => 'Sesión cerrada'])->withCookie($forget);
    }

    public function profile()
    {
        return response()->json(auth()->user());
    }

    public function refresh()
    {
        try {
            $newToken = auth('api')->refresh();
            $cookie = cookie(
                'auth_token',
                $newToken,
                auth('api')->factory()->getTTL(),
                '/',
                null,
                false,
                true,
                false,
                'Lax'
            );

            return response()->json([
                'message' => 'Sesión renovada correctamente'
            ])->withCookie($cookie);

        } catch (TokenInvalidException $e) {
            return response()->json(['error' => 'Token inválido.'], 401);
        } catch (TokenBlacklistedException $e) {
            return response()->json(['error' => 'Token en lista negra.'], 401);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Token no válido o expirado. Por favor, inicie sesión de nuevo.'], 401);
        }
    }

    public function me()
    {
        try {
            $user = auth('api')->user();

            if (!$user) {
                return response()->json(['message' => 'No autenticado'], 401);
            }

            return response()->json([
                'user' => [
                    'id'     => $user->id,
                    'nombre' => $user->nombre ?? $user->name,
                    'email'  => $user->email,
                    'rol'    => $user->rol ? $user->rol->nombre : 'Sin rol',
                    'imagen_path' => $user->imagen_path,
                    'google_id' => $user->google_id,
                    // 'google_email' => $user->google_email,
                ],
                'permisos' => $user->getAllPermisos()->pluck('nombre'),
            ]);

        } catch (\Exception $e) {
            return response()->json(['message' => 'Sesión no válida'], 401);
        }
    }
}
