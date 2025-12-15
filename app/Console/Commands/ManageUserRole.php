<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class ManageUserRole extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'user:role {email} {role}';

    /**
     * The console command description.
     */
    protected $description = 'Thay đổi role của user theo email';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        $role = $this->argument('role');

        // Validate role
        if (!in_array($role, ['admin', 'post_manager'])) {
            $this->error('Role không hợp lệ. Chỉ chấp nhận: admin, post_manager');
            return 1;
        }

        // Find user
        $user = User::where('email', $email)->first();

        if (!$user) {
            $this->error("Không tìm thấy user với email: {$email}");
            return 1;
        }

        // Update role
        $oldRole = $user->role;
        $user->role = $role;
        $user->save();

        $this->info("Đã cập nhật role cho user {$user->name} ({$email})");
        $this->info("Role cũ: {$oldRole}");
        $this->info("Role mới: {$role}");

        return 0;
    }
}
