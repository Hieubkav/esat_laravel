<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckFilamentResourceAccess
{
    /**
     * Handle an incoming request - Phiên bản đơn giản và an toàn
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            // Kiểm tra đăng nhập
            if (!Auth::check()) {
                return redirect('/admin/login');
            }

            $user = Auth::user();
            if (!$user) {
                return redirect('/admin/login');
            }

            // Admin có quyền truy cập tất cả
            if (isset($user->role) && $user->role === 'admin') {
                return $next($request);
            }

            // Kiểm tra Post Manager
            if (isset($user->role) && $user->role === 'post_manager') {
                return $this->checkPostManagerAccess($request, $next);
            }

            // Mặc định cho phép truy cập
            return $next($request);

        } catch (\Throwable $e) {
            // Log lỗi và redirect
            error_log('CheckFilamentResourceAccess error: ' . $e->getMessage());
            return redirect('/admin/login');
        }
    }

    /**
     * Kiểm tra quyền truy cập cho Post Manager
     */
    private function checkPostManagerAccess(Request $request, Closure $next): Response
    {
        try {
            $path = $request->path();

            // Danh sách paths bị cấm cho Post Manager
            $forbiddenPaths = [
                'admin/manage-settings',
                'admin/manage-web-design',
                'admin/visitor-analytics',
                'admin/products',
                'admin/product-categories',
                'admin/users',
                'admin/customers',
                'admin/employees',
                'admin/orders',
                'admin/carts',
                'admin/sliders',
                'admin/menu-items',
                'admin/partners',
                'admin/associations',
            ];

            // Kiểm tra paths bị cấm
            foreach ($forbiddenPaths as $forbiddenPath) {
                if (strpos($path, $forbiddenPath) === 0) {
                    abort(403, 'Bạn không có quyền truy cập trang này.');
                }
            }

            return $next($request);

        } catch (\Throwable $e) {
            error_log('checkPostManagerAccess error: ' . $e->getMessage());
            return redirect('/admin/login');
        }
    }


}
