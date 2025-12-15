<?php

namespace App\Traits;

use Illuminate\Support\Facades\Auth;

trait HasPageRoleBasedAccess
{
    /**
     * Kiểm tra xem user hiện tại có thể truy cập page này không
     */
    public static function canAccess(): bool
    {
        $user = Auth::user();
        
        if (!$user) {
            return false;
        }

        // Admin có quyền truy cập tất cả
        if ($user->isAdmin()) {
            return true;
        }

        // Post Manager không có quyền truy cập các pages quản trị
        if ($user->isPostManager()) {
            return false;
        }

        return false;
    }

    /**
     * Ẩn page khỏi navigation nếu user không có quyền
     */
    public static function shouldRegisterNavigation(): bool
    {
        return static::canAccess();
    }
}
