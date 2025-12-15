<?php

namespace App\Helpers;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;

class PlaceholderHelper
{
    /**
     * Lấy ảnh placeholder từ settings hoặc fallback
     *
     * @param string $type Loại placeholder (product, post, employee, etc.)
     * @return string URL của ảnh placeholder
     */
    public static function getPlaceholderImage(string $type = 'default'): string
    {
        // Lấy settings từ cache
        $settings = Cache::remember('settings', 3600, function () {
            return Setting::first();
        });

        // Nếu có ảnh placeholder trong settings
        if ($settings && $settings->placeholder_image) {
            return asset('storage/' . $settings->placeholder_image);
        }

        // Nếu có logo thì dùng logo
        if ($settings && $settings->logo_link) {
            return asset('storage/' . $settings->logo_link);
        }

        // Fallback về ảnh mặc định theo loại
        return self::getDefaultPlaceholder($type);
    }

    /**
     * Lấy ảnh placeholder mặc định theo loại
     *
     * @param string $type
     * @return string
     */
    private static function getDefaultPlaceholder(string $type): string
    {
        $placeholders = [
            'product' => 'data:image/svg+xml;base64,' . base64_encode(self::generateSvgPlaceholder('🧁', '#dc2626')),
            'post' => 'data:image/svg+xml;base64,' . base64_encode(self::generateSvgPlaceholder('📰', '#dc2626')),
            'employee' => 'data:image/svg+xml;base64,' . base64_encode(self::generateSvgPlaceholder('👤', '#dc2626')),
            'partner' => 'data:image/svg+xml;base64,' . base64_encode(self::generateSvgPlaceholder('🤝', '#dc2626')),
            'slider' => 'data:image/svg+xml;base64=' . base64_encode(self::generateSvgPlaceholder('🖼️', '#dc2626')),
            'default' => 'data:image/svg+xml;base64,' . base64_encode(self::generateSvgPlaceholder('📷', '#dc2626')),
        ];

        return $placeholders[$type] ?? $placeholders['default'];
    }

    /**
     * Tạo SVG placeholder với icon và màu
     *
     * @param string $icon
     * @param string $color
     * @return string
     */
    private static function generateSvgPlaceholder(string $icon, string $color): string
    {
        return '<svg width="400" height="400" xmlns="http://www.w3.org/2000/svg">
            <rect width="400" height="400" fill="#f3f4f6"/>
            <rect x="50" y="50" width="300" height="300" fill="' . $color . '" opacity="0.1" rx="20"/>
            <text x="200" y="220" font-family="Arial, sans-serif" font-size="80" text-anchor="middle" fill="' . $color . '">' . $icon . '</text>
        </svg>';
    }

    /**
     * Lấy logo từ settings hoặc fallback
     *
     * @return string
     */
    public static function getLogo(): string
    {
        $settings = Cache::remember('settings', 3600, function () {
            return Setting::first();
        });

        if ($settings && $settings->logo_link) {
            return asset('storage/' . $settings->logo_link);
        }

        // Fallback về SVG logo
        return 'data:image/svg+xml;base64,' . base64_encode(
            '<svg width="200" height="100" xmlns="http://www.w3.org/2000/svg">
                <rect width="200" height="100" fill="#dc2626"/>
                <text x="100" y="60" font-family="Arial, sans-serif" font-size="24" font-weight="bold" text-anchor="middle" fill="white">LOGO</text>
            </svg>'
        );
    }

    /**
     * Kiểm tra xem có ảnh thật hay không
     *
     * @param string|null $imagePath
     * @return bool
     */
    public static function hasRealImage(?string $imagePath): bool
    {
        return !empty($imagePath) && !str_starts_with($imagePath, 'data:image/svg+xml');
    }
}
