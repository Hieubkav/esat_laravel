<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Model;

class FilamentPerformanceServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Tối ưu hóa Eloquent queries
        $this->optimizeEloquentQueries();
        
        // Tối ưu hóa Filament performance
        $this->optimizeFilamentPerformance();
    }

    /**
     * Tối ưu hóa Eloquent queries
     */
    protected function optimizeEloquentQueries(): void
    {
        // Ngăn chặn lazy loading để tránh N+1 queries
        Model::preventLazyLoading(!app()->isProduction());
        
        // Ngăn chặn silently discarding attributes
        Model::preventSilentlyDiscardingAttributes(!app()->isProduction());
        
        // Ngăn chặn accessing missing attributes
        Model::preventAccessingMissingAttributes(!app()->isProduction());
    }

    /**
     * Tối ưu hóa Filament performance
     */
    protected function optimizeFilamentPerformance(): void
    {
        // Cấu hình cache cho navigation badges
        if (config('filament-performance.cache.enable_query_cache', true)) {
            $this->enableNavigationBadgeCache();
        }
    }

    /**
     * Enable cache cho navigation badges
     */
    protected function enableNavigationBadgeCache(): void
    {
        // Có thể implement cache logic ở đây nếu cần
        // Hiện tại để trống vì Filament đã có cơ chế cache riêng
    }
}
