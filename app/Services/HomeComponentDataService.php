<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Post;
use App\Models\Partner;
use Illuminate\Support\Facades\Cache;

class HomeComponentDataService
{
    /**
     * Load data cho component dựa trên type và config
     */
    public function loadComponentData(string $type, array $config): array
    {
        return match ($type) {
            'featured_products' => $this->loadFeaturedProducts($config),
            'news' => $this->loadNews($config),
            'partners' => $this->loadPartners($config),
            default => [],
        };
    }

    /**
     * Load featured products data
     */
    protected function loadFeaturedProducts(array $config): array
    {
        $displayMode = $config['display_mode'] ?? 'featured';
        $limit = $config['limit'] ?? 8;
        $cacheKey = "home_featured_products_{$displayMode}_{$limit}";

        $products = Cache::remember($cacheKey, 1800, function () use ($config, $displayMode, $limit) {
            if ($displayMode === 'manual' && !empty($config['products'])) {
                $productIds = collect($config['products'])->pluck('product_id')->filter();
                return Product::with(['category', 'productImages' => fn($q) => $q->orderBy('order')])
                    ->whereIn('id', $productIds)
                    ->where('status', 'active')
                    ->get();
            }

            return Product::with(['category', 'productImages' => fn($q) => $q->orderBy('order')])
                ->where('status', 'active')
                ->orderBy('created_at', 'desc')
                ->limit($limit)
                ->get();
        });

        return ['products' => $products];
    }

    /**
     * Load news/posts data
     */
    protected function loadNews(array $config): array
    {
        $displayMode = $config['display_mode'] ?? 'latest';
        $limit = $config['limit'] ?? 6;
        $cacheKey = "home_news_{$displayMode}_{$limit}";

        $posts = Cache::remember($cacheKey, 1800, function () use ($config, $displayMode, $limit) {
            if ($displayMode === 'manual' && !empty($config['posts'])) {
                $postIds = collect($config['posts'])->pluck('post_id')->filter();
                return Post::whereIn('id', $postIds)
                    ->where('status', 'active')
                    ->get();
            }

            return Post::where('status', 'active')
                ->orderBy('created_at', 'desc')
                ->limit($limit)
                ->get();
        });

        return ['posts' => $posts];
    }

    /**
     * Load partners data
     */
    protected function loadPartners(array $config): array
    {
        $displayMode = $config['display_mode'] ?? 'auto';
        $limit = $config['limit'] ?? 10;

        if ($displayMode === 'manual' && !empty($config['partners'])) {
            return ['partners' => collect($config['partners'])];
        }

        $cacheKey = "home_partners_{$limit}";
        $partners = Cache::remember($cacheKey, 1800, function () use ($limit) {
            return Partner::where('status', true)
                ->orderBy('order')
                ->limit($limit)
                ->get()
                ->map(fn($p) => [
                    'logo' => $p->logo_link,
                    'name' => $p->name,
                    'link' => $p->website_link,
                ]);
        });

        return ['partners' => $partners];
    }

    /**
     * Clear all home component caches
     */
    public function clearCache(): void
    {
        Cache::forget('home_featured_products_*');
        Cache::forget('home_news_*');
        Cache::forget('home_partners_*');
    }
}
