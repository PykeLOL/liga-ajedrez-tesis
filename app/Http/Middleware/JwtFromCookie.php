<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Log;
use Tymon\JWTAuth\Facades\JWTAuth;
use Symfony\Component\HttpFoundation\Cookie;

class JwtFromCookie
{
    public function handle($request, Closure $next)
    {
        if (!$request->cookies->has('auth_token')) {
            return $next($request);
        }

        $token = $request->cookie('auth_token');
        $request->headers->set('Authorization', 'Bearer ' . $token);

        try {
            $payload = JWTAuth::setToken($token)->getPayload();
            $exp = $payload->get('exp');
            $now = time();
            $remaining = $exp - $now;
            $refreshThreshold = 5 * 60;
            $response = $next($request);

            if ($remaining > 0 && $remaining <= $refreshThreshold) {
                $newToken = JWTAuth::refresh($token);
                return $response->withCookie(
                    cookie(
                        'auth_token',
                        $newToken,
                        auth('api')->factory()->getTTL(), // minutos
                        '/',
                        null,
                        false,
                        true,
                        false,
                        'Lax'
                    )
                );
            }
            return $response;
        } catch (\Exception $e) {
            Log::warning('JWT inválido en middleware: ' . $e->getMessage());
            return $next($request);
        }
    }
}
