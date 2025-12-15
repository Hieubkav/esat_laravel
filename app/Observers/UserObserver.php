<?php

namespace App\Observers;

use App\Models\User;

class UserObserver
{
    /**
     * Handle the User "creating" event.
     */
    public function creating(User $user): void
    {
        // Nếu có plain_password nhưng chưa có password, hash plain_password
        if ($user->plain_password && !$user->password) {
            $user->password = bcrypt($user->plain_password);
        }
        
        // Nếu chưa có plain_password, set mặc định
        if (!$user->plain_password) {
            $user->plain_password = 'password';
        }
    }

    /**
     * Handle the User "updating" event.
     */
    public function updating(User $user): void
    {
        // Nếu plain_password thay đổi, cập nhật password
        if ($user->isDirty('plain_password') && $user->plain_password) {
            $user->password = bcrypt($user->plain_password);
        }
    }
}
