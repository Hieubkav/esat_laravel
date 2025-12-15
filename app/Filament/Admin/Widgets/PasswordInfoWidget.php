<?php

namespace App\Filament\Admin\Widgets;

use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class PasswordInfoWidget extends Widget
{
    protected static string $view = 'filament.admin.widgets.password-info-widget';

    protected int | string | array $columnSpan = 'full';

    protected static ?int $sort = 2;

    public function getViewData(): array
    {
        $user = Auth::user();
        
        // Chỉ hiển thị cho quản trị viên
        if (!$user || $user->role !== 'admin') {
            return ['users' => collect(), 'isAdmin' => false];
        }

        $users = User::orderBy('role')->orderBy('name')->get();
        
        return [
            'users' => $users,
            'isAdmin' => true,
        ];
    }

    public static function canView(): bool
    {
        $user = Auth::user();
        return $user && $user->role === 'admin';
    }
}
