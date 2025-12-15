<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class ListUserRoles extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'user:list-roles';

    /**
     * The console command description.
     */
    protected $description = 'Liệt kê tất cả users và role của họ';

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

        $this->info('Danh sách users và role:');
        $this->line('');

        $headers = ['ID', 'Tên', 'Email', 'Role', 'Trạng thái', 'Ngày tạo'];
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
                $user->status === 'active' ? '✅ Hoạt động' : '❌ Không hoạt động',
                $user->created_at->format('d/m/Y H:i'),
            ];
        }

        $this->table($headers, $rows);

        $this->line('');
        $this->info('Tổng số users: ' . $users->count());
        $this->info('Quản trị viên: ' . $users->where('role', 'admin')->count());
        $this->info('Quản lý bài viết: ' . $users->where('role', 'post_manager')->count());

        return 0;
    }
}
