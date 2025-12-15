<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use Livewire\Livewire;
use App\Models\WebDesign;
use App\Observers\WebDesignObserver;
use App\Models\User;
use App\Observers\UserObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Đăng ký Observer cho WebDesign
        WebDesign::observe(WebDesignObserver::class);

        // Đăng ký Observer cho User
        User::observe(UserObserver::class);
    }
}
