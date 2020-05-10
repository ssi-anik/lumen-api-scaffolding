<?php

namespace App\Http\Middleware;

use Closure;

class ExampleMiddleware
{
    public function handle ($request, Closure $next) {
        return $next($request);
    }
}
