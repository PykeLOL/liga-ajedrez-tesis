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

    public function refresh()
    {
        try {
            // Intenta refrescar el token.
            // La librería tomará el token expirado de la cabecera Authorization.
            // Si JWT_BLACKLIST_ENABLED=true, invalidará el token viejo.
            $newToken = auth('api')->refresh();

            return response()->json([
                'access_token' => $newToken,
                'token_type'   => 'bearer',
                'expires_in'   => auth('api')->factory()->getTTL() * 60
            ]);

        } catch (TokenInvalidException $e) {
            // El token es inválido (malformado, etc.)
            return response()->json(['error' => 'Token inválido.'], 401);
        } catch (TokenBlacklistedException $e) {
            // El token ya está en la lista negra (ej. después de un logout)
            return response()->json(['error' => 'Token en lista negra.'], 401);
        } catch (\Exception $e) {
            // Otra excepción, como TokenExpiredException (si pasó el refresh_ttl)
            return response()->json(['error' => 'Token no válido o expirado. Por favor, inicie sesión de nuevo.'], 401);
        }
    }
}
