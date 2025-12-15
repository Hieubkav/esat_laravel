<?php

namespace App\Livewire\Public;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\On;

class UserAccount extends Component
{
    public $isLoggedIn = false;
    public $user = null;

    public function mount()
    {
        $this->checkAuthStatus();
    }

    #[On('customer-logged-in')]
    #[On('customer-registered')]
    #[On('customer-logged-out')]
    #[On('customer-password-created')]
    public function checkAuthStatus()
    {
        $this->isLoggedIn = Auth::guard('mshopkeeper_customer')->check();
        $this->user = Auth::guard('mshopkeeper_customer')->user();

        // Force re-render để đảm bảo UI cập nhật
        $this->dispatch('auth-status-updated', [
            'isLoggedIn' => $this->isLoggedIn,
            'user' => $this->user ? [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'tel' => $this->user->tel,
                'email' => $this->user->email
            ] : null
        ]);
    }

    public function logout()
    {
        // Set logout flag to prevent network error dialogs
        $this->js('
            console.log("Setting logout flag");
            window.isLoggingOut = true;
            console.log("Logout flag set:", window.isLoggingOut);
        ');

        try {
            // Chỉ logout guard mshopkeeper_customer, không invalidate toàn bộ session
            Auth::guard('mshopkeeper_customer')->logout();

            // Chỉ regenerate CSRF token, không invalidate session để không ảnh hưởng admin
            session()->regenerateToken();

            $this->checkAuthStatus();
            $this->dispatch('customer-logged-out');

            // Stay on current page instead of redirecting to home
            $this->js('
                setTimeout(() => {
                    console.log("Refreshing current page after logout");
                    window.location.reload();
                }, 100);
            ');

        } catch (\Exception $e) {
            Log::error('Livewire logout error', ['error' => $e->getMessage()]);

            $this->js('
                console.log("Logout error, refreshing current page anyway");
                window.location.reload();
            ');
        }
    }





    public function render()
    {
        return view('livewire.public.user-account');
    }
}
