<?php

namespace App\Http\Middleware;

use Closure;

class SecretKey
{
    public function handle($request, Closure $next, $guard = null)
    {
        if ($request->secretKey == env('SECRET_KEY')) {
            return $next($request);
        }
        return abort('404');
    }
}
