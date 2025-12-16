<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\Setting;
use App\Models\CatProduct;
use App\Models\Product;
use App\Models\Post;
use App\Models\Slider;
use App\Models\Partner;
use App\Models\MenuItem;
use App\Models\Association;
use Illuminate\Support\Facades\Cache;

class ViewServiceProvider extends ServiceProvider
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
        // Share data với tất cả views
        View::composer('*', function ($view) {
            $this->shareGlobalData($view);
        });

        // Share data cho storefront views
        View::composer([
            'shop.storeFront',
            'components.storefront.*',
            'storefront.products.*',
            'storefront.posts.*'
        ], function ($view) {
            $this->shareStorefrontData($view);
        });

        // Share data cho layout views
        View::composer([
            'layouts.app',
            'layouts.shop',
            'components.public.*'
        ], function ($view) {
            $this->shareLayoutData($view);
        });
    }

    /**
     * Share global data với tất cả views
     */
    private function shareGlobalData($view)
    {
        // Cache settings trong 1 giờ
        $settings = Cache::remember('global_settings', 3600, function () {
            return Setting::where('status', 'active')->first() ?? new Setting([
                'site_name' => config('app.name'),
                'seo_title' => config('app.name'),
                'hotline' => '1900636340',
                'email' => 'info@esat.vn',
                'status' => 'active'
            ]);
        });

        $view->with([
            'globalSettings' => $settings,
            'settings' => $settings
        ]);
    }

    /**
     * Share data cho storefront views - Tối ưu performance
     */
    private function shareStorefrontData($view)
    {
        $storefrontData = [
            // Hero Banner - Cache 1 giờ
            'sliders' => Cache::remember('storefront_sliders', 3600, function () {
                return Slider::where('status', 'active')
                    ->orderBy('order')
                    ->select(['id', 'title', 'description', 'image_link', 'link', 'alt_text', 'order', 'status'])
                    ->get();
            }),

            // Categories data - Cache 2 giờ
            'categories' => Cache::remember('storefront_categories', 7200, function () {
                return CatProduct::where('status', 'active')
                    ->orderBy('order')
                    ->select(['id', 'name', 'slug', 'image', 'order'])
                    ->take(12)
                    ->get();
            }),

            // Featured Products - Cache 5 phút
            'featuredProducts' => Cache::remember('storefront_products', 300, function () {
                return Product::where('status', 'active')
                    ->where('is_hot', true)
                    ->with(['category', 'images' => function($query) {
                        $query->where('status', 'active')->orderBy('order')->take(1);
                    }])
                    ->select(['id', 'name', 'slug', 'price', 'description', 'category_id', 'is_hot'])
                    ->orderBy('price', 'desc')
                    ->take(8)
                    ->get();
            }),

            // Featured Posts - Cache 30 phút (thay thế services, news, courses)
            'featuredPosts' => Cache::remember('storefront_featured_posts', 1800, function () {
                return Post::where('status', 'active')
                    ->where('is_featured', true)
                    ->with(['categories:id,name', 'images' => function($query) {
                        $query->where('status', 'active')->orderBy('order')->take(1);
                    }])
                    ->select(['id', 'title', 'slug', 'seo_description', 'thumbnail', 'order', 'created_at'])
                    ->orderBy('order')
                    ->orderBy('created_at', 'desc')
                    ->take(6)
                    ->get();
            }),

            // Backward compatibility - return empty collections
            'services' => collect([]),
            'newsPosts' => collect([]),
            'courses' => collect([]),

            // Partners - Cache 2 giờ
            'partners' => Cache::remember('storefront_partners', 7200, function () {
                return Partner::where('status', 'active')
                    ->select(['id', 'name', 'logo_link', 'website_link', 'description', 'order'])
                    ->orderBy('order')
                    ->get();
            }),
        ];

        $view->with($storefrontData);
    }

    /**
     * Share data cho layout views
     */
    private function shareLayoutData($view)
    {
        // Cache navigation data trong 2 giờ
        $navigationData = Cache::remember('navigation_data', 7200, function () {
            return [
                // Main Categories cho navigation
                'mainCategories' => CatProduct::where('status', 'active')
                    ->orderBy('order')
                    ->get(),

                // Footer Categories
                'footerCategories' => CatProduct::where('status', 'active')
                    ->orderBy('order')
                    ->take(6)
                    ->get(),

                // Recent Posts cho footer
                'recentPosts' => Post::where('status', 'active')
                    ->orderBy('created_at', 'desc')
                    ->take(3)
                    ->get(),

                // Menu Items cho dynamic navigation
                'menuItems' => MenuItem::where('status', 'active')
                    ->whereNull('parent_id')
                    ->with([
                        'children' => function ($query) {
                            $query->where('status', 'active')
                                ->orderBy('order');
                        },
                    ])
                    ->orderBy('order')
                    ->get(),

                // Associations cho footer certification images
                'associations' => Association::where('status', 'active')
                    ->orderBy('order')
                    ->get(),
            ];
        });

        $view->with($navigationData);
    }

    /**
     * Clear cache khi cần thiết
     */
    public static function clearCache()
    {
        Cache::forget('global_settings');

        // Clear storefront caches
        Cache::forget('storefront_sliders');
        Cache::forget('storefront_categories');
        Cache::forget('storefront_products');
        Cache::forget('storefront_featured_posts');
        Cache::forget('storefront_partners');

        Cache::forget('navigation_data');

        // Clear posts filter cache
        Cache::forget('posts_categories_filter');
    }

    /**
     * Refresh specific cache
     */
    public static function refreshCache($type = 'all')
    {
        switch ($type) {
            case 'settings':
                Cache::forget('global_settings');
                break;
            case 'storefront':
                Cache::forget('storefront_sliders');
                Cache::forget('storefront_categories');
                Cache::forget('storefront_products');
                Cache::forget('storefront_featured_posts');
                Cache::forget('storefront_partners');
                Cache::forget('posts_categories_filter');
                break;
            case 'sliders':
                Cache::forget('storefront_sliders');
                // Force rebuild cache ngay lập tức
                Cache::remember('storefront_sliders', 3600, function () {
                    return \App\Models\Slider::where('status', 'active')
                        ->orderBy('order')
                        ->select(['id', 'title', 'description', 'image_link', 'link', 'alt_text', 'order', 'status'])
                        ->get();
                });
                break;
            case 'navigation':
                Cache::forget('navigation_data');
                Cache::forget('posts_categories_filter');
                break;
            case 'all':
            default:
                self::clearCache();
                break;
        }
    }
}
