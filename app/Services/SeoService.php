<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Post;
use App\Models\Setting;
use App\Helpers\PlaceholderHelper;
use Illuminate\Support\Facades\Cache;

class SeoService
{
    /**
     * Lấy OG image cho sản phẩm
     *
     * @param Product $product
     * @return string
     */
    public static function getProductOgImage(Product $product): string
    {
        // Ưu tiên ảnh đầu tiên từ relationship images
        if ($product->images && $product->images->count() > 0) {
            $firstImage = $product->images->first();
            if ($firstImage && $firstImage->image_link) {
                return asset('storage/' . $firstImage->image_link);
            }
        }

        // Fallback về og_image_link của sản phẩm
        if ($product->og_image_link) {
            return asset('storage/' . $product->og_image_link);
        }

        // Fallback về settings og_image
        return self::getDefaultOgImage();
    }

    /**
     * Lấy OG image cho bài viết
     *
     * @param Post $post
     * @return string
     */
    public static function getPostOgImage(Post $post): string
    {
        // Ưu tiên thumbnail
        if ($post->thumbnail) {
            return asset('storage/' . $post->thumbnail);
        }

        // Ưu tiên ảnh đầu tiên từ relationship images
        if ($post->images && $post->images->count() > 0) {
            $firstImage = $post->images->first();
            if ($firstImage && $firstImage->image_link) {
                return asset('storage/' . $firstImage->image_link);
            }
        }

        // Fallback về og_image_link của bài viết
        if ($post->og_image_link) {
            return asset('storage/' . $post->og_image_link);
        }

        // Fallback về settings og_image
        return self::getDefaultOgImage();
    }

    /**
     * Lấy OG image mặc định từ settings
     *
     * @return string
     */
    public static function getDefaultOgImage(): string
    {
        $settings = Cache::remember('global_settings', 3600, function () {
            return Setting::first();
        });

        if ($settings && $settings->og_image_link) {
            return asset('storage/' . $settings->og_image_link);
        }

        if ($settings && $settings->logo_link) {
            return asset('storage/' . $settings->logo_link);
        }

        return PlaceholderHelper::getLogo();
    }

    /**
     * Tạo structured data cho sản phẩm
     *
     * @param Product $product
     * @return array
     */
    public static function getProductStructuredData(Product $product): array
    {
        $settings = Cache::remember('global_settings', 3600, function () {
            return Setting::first();
        });

        return [
            '@context' => 'https://schema.org',
            '@type' => 'Product',
            'name' => $product->seo_title ?: $product->name,
            'description' => $product->seo_description ?: $product->description,
            'image' => self::getProductOgImage($product),
            'url' => route('products.show', $product->slug),
            'sku' => $product->sku,
            'brand' => [
                '@type' => 'Brand',
                'name' => $product->brand ?: ($settings->site_name ?? 'Vũ Phúc Baking')
            ],
            'offers' => [
                '@type' => 'Offer',
                'price' => $product->getCurrentPrice(),
                'priceCurrency' => 'VND',
                'availability' => $product->stock > 0 ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock',
                'url' => route('products.show', $product->slug)
            ]
        ];
    }

    /**
     * Tạo structured data cho bài viết
     *
     * @param Post $post
     * @return array
     */
    public static function getPostStructuredData(Post $post): array
    {
        $settings = Cache::remember('global_settings', 3600, function () {
            return Setting::first();
        });

        return [
            '@context' => 'https://schema.org',
            '@type' => 'Article',
            'headline' => $post->seo_title ?: $post->title,
            'description' => $post->seo_description ?: ($post->content ? strip_tags(substr($post->content, 0, 160)) : ''),
            'image' => self::getPostOgImage($post),
            'url' => route('posts.show', $post->slug),
            'datePublished' => $post->created_at->toISOString(),
            'dateModified' => $post->updated_at->toISOString(),
            'author' => [
                '@type' => 'Organization',
                'name' => $settings->site_name ?? 'Vũ Phúc Baking'
            ],
            'publisher' => [
                '@type' => 'Organization',
                'name' => $settings->site_name ?? 'Vũ Phúc Baking',
                'logo' => [
                    '@type' => 'ImageObject',
                    'url' => self::getDefaultOgImage()
                ]
            ]
        ];
    }

    /**
     * Tạo breadcrumb structured data
     *
     * @param array $breadcrumbs
     * @return array
     */
    public static function getBreadcrumbStructuredData(array $breadcrumbs): array
    {
        $itemListElement = [];

        foreach ($breadcrumbs as $index => $breadcrumb) {
            $itemListElement[] = [
                '@type' => 'ListItem',
                'position' => $index + 1,
                'name' => $breadcrumb['name'],
                'item' => $breadcrumb['url']
            ];
        }

        return [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => $itemListElement
        ];
    }

    /**
     * Tạo meta tags cho SEO
     *
     * @param string $title
     * @param string $description
     * @param string $ogImage
     * @param string $url
     * @param string $type
     * @return array
     */
    public static function getMetaTags(string $title, string $description, string $ogImage, string $url, string $type = 'website'): array
    {
        return [
            'title' => $title,
            'description' => $description,
            'og:title' => $title,
            'og:description' => $description,
            'og:image' => $ogImage,
            'og:url' => $url,
            'og:type' => $type,
            'twitter:card' => 'summary_large_image',
            'twitter:title' => $title,
            'twitter:description' => $description,
            'twitter:image' => $ogImage,
        ];
    }
}
