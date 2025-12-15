<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Cập nhật role cho user admin
        User::where('email', 'admin@vuphucbaking.com')
            ->update(['role' => 'admin']);

        // Cập nhật role cho user editor thành post_manager
        User::where('email', 'editor@vuphucbaking.com')
            ->update(['role' => 'post_manager']);

        // Tạo thêm một user post_manager mẫu nếu chưa có
        if (!User::where('email', 'postmanager@vuphucbaking.com')->exists()) {
            User::create([
                'name' => 'Quản lý bài viết',
                'email' => 'postmanager@vuphucbaking.com',
                'password' => 'password',
                'role' => 'post_manager',
                'order' => 3,
                'status' => 'active',
            ]);
        }

        $this->command->info('Đã cập nhật role cho các user thành công!');
        $this->command->info('Quản trị viên: admin@vuphucbaking.com (role: admin)');
        $this->command->info('Quản lý bài viết: editor@vuphucbaking.com (role: post_manager)');
        $this->command->info('Quản lý bài viết: postmanager@vuphucbaking.com (role: post_manager)');
    }
}
