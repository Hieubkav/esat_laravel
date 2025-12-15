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
use App\Models\MShopKeeperInventoryItem;
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
                'email' => 'info@vuphucbaking.com',
                'status' => 'active'
            ]);
        });

        $view->with([
            'globalSettings' => $settings,
            'settings' => $settings // Giữ lại để tương thích với code cũ
        ]);
    }

    /**
     * Share data cho storefront views - Tối ưu performance
     */
    private function shareStorefrontData($view)
    {
        // Cache riêng biệt cho từng loại dữ liệu để tối ưu hơn
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
                    ->whereNull('parent_id')
                    ->orderBy('order')
                    ->select(['id', 'name', 'slug', 'image', 'order'])
                    ->take(12)
                    ->get();
            }),

            // Featured Products - Cache 5 phút (sử dụng MShopKeeper Inventory)
            'featuredProducts' => Cache::remember('storefront_mshopkeeper_products', 300, function () {
                return \App\Models\MShopKeeperInventoryItem::where('inactive', false)
                    ->where('is_visible', true)
                    ->where('is_featured', true)
                    ->where('is_item', true)
                    ->with(['category'])
                    ->select(['id', 'mshopkeeper_id', 'code', 'name', 'selling_price', 'cost_price', 'picture', 'description', 'category_mshopkeeper_id', 'total_on_hand', 'is_featured', 'is_visible', 'inactive', 'is_item'])
                    ->orderBy('total_on_hand', 'desc')
                    ->orderBy('selling_price', 'desc')
                    ->take(8)
                    ->get();
            }),

            // Services data - Cache 1 giờ
            'services' => Cache::remember('storefront_services', 3600, function () {
                return Post::where('status', 'active')
                    ->where('type', 'service')
                    ->with(['categories:id,name', 'images' => function($query) {
                        $query->where('status', 'active')->orderBy('order')->take(1);
                    }])
                    ->select(['id', 'title', 'slug', 'seo_description', 'thumbnail', 'order'])
                    ->orderBy('order')
                    ->get();
            }),

            // News Posts - Cache 30 phút
            'newsPosts' => Cache::remember('storefront_news', 1800, function () {
                return Post::where('status', 'active')
                    ->where('type', 'news')
                    ->where('is_featured', true)
                    ->with(['categories:id,name', 'images' => function($query) {
                        $query->where('status', 'active')->orderBy('order')->take(1);
                    }])
                    ->select(['id', 'title', 'slug', 'seo_description', 'thumbnail', 'order', 'created_at'])
                    ->orderBy('order')
                    ->orderBy('created_at', 'desc')
                    ->take(4)
                    ->get();
            }),

            // Courses - Cache 1 giờ
            'courses' => Cache::remember('storefront_courses', 3600, function () {
                return Post::where('status', 'active')
                    ->where('type', 'course')
                    ->with(['categories:id,name', 'images' => function($query) {
                        $query->where('status', 'active')->orderBy('order')->take(1);
                    }])
                    ->select(['id', 'title', 'slug', 'seo_description', 'seo_title', 'thumbnail', 'order', 'created_at'])
                    ->orderBy('order')
                    ->orderBy('created_at', 'desc')
                    ->take(6)
                    ->get();
            }),

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
                    ->whereNull('parent_id')
                    ->with(['children' => function ($query) {
                        $query->where('status', 'active')->orderBy('order');
                    }])
                    ->orderBy('order')
                    ->get(),

                // Footer Categories
                'footerCategories' => CatProduct::where('status', 'active')
                    ->whereNull('parent_id')
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
                                ->with([
                                    'post:id,slug',
                                    'catPost:id,slug',
                                    'product:id,slug',
                                    'catProduct:id,slug',
                                    'mshopkeeperInventoryItem:id,code,name',
                                    'mshopkeeperCategory:id,name'
                                ])
                                ->orderBy('order');
                        },
                        'post:id,slug',
                        'catPost:id,slug',
                        'product:id,slug',
                        'catProduct:id,slug',
                        'mshopkeeperInventoryItem:id,code,name',
                        'mshopkeeperCategory:id,name'
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
        Cache::forget('storefront_services');
        Cache::forget('storefront_news');
        Cache::forget('storefront_courses');
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
                Cache::forget('storefront_services');
                Cache::forget('storefront_news');
                Cache::forget('storefront_courses');
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
