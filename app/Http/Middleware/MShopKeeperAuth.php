<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class MShopKeeperAuth
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::guard('mshopkeeper_customer')->check()) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthorized'], 401);
            }

            // Redirect về trang chủ với popup đăng nhập thay vì trang đăng nhập riêng
            return redirect('/')->with([
                'error' => 'Vui lòng đăng nhập để tiếp tục.',
                'show_login_popup' => true
            ]);
        }

        return $next($request);
    }
}
