<?php

namespace App\Http\Middleware;

use Closure;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Exceptions\JWTException;

class OptionalAuth
{
    public function handle($request, Closure $next)
    {
        try {
            if ($token = JWTAuth::getToken()) {
                JWTAuth::setToken($token);
                Auth::setUser(JWTAuth::authenticate());
            }
        } catch (JWTException $e) {
        }

        return $next($request);
    }
}
