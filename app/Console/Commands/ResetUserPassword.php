<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class ResetUserPassword extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'user:reset-password {email} {password}';

    /**
     * The console command description.
     */
    protected $description = 'Đặt lại mật khẩu cho user theo email';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        $password = $this->argument('password');

        // Find user
        $user = User::where('email', $email)->first();

        if (!$user) {
            $this->error("Không tìm thấy user với email: {$email}");
            return 1;
        }

        // Update password
        $user->password = Hash::make($password);
        $user->plain_password = $password;
        $user->save();

        $this->info("Đã đặt lại mật khẩu cho user {$user->name} ({$email})");
        $this->info("Mật khẩu mới: {$password}");

        return 0;
    }
}
