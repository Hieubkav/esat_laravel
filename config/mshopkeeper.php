<?php

return [
    /*
    |--------------------------------------------------------------------------
    | MShopKeeper API Configuration
    |--------------------------------------------------------------------------
    |
    | Cấu hình cho tích hợp API MShopKeeper
    |
    */

    'app_id' => env('MSHOPKEEPER_APP_ID', 'MShopKeeperOpenPlatform'),
    
    'domain' => env('MSHOPKEEPER_DOMAIN', 'vuphucbaking.mshopkeeper.vn'),
    
    'secret_key' => env('MSHOPKEEPER_SECRET_KEY', '9dfd836206bc97e25c2b0088d932debd69489265507b2b2758ff652aadca4207'),
    
    'base_url' => env('MSHOPKEEPER_BASE_URL', 'https://graphapi.mshopkeeper.vn'),

    'mock_mode' => env('MSHOPKEEPER_MOCK_MODE', true),

    /*
    |--------------------------------------------------------------------------
    | API Endpoints
    |--------------------------------------------------------------------------
    */
    'endpoints' => [
        'login' => '/auth/api/Account/Login',
        'categories' => '/api/v1/categories/list',
        'categories_tree' => '/api/v1/categories/tree',
        'branchs' => '/api/v1/branchs/all',
        'customers' => '/api/v1/customers/paging',
        'member_levels' => '/api/v1/customers/get-all-member-level',
        'products' => '/api/v1/products/list',
        'orders' => '/api/v1/orders/list',

        // Customer APIs - Tìm kiếm và thông tin khách hàng
        'customers_by_info' => '/api/v1/customers/customerbyinfo',
        'customers_lomas_search' => '/api/v1/customers/search-lomas-info',
        'customers_point_paging' => '/api/v1/customers/point-paging',
        'create_customer' => '/api/v1/customers/',

        // Inventory/Products APIs - Quản lý hàng hóa
        'inventory_paging_with_detail' => '/api/v1/inventoryitems/pagingwithdetail',
        'inventory_paging_by_code' => '/api/v1/inventoryitems/pagingbycode',
        'inventory_detail' => '/api/v1/inventoryitems/detail',

        // Order APIs - Quản lý đơn hàng
        'create_order' => '/api/v1/invoices/',

        // Invoice APIs - Quản lý hóa đơn
        'invoice_detail_by_refid' => '/api/v1/invoices/detailbyrefid',
    ],

    /*
    |--------------------------------------------------------------------------
    | Additional Settings
    |--------------------------------------------------------------------------
    */
    'company_code' => env('MSHOPKEEPER_COMPANY_CODE', 'demoquanao'),
    'default_branch_id' => env('MSHOPKEEPER_DEFAULT_BRANCH_ID', '8C3D6B0D-3B58-4379-BFFB-1CCEA7A7F884'),

    /*
    |--------------------------------------------------------------------------
    | Request Settings
    |--------------------------------------------------------------------------
    */
    'timeout' => env('MSHOPKEEPER_TIMEOUT', 180),
    'retry_attempts' => env('MSHOPKEEPER_RETRY_ATTEMPTS', 3),
    'retry_delay' => env('MSHOPKEEPER_RETRY_DELAY', 1000), // milliseconds
    
    /*
    |--------------------------------------------------------------------------
    | Cache Settings
    |--------------------------------------------------------------------------
    */
    'cache' => [
        'token_ttl' => env('MSHOPKEEPER_TOKEN_TTL', 3600), // 1 hour
        'categories_ttl' => env('MSHOPKEEPER_CATEGORIES_TTL', 1800), // 30 minutes
        'products_ttl' => env('MSHOPKEEPER_PRODUCTS_TTL', 900), // 15 minutes
    ],
    
    /*
    |--------------------------------------------------------------------------
    | Error Types Mapping (theo tài liệu API chính thức)
    |--------------------------------------------------------------------------
    */
    'error_types' => [
        // Dải mã lỗi chung
        0 => 'Không có lỗi',
        1 => 'Tham số không hợp lệ null or empty',
        2 => 'Mã cửa hàng không tồn tại',
        3 => 'Mã Appid không tồn tại trên hệ thống',
        4 => 'Chuỗi thông tin chữ ký đăng nhập không hợp lệ, timeout',
        5 => 'Tham số lấy phân trang vượt quá số lượng cấu hình cho phép (max 100)',
        6 => 'Tham số ngày giờ không hợp lệ (01/01/1753 - 31/12/9999)',
        7 => 'Thiết lập kết nối MShopKeeper đang ở trạng thái ngắt, không thể lấy dữ liệu',

        // Dải mã lỗi nghiêm trọng
        100 => 'Lỗi nội bộ API Graph',
        102 => 'Request bị từ chối, do có request cùng loại đang xử lý. Vui lòng chờ xử lý xong hoặc chờ request đang xử lý timeout thì gọi lại',

        // Mã lỗi HTTP
        'UNAUTHORIZED' => 'Chuỗi AccessToken hết hạn hoặc không hợp lệ cần phải gọi cấp phát lại',
        'UNKNOWN_ERROR' => 'Lỗi không xác định',
    ],
    
    /*
    |--------------------------------------------------------------------------
    | Logging Settings
    |--------------------------------------------------------------------------
    */
    'logging' => [
        'enabled' => env('MSHOPKEEPER_LOGGING_ENABLED', true),
        'level' => env('MSHOPKEEPER_LOG_LEVEL', 'debug'),
        'channel' => env('MSHOPKEEPER_LOG_CHANNEL', 'single'),
    ],
];
