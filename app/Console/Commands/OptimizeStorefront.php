<?php

namespace App\Console\Commands;

use App\Providers\ViewServiceProvider;
use App\Services\PerformanceService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Artisan;

class OptimizeStorefront extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'storefront:optimize {--clear : Clear all caches} {--warm : Warm up caches}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Optimize storefront performance by managing caches';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $clear = $this->option('clear');
        $warm = $this->option('warm');

        if ($clear) {
            $this->clearCaches();
        }

        if ($warm) {
            $this->warmUpCaches();
        }

        if (!$clear && !$warm) {
            $this->info('Available options:');
            $this->line('  --clear  Clear all storefront caches');
            $this->line('  --warm   Warm up caches with fresh data');
            $this->line('');
            $this->line('Example: php artisan storefront:optimize --clear --warm');
        }

        return 0;
    }

    /**
     * Clear all caches
     */
    private function clearCaches(): void
    {
        $this->info('🧹 Clearing caches...');

        // Clear application cache
        Artisan::call('cache:clear');
        $this->line('✅ Application cache cleared');

        // Clear view cache
        Artisan::call('view:clear');
        $this->line('✅ View cache cleared');

        // Clear config cache
        Artisan::call('config:clear');
        $this->line('✅ Config cache cleared');

        // Clear route cache
        Artisan::call('route:clear');
        $this->line('✅ Route cache cleared');

        // Clear storefront specific caches
        $cacheKeys = [
            'storefront_sliders',
            'storefront_categories',
            'storefront_products',
            'storefront_services',
            'storefront_news',
            'storefront_partners',
            'navigation_data',
            'global_settings'
        ];

        foreach ($cacheKeys as $key) {
            Cache::forget($key);
        }
        $this->line('✅ Storefront caches cleared');

        $this->info('🎉 All caches cleared successfully!');
    }

    /**
     * Warm up caches
     */
    private function warmUpCaches(): void
    {
        $this->info('🔥 Warming up caches...');

        // Cache config
        Artisan::call('config:cache');
        $this->line('✅ Config cached');

        // Cache routes
        Artisan::call('route:cache');
        $this->line('✅ Routes cached');

        // Cache views
        Artisan::call('view:cache');
        $this->line('✅ Views cached');

        // Warm up storefront data by accessing homepage
        try {
            $this->info('🌐 Warming up storefront data...');

            // Simulate accessing homepage to trigger cache warming
            $this->warmUpStorefrontData();

            $this->line('✅ Storefront data cached');
        } catch (\Exception $e) {
            $this->error('❌ Failed to warm up storefront data: ' . $e->getMessage());
        }

        $this->info('🎉 Cache warming completed!');
    }

    /**
     * Warm up storefront specific data
     */
    private function warmUpStorefrontData(): void
    {
        // Warm up settings
        Cache::remember('global_settings', 3600, function () {
            return \App\Models\Setting::first();
        });

        // Warm up sliders
        Cache::remember('storefront_sliders', 3600, function () {
            return \App\Models\Slider::where('status', 'active')
                ->orderBy('order')
                ->select(['id', 'title', 'description', 'image_link', 'link', 'order'])
                ->get();
        });

        // Warm up categories
        Cache::remember('storefront_categories', 7200, function () {
            return \App\Models\CatProduct::where('status', 'active')
                ->whereNull('parent_id')
                ->orderBy('order')
                ->select(['id', 'name', 'slug', 'image', 'order'])
                ->take(12)
                ->get();
        });

        // Warm up featured products
        Cache::remember('storefront_products', 1800, function () {
            return \App\Models\Product::where('status', 'active')
                ->where('is_hot', true)
                ->with(['category:id,name', 'images' => function($query) {
                    $query->where('status', 'active')->orderBy('order')->take(1);
                }])
                ->select(['id', 'name', 'slug', 'price', 'compare_price', 'is_hot', 'category_id', 'seo_title', 'order'])
                ->orderBy('order')
                ->take(8)
                ->get();
        });

        // Warm up services
        Cache::remember('storefront_services', 3600, function () {
            return \App\Models\Post::where('status', 'active')
                ->where('type', 'service')
                ->with(['category:id,name', 'images' => function($query) {
                    $query->where('status', 'active')->orderBy('order')->take(1);
                }])
                ->select(['id', 'title', 'slug', 'seo_description', 'thumbnail', 'category_id', 'order'])
                ->orderBy('order')
                ->get();
        });

        // Warm up news
        Cache::remember('storefront_news', 1800, function () {
            return \App\Models\Post::where('status', 'active')
                ->where('type', 'news')
                ->with(['category:id,name', 'images' => function($query) {
                    $query->where('status', 'active')->orderBy('order')->take(1);
                }])
                ->select(['id', 'title', 'slug', 'seo_description', 'thumbnail', 'category_id', 'order', 'created_at'])
                ->orderBy('order')
                ->orderBy('created_at', 'desc')
                ->take(6)
                ->get();
        });

        // Warm up partners
        Cache::remember('storefront_partners', 7200, function () {
            return \App\Models\Partner::where('status', 'active')
                ->select(['id', 'name', 'logo_link', 'website_link', 'description', 'order'])
                ->orderBy('order')
                ->get();
        });

        // Warm up navigation data
        Cache::remember('navigation_data', 7200, function () {
            return [
                'mainCategories' => \App\Models\CatProduct::where('status', 'active')
                    ->whereNull('parent_id')
                    ->with(['children' => function ($query) {
                        $query->where('status', 'active')->orderBy('order');
                    }])
                    ->orderBy('order')
                    ->get(),
                'menuItems' => \App\Models\MenuItem::where('status', 'active')
                    ->whereNull('parent_id')
                    ->with(['children' => function ($query) {
                        $query->where('status', 'active')->orderBy('order');
                    }])
                    ->orderBy('order')
                    ->get(),
            ];
        });
    }
}
