<?php

namespace App\Filament\Admin\Widgets;

use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;

class UserRoleWidget extends Widget
{
    protected static string $view = 'filament.admin.widgets.user-role-widget';

    protected int | string | array $columnSpan = 'full';

    protected static ?int $sort = 1;

    public function getViewData(): array
    {
        $user = Auth::user();
        
        return [
            'user' => $user,
            'roleName' => match($user->role) {
                'admin' => 'Quản trị viên',
                'post_manager' => 'Quản lý bài viết',
                default => $user->role,
            },
            'permissions' => $this->getUserPermissions($user),
        ];
    }

    private function getUserPermissions($user): array
    {
        if ($user->isAdmin()) {
            return [
                'Quản lý tất cả các chức năng trong hệ thống',
                'Tạo, sửa, xóa tất cả các loại nội dung',
                'Quản lý người dùng và phân quyền',
                'Cấu hình hệ thống',
            ];
        }

        if ($user->isPostManager()) {
            return [
                'Quản lý bài viết (tạo, sửa, xóa)',
                'Quản lý chuyên mục bài viết',
                'Xem dashboard và thống kê cơ bản',
            ];
        }

        return ['Không có quyền đặc biệt'];
    }
}
