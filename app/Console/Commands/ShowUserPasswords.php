<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class ShowUserPasswords extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'user:show-passwords';

    /**
     * The console command description.
     */
    protected $description = 'Hiển thị mật khẩu của tất cả users (chỉ dành cho quản trị viên)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $users = User::orderBy('role')->orderBy('name')->get();

        if ($users->isEmpty()) {
            $this->info('Không có user nào trong hệ thống.');
            return 0;
        }

        $this->info('Danh sách users và mật khẩu:');
        $this->line('');

        $headers = ['ID', 'Tên', 'Email', 'Role', 'Mật khẩu', 'Trạng thái'];
        $rows = [];

        foreach ($users as $user) {
            $roleName = match($user->role) {
                'admin' => 'Quản trị viên',
                'post_manager' => 'Quản lý bài viết',
                default => $user->role,
            };

            $rows[] = [
                $user->id,
                $user->name,
                $user->email,
                $roleName,
                $user->plain_password ?: 'password',
                $user->status === 'active' ? '✅ Hoạt động' : '❌ Không hoạt động',
            ];
        }

        $this->table($headers, $rows);

        $this->line('');
        $this->warn('⚠️  CẢNH BÁO: Thông tin mật khẩu rất nhạy cảm. Vui lòng bảo mật!');
        $this->info('💡 Sử dụng: php artisan user:reset-password email newpassword để đổi mật khẩu');

        return 0;
    }
}
