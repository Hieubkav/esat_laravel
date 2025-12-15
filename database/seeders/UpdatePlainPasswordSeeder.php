<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UpdatePlainPasswordSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Cập nhật plain_password cho tất cả users hiện có
        $users = User::all();
        
        foreach ($users as $user) {
            // Nếu chưa có plain_password, set mặc định là 'password'
            if (empty($user->plain_password)) {
                $user->plain_password = 'password';
                $user->save();
            }
        }

        $this->command->info('Đã cập nhật plain_password cho ' . $users->count() . ' users');
        
        // Hiển thị thông tin users
        foreach ($users as $user) {
            $this->command->info("User: {$user->name} ({$user->email}) - Password: {$user->plain_password}");
        }
    }
}
