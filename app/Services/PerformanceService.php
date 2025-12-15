<?php

namespace App\Services;

use App\Helpers\PlaceholderHelper;
use Illuminate\Support\Facades\Cache;

class PerformanceService
{
    /**
     * Lấy URL ảnh tối ưu với fallback
     *
     * @param object|null $model Model có ảnh
     * @param string $imageField Tên field chứa ảnh
     * @param string $type Loại placeholder
     * @param bool $lazy Có lazy loading không
     * @return array [url, alt, loading]
     */
    public static function getOptimizedImage($model, string $imageField = 'image_link', string $type = 'default', bool $lazy = true): array
    {
        $imageUrl = null;
        $alt = '';
        
        if ($model && isset($model->$imageField) && !empty($model->$imageField)) {
            $imageUrl = asset('storage/' . $model->$imageField);
            $alt = $model->name ?? $model->title ?? 'Hình ảnh';
        } else {
            $imageUrl = PlaceholderHelper::getPlaceholderImage($type);
            $alt = $model->name ?? $model->title ?? 'Hình ảnh placeholder';
        }

        return [
            'url' => $imageUrl,
            'alt' => $alt,
            'loading' => $lazy ? 'lazy' : 'eager'
        ];
    }

    /**
     * Tạo HTML img tag tối ưu
     *
     * @param object|null $model
     * @param string $imageField
     * @param string $type
     * @param string $classes
     * @param bool $lazy
     * @return string
     */
    public static function renderOptimizedImage($model, string $imageField = 'image_link', string $type = 'default', string $classes = '', bool $lazy = true): string
    {
        $image = self::getOptimizedImage($model, $imageField, $type, $lazy);
        
        return sprintf(
            '<img src="%s" alt="%s" class="%s" loading="%s">',
            $image['url'],
            htmlspecialchars($image['alt']),
            $classes,
            $image['loading']
        );
    }

    /**
     * Preload critical images
     *
     * @param array $images
     * @return string
     */
    public static function preloadImages(array $images): string
    {
        $preloadTags = '';
        foreach ($images as $image) {
            if (filter_var($image, FILTER_VALIDATE_URL)) {
                $preloadTags .= sprintf('<link rel="preload" as="image" href="%s">', $image) . "\n";
            }
        }
        return $preloadTags;
    }

    /**
     * Tối ưu cache cho storefront data
     *
     * @param string $key
     * @param callable $callback
     * @param int $minutes
     * @return mixed
     */
    public static function cacheStorefrontData(string $key, callable $callback, int $minutes = 30)
    {
        return Cache::remember("storefront_{$key}", $minutes * 60, $callback);
    }

    /**
     * Invalidate storefront cache
     *
     * @param array $keys
     * @return void
     */
    public static function invalidateStorefrontCache(array $keys = []): void
    {
        $defaultKeys = [
            'storefront_sliders',
            'storefront_products',
            'storefront_posts',
            'storefront_partners',
            'storefront_categories'
        ];

        $keysToInvalidate = empty($keys) ? $defaultKeys : $keys;

        foreach ($keysToInvalidate as $key) {
            Cache::forget($key);
        }
    }

    /**
     * Tạo responsive image srcset
     *
     * @param string $imagePath
     * @param array $sizes
     * @return string
     */
    public static function generateSrcSet(string $imagePath, array $sizes = [400, 800, 1200]): string
    {
        if (str_starts_with($imagePath, 'data:')) {
            return $imagePath; // SVG placeholder không cần srcset
        }

        $srcset = [];
        $baseUrl = asset('storage/' . $imagePath);
        
        foreach ($sizes as $size) {
            $srcset[] = "{$baseUrl} {$size}w";
        }

        return implode(', ', $srcset);
    }

    /**
     * Kiểm tra và tối ưu performance metrics
     *
     * @return array
     */
    public static function getPerformanceMetrics(): array
    {
        return [
            'cache_hit_rate' => self::getCacheHitRate(),
            'image_optimization' => self::getImageOptimizationStatus(),
            'database_queries' => self::getDatabaseQueryCount(),
        ];
    }

    private static function getCacheHitRate(): float
    {
        // Implement cache hit rate calculation
        return 85.5; // Placeholder
    }

    private static function getImageOptimizationStatus(): string
    {
        return 'optimized'; // Placeholder
    }

    private static function getDatabaseQueryCount(): int
    {
        return \DB::getQueryLog() ? count(\DB::getQueryLog()) : 0;
    }
}
