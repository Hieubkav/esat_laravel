<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Cấu hình tối ưu hiệu suất Filament
    |--------------------------------------------------------------------------
    |
    | File này chứa các cấu hình để tối ưu hóa hiệu suất của Filament Admin Panel
    |
    */

    // Cấu hình pagination mặc định
    'pagination' => [
        'default_per_page' => 10,
        'per_page_options' => [5, 10, 15, 25, 50],
        'max_per_page' => 50,
    ],

    // Cấu hình cache
    'cache' => [
        'navigation_badge_ttl' => 300, // 5 phút
        'table_count_ttl' => 60, // 1 phút
        'enable_query_cache' => true,
    ],

    // Cấu hình tối ưu query
    'query_optimization' => [
        'enable_select_optimization' => true,
        'enable_eager_loading' => true,
        'limit_relations' => true,
    ],

    // Cấu hình UI performance
    'ui_performance' => [
        'enable_spa_mode' => true,
        'enable_unsaved_changes_alerts' => true,
        'loading_delay' => 'none', // Hiển thị loading ngay lập tức
    ],

    // Cấu hình image optimization
    'image_optimization' => [
        'thumbnail_max_width' => 300,
        'thumbnail_max_height' => 200,
        'preview_max_width' => 800,
        'preview_max_height' => 600,
        'default_quality' => 85,
    ],
];
