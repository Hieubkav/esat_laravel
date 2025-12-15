<?php

namespace App\Constants;

class NavigationGroups
{
    // 3 nhóm navigation chính - loại bỏ icon màu sắc
    // Dashboard không có group, hiển thị ở đầu sidebar
    // Thứ tự hiển thị: Dashboard (no group) -> Product -> Content -> Settings
    const PRODUCT_MANAGEMENT = 'Quản lý sản phẩm';
    const ECOMMERCE = 'Thương mại điện tử';
    const CONTENT_MANAGEMENT = 'Quản lý nội dung';
    const WEBSITE_SETTINGS = 'Cài đặt website';

    /**
     * Lấy giá trị navigation group an toàn
     */
    public static function getGroup(string $groupName): string
    {
        $groups = [
            'PRODUCT_MANAGEMENT' => self::PRODUCT_MANAGEMENT,
            'ECOMMERCE' => self::ECOMMERCE,
            'CONTENT_MANAGEMENT' => self::CONTENT_MANAGEMENT,
            'WEBSITE_SETTINGS' => self::WEBSITE_SETTINGS,
        ];

        return $groups[$groupName] ?? 'Khác';
    }
}
