<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class SimpleResourceAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        // Tạm thời cho phép tất cả để test
        return $next($request);
    }
}
