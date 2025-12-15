<?php

if (!function_exists('getProductImageUrl')) {
    /**
     * Tạo URL ảnh WebP với tên SEO-friendly cho sản phẩm
     *
     * @param object $product
     * @return string|null
     */
    function getProductImageUrl($product): ?string
    {
        // Chỉ sử dụng ảnh từ relationship images (ProductImage)
        if ($product && $product->images && $product->images->count() > 0) {
            // Lấy ảnh chính hoặc ảnh đầu tiên
            $mainImage = $product->images->where('is_main', true)->first()
                ?? $product->images->where('status', 'active')->first()
                ?? $product->images->first();

            if ($mainImage && $mainImage->image_link) {
                return getProductImageUrlFromImage($mainImage, $product->name);
            }
        }

        return null;
    }
}

if (!function_exists('getProductImageUrlFromImage')) {
    /**
     * Tạo URL ảnh WebP với tên SEO-friendly cho ProductImage
     *
     * @param object $productImage
     * @param string $productName
     * @return string|null
     */
    function getProductImageUrlFromImage($productImage, string $productName): ?string
    {
        if (!$productImage || !$productImage->image_link) {
            return null;
        }

        return asset('storage/' . $productImage->image_link);
    }
}

if (!function_exists('getProductImageAlt')) {
    /**
     * Lấy alt text cho ảnh sản phẩm
     *
     * @param object $productImage
     * @param string $productName
     * @param string|null $seoTitle
     * @return string
     */
    function getProductImageAlt($productImage, string $productName, ?string $seoTitle = null): string
    {
        if ($productImage && !empty($productImage->alt_text)) {
            return $productImage->alt_text;
        }

        if (!empty($seoTitle)) {
            return $seoTitle;
        }

        return $productName;
    }
}

if (!function_exists('formatPrice')) {
    /**
     * Format giá tiền
     *
     * @param float|int $price
     * @param string $currency
     * @return string
     */
    function formatPrice($price, string $currency = 'đ'): string
    {
        return number_format($price, 0, ',', '.') . $currency;
    }
}

if (!function_exists('getBakingIcon')) {
    /**
     * Tạo custom baking icon HTML
     *
     * @param string $size
     * @param string $color
     * @param bool $withText
     * @return string
     */
    function getBakingIcon(string $size = 'w-16 h-16', string $color = 'text-red-300', bool $withText = true): string
    {
        $icon = '<svg xmlns="http://www.w3.org/2000/svg" class="' . $size . ' ' . $color . ' mx-auto' . ($withText ? ' mb-2' : '') . '" fill="currentColor" viewBox="0 0 24 24">
            <path d="M12 2C13.1 2 14 2.9 14 4C14 5.1 13.1 6 12 6C10.9 6 10 5.1 10 4C10 2.9 10.9 2 12 2ZM21 9V7L15 1H9L3 7V9H21ZM12 19C10.9 19 10 18.1 10 17C10 15.9 10.9 15 12 15C13.1 15 14 15.9 14 17C14 18.1 13.1 19 12 19ZM12 13C9.8 13 8 14.8 8 17C8 19.2 9.8 21 12 21C14.2 21 16 19.2 16 17C16 14.8 14.2 13 12 13ZM5 11C3.9 11 3 11.9 3 13C3 14.1 3.9 15 5 15C6.1 15 7 14.1 7 13C7 11.9 6.1 11 5 11ZM19 11C17.9 11 17 11.9 17 13C17 14.1 17.9 15 19 15C20.1 15 21 14.1 21 13C21 11.9 20.1 11 19 11Z"/>
        </svg>';

        if ($withText) {
            $textSize = str_contains($size, 'w-32') ? 'text-base' : (str_contains($size, 'w-20') ? 'text-sm' : 'text-xs');
            $icon .= '<p class="' . $textSize . ' text-red-400 font-medium">ESAT</p>';
        }

        return $icon;
    }
}

if (!function_exists('getOptimizedImageUrl')) {
    /**
     * Lấy URL ảnh tối ưu với fallback
     *
     * @param object|null $model
     * @param string $field
     * @param string $type
     * @return string
     */
    function getOptimizedImageUrl($model, string $field = 'image_link', string $type = 'default'): string
    {
        if ($model && isset($model->$field) && !empty($model->$field)) {
            return asset('storage/' . $model->$field);
        }

        return \App\Helpers\PlaceholderHelper::getPlaceholderImage($type);
    }
}
