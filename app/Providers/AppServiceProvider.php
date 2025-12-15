<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use Livewire\Livewire;
use App\Models\WebDesign;
use App\Observers\WebDesignObserver;
use App\Models\User;
use App\Observers\UserObserver;
use App\Models\MShopKeeperInventoryItem;
use App\Observers\MShopKeeperInventoryItemObserver;


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

        // Đăng ký Observer cho MShopKeeperInventoryItem
        MShopKeeperInventoryItem::observe(MShopKeeperInventoryItemObserver::class);

        // Đăng ký Livewire components một cách rõ ràng để tránh lỗi ở production
        $this->registerLivewireComponents();

    //    Livewire::setScriptRoute(function ($handle) {
    //         return Route::get('/vuphuc/livewire/livewire.min.js?id=13b7c601', $handle);
    //     });

    //     Livewire::setUpdateRoute(function ($handle) {
    //         return Route::post('/vuphuc/livewire/update', $handle);
    //     });

    }

    /**
     * Đăng ký Livewire components một cách rõ ràng
     */
    private function registerLivewireComponents(): void
    {
        // Đăng ký component MShopKeeperInventoryFilter với tên rõ ràng
        Livewire::component('mshopkeeper-inventory-filter', \App\Livewire\MShopKeeperInventoryFilter::class);
    }
}
