<?php

namespace App\Traits;

use Illuminate\Support\Facades\Auth;

trait HasRoleBasedAccess
{
    /**
     * Kiểm tra xem user hiện tại có thể truy cập resource này không
     */
    public static function canAccess(): bool
    {
        $user = Auth::user();

        if (!$user) {
            return false;
        }

        // Lấy tên class của resource hiện tại - xử lý trường hợp null
        $staticClass = static::class;
        if ($staticClass === null) {
            return false;
        }

        $resourceClass = class_basename($staticClass);

        return $user->canAccessResource($resourceClass);
    }

    /**
     * Kiểm tra xem có thể tạo record mới không
     */
    public static function canCreate(): bool
    {
        return static::canAccess();
    }

    /**
     * Kiểm tra xem có thể chỉnh sửa record không
     */
    public static function canEdit($record): bool
    {
        return static::canAccess();
    }

    /**
     * Kiểm tra xem có thể xóa record không
     */
    public static function canDelete($record): bool
    {
        return static::canAccess();
    }

    /**
     * Kiểm tra xem có thể xem record không
     */
    public static function canView($record): bool
    {
        return static::canAccess();
    }

    /**
     * Ẩn resource khỏi navigation nếu user không có quyền
     */
    public static function shouldRegisterNavigation(): bool
    {
        return static::canAccess();
    }
}
