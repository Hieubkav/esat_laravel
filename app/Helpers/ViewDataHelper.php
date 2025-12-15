<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Cache;
use App\Models\Setting;
use App\Models\CatProduct;
use App\Models\Product;
use App\Models\Post;
use App\Models\Slider;
use App\Models\Partner;
use App\Models\MenuItem;

class ViewDataHelper
{
    /**
     * Get cached settings data
     */
    public static function getSettings()
    {
        return Cache::remember('global_settings', 3600, function () {
            return Setting::where('status', 'active')->first() ?? new Setting([
                'site_name' => config('app.name'),
                'seo_title' => config('app.name'),
                'hotline' => '1900636340',
                'email' => 'info@esat.vn',
                'status' => 'active'
            ]);
        });
    }

    /**
     * Get cached storefront data
     */
    public static function getStorefrontData()
    {
        return Cache::remember('storefront_data', 1800, function () {
            return [
                'sliders' => Slider::where('status', 'active')
                    ->orderBy('order')
                    ->get(),

                'categories' => CatProduct::where('status', 'active')
                    ->whereNull('parent_id')
                    ->orderBy('order')
                    ->take(12)
                    ->get(),

                'featuredProducts' => Product::where('status', 'active')
                    ->where('is_hot', true)
                    ->with(['category', 'images'])
                    ->orderBy('order')
                    ->take(8)
                    ->get(),

                'latestPosts' => Post::where('status', 'active')
                    ->with(['categories', 'images'])
                    ->orderBy('created_at', 'desc')
                    ->take(6)
                    ->get(),

                'partners' => Partner::where('status', 'active')
                    ->orderBy('order')
                    ->get(),

                'popularProducts' => Product::where('status', 'active')
                    ->where('is_hot', true)
                    ->with(['category', 'images'])
                    ->orderBy('order')
                    ->take(8)
                    ->get(),

                'newProducts' => Product::where('status', 'active')
                    ->with(['category', 'images'])
                    ->orderBy('created_at', 'desc')
                    ->take(8)
                    ->get(),
            ];
        });
    }

    /**
     * Get cached navigation data
     */
    public static function getNavigationData()
    {
        return Cache::remember('navigation_data', 7200, function () {
            return [
                'mainCategories' => CatProduct::where('status', 'active')
                    ->whereNull('parent_id')
                    ->with(['children' => function ($query) {
                        $query->where('status', 'active')->orderBy('order');
                    }])
                    ->orderBy('order')
                    ->get(),

                'footerCategories' => CatProduct::where('status', 'active')
                    ->whereNull('parent_id')
                    ->orderBy('order')
                    ->take(6)
                    ->get(),

                'recentPosts' => Post::where('status', 'active')
                    ->orderBy('created_at', 'desc')
                    ->take(3)
                    ->get(),

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
                                ])
                                ->orderBy('order');
                        },
                        'post:id,slug',
                        'catPost:id,slug',
                        'product:id,slug',
                        'catProduct:id,slug',
                    ])
                    ->orderBy('order')
                    ->get(),
            ];
        });
    }

    /**
     * Get specific data by key
     */
    public static function get($key, $default = null)
    {
        $storefrontData = self::getStorefrontData();
        $navigationData = self::getNavigationData();
        $settings = self::getSettings();

        $allData = array_merge($storefrontData, $navigationData, ['settings' => $settings]);

        return $allData[$key] ?? $default;
    }
}
