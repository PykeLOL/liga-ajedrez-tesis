<?php

namespace App\Http\Middleware;

use Closure;

class JwtFromCookie
{
    public function handle($request, Closure $next)
    {
        if ($request->cookies->has('auth_token')) { // <- aquí
            $token = $request->cookie('auth_token'); // <- aquí también
            $request->headers->set('Authorization', 'Bearer ' . $token);
        }
        return $next($request);
    }
}
