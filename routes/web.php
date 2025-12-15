<?php

use App\Http\Controllers\EcomerceController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\CustomerAuthController;
use App\Http\Controllers\ModalAuthController;
use App\Http\Controllers\CustomerOrderController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\MShopKeeperTestController;
use App\Http\Controllers\MShopKeeperInventoryController;
use App\Http\Controllers\MShopKeeperCartController;
use App\Http\Controllers\MShopKeeperOrderController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MainController;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;

// CSRF Token endpoint for JavaScript helpers
Route::get('/csrf-token', function () {
    return response()->json(['token' => csrf_token()]);
});

Route::controller(MainController::class)->group(function () {
    Route::get('/', 'storeFront')->name('storeFront');
});

Route::controller(EcomerceController::class)->group(function () {
    Route::get('/ban-hang', 'index')->name('ecomerce.index');
});

// Thêm route cho sản phẩm và danh mục
Route::controller(ProductController::class)->group(function () {
    Route::get('/danh-muc/{slug}', 'category')->name('products.category');
    Route::get('/danh-muc', 'categories')->name('products.categories');
    Route::get('/san-pham/{slug}', 'show')->name('products.show');
});

// Routes cho MShopKeeper Inventory (sản phẩm từ API)
Route::controller(MShopKeeperInventoryController::class)->group(function () {
    Route::get('/kho-hang', 'index')->name('mshopkeeper.inventory.index');
    Route::get('/kho-hang/gioi-thieu', 'intro')->name('mshopkeeper.inventory.intro');
    Route::get('/kho-hang/san-pham/{code}', 'show')->name('mshopkeeper.inventory.show');
    Route::get('/kho-hang/san-pham/{code}/lien-he', 'contact')->name('mshopkeeper.product.contact');
    Route::get('/kho-hang/noi-bat', 'featured')->name('mshopkeeper.inventory.featured');
    Route::get('/api/kho-hang/thong-ke', 'stats')->name('mshopkeeper.inventory.stats');
});

// Test route: Danh sách danh mục khác nhau của sản phẩm MShopKeeper
Route::get('/test-mshopkeeper-product-categories', function () {
    try {
        // Lấy danh sách danh mục distinct từ bảng sản phẩm tồn kho (đã sync từ MShopKeeper)
        $agg = \App\Models\MShopKeeperInventoryItem::where('inactive', false)
            ->where('is_visible', true)
            ->where('is_item', true)
            ->whereNotNull('category_name')
            ->selectRaw('category_mshopkeeper_id, category_name, COUNT(*) as products_count')
            ->groupBy('category_mshopkeeper_id', 'category_name')
            ->orderBy('category_name')
            ->get();

        // Nạp thông tin phân cấp từ bảng danh mục (nếu có)
        $ids = $agg->pluck('category_mshopkeeper_id')->filter()->unique()->values();
        $categories = \App\Models\MShopKeeperCategory::with('parent.parent.parent')
            ->whereIn('mshopkeeper_id', $ids)
            ->get()
            ->keyBy('mshopkeeper_id');

        $data = $agg->map(function ($row) use ($categories) {
            $cat = $row->category_mshopkeeper_id ? ($categories[$row->category_mshopkeeper_id] ?? null) : null;
            return [
                'category_mshopkeeper_id' => $row->category_mshopkeeper_id ?: null,
                'name' => $row->category_name,
                'products_count' => (int) $row->products_count,
                'has_hierarchy' => (bool) $cat,
                'grade' => $cat->grade ?? null,
                'breadcrumb' => $cat ? $cat->breadcrumb : $row->category_name,
            ];
        })->values();

        return response()->json([
            'success' => true,
            'total_distinct_categories' => $data->count(),
            'categories' => $data,
        ]);
    } catch (\Throwable $e) {
        return response()->json([
            'success' => false,
            'error' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
        ], 500);
    }
})->name('mshopkeeper.test.product_categories');

// Routes cho MShopKeeper Cart (giỏ hàng)
Route::controller(MShopKeeperCartController::class)->group(function () {
    Route::get('/gio-hang', 'show')->name('mshopkeeper.cart.show');
    Route::get('/gio-hang/lien-he', 'contact')->name('mshopkeeper.cart.contact');
    Route::post('/gio-hang/them', 'add')->name('mshopkeeper.cart.add');
    Route::patch('/gio-hang/cap-nhat/{cartItemId}', 'update')->name('mshopkeeper.cart.update');
    Route::delete('/gio-hang/xoa/{cartItemId}', 'remove')->name('mshopkeeper.cart.remove');
    Route::delete('/gio-hang/xoa-tat-ca', 'clear')->name('mshopkeeper.cart.clear');
    Route::get('/api/gio-hang/so-luong', 'count')->name('mshopkeeper.cart.count');
});

// Routes cho MShopKeeper Order (yêu cầu đăng nhập MShopKeeper)
Route::middleware('mshopkeeper.auth')->controller(MShopKeeperOrderController::class)->group(function () {
    Route::get('/dat-hang', 'checkout')->name('mshopkeeper.checkout');
    Route::post('/dat-hang', 'placeOrder')->name('mshopkeeper.order.place');
    Route::post('/api/dat-hang-nhanh', 'quickOrder')->name('mshopkeeper.order.quick');
});

// Test OrderNo auto-generation
Route::get('/test-orderno-autogen', function () {
    try {
        // Create test customer
        $customer = \App\Models\MShopKeeperCustomer::firstOrCreate([
            'email' => 'test-orderno@debug.com'
        ], [
            'mshopkeeper_id' => 'debug-orderno-' . time(),
            'code' => 'DEBUG_ORDERNO_CUSTOMER',
            'name' => 'Debug OrderNo Customer',
            'tel' => '0999888777',
            'addr' => 'Debug OrderNo Address',
            'email' => 'test-orderno@debug.com',
            'sync_status' => 'synced',
        ]);

        // Login as test customer
        auth('mshopkeeper_customer')->login($customer);

        // Add test product to cart
        $cartService = app(\App\Services\MShopKeeperCartService::class);
        $product = \App\Models\MShopKeeperInventoryItem::first();
        if ($product) {
            $cartService->addToCart($product->id, 1);
        }

        // Test order service with auto OrderNo generation
        $orderService = app(\App\Services\MShopKeeperOrderService::class);
        $orderData = [
            'shipping_address' => 'Test auto OrderNo generation',
            'payment_method' => 'cod',
            'note' => 'Test để hệ thống tự sinh OrderNo',
        ];

        $result = $orderService->createOrderFromCart($orderData);

        return response()->json([
            'success' => $result['success'] ?? false,
            'message' => $result['message'] ?? 'No message',
            'mshopkeeper_order_no' => $result['order']['OrderNo'] ?? null,
            'mshopkeeper_order_id' => $result['order']['OrderId'] ?? null,
            'local_order_number' => $result['local_order']['order_number'] ?? null,
            'note' => 'OrderNo được hệ thống MShopKeeper tự sinh (không gửi trong payload)',
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'error' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
        ], 500);
    }
});

// Test sync orders from MShopKeeper
Route::get('/test-sync-orders', function () {
    try {
        $mshopkeeperService = app(\App\Services\MShopKeeperService::class);

        // Test call orders API
        $result = $mshopkeeperService->getOrders([
            'Page' => 1,
            'Limit' => 10,
            'SortField' => 'OrderDate',
            'SortType' => 0, // Descending - mới nhất trước
        ]);

        if ($result['success']) {
            $orders = $result['data'] ?? [];

            // Tìm order DT000025 hoặc orders gần đây
            $recentOrders = collect($orders)->take(5);

            return response()->json([
                'success' => true,
                'message' => 'Orders API working',
                'orders_count' => count($orders),
                'recent_orders' => $recentOrders->map(function($order) {
                    return [
                        'OrderId' => $order['OrderId'] ?? null,
                        'OrderNo' => $order['OrderNo'] ?? null,
                        'OrderDate' => $order['OrderDate'] ?? null,
                        'TotalAmount' => $order['TotalAmount'] ?? null,
                        'CustomerName' => $order['Customer']['Name'] ?? null,
                        'Status' => $order['Status'] ?? null,
                    ];
                }),
                'found_dt000025' => collect($orders)->contains(function($order) {
                    return ($order['OrderNo'] ?? '') === 'DT000025';
                }),
            ]);
        } else {
            return response()->json([
                'success' => false,
                'error' => $result['error'] ?? 'Unknown error',
                'message' => 'Failed to fetch orders from MShopKeeper API'
            ]);
        }

    } catch (\Exception $e) {
        return response()->json([
            'error' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
        ], 500);
    }
});

// Test view orders directly
Route::get('/view-orders', function () {
    try {
        $orders = \App\Models\Order::with('items')
            ->latest()
            ->take(10)
            ->get();

        $ordersData = $orders->map(function($order) {
            return [
                'id' => $order->id,
                'order_number' => $order->order_number,
                'mshopkeeper_order_no' => $order->mshopkeeper_order_no,
                'mshopkeeper_order_id' => $order->mshopkeeper_order_id,
                'total' => number_format($order->total) . 'đ',
                'status' => $order->status,
                'payment_method' => $order->payment_method,
                'shipping_name' => $order->shipping_name,
                'shipping_phone' => $order->shipping_phone,
                'shipping_address' => $order->shipping_address,
                'note' => $order->note,
                'created_at' => $order->created_at->format('d/m/Y H:i'),
                'items_count' => $order->items->count(),
                'items' => $order->items->map(function($item) {
                    return [
                        'product_name' => $item->mshopkeeper_product_name ?? $item->product_name,
                        'product_code' => $item->mshopkeeper_product_code ?? $item->product_code,
                        'quantity' => $item->quantity,
                        'price' => number_format($item->price) . 'đ',
                        'total' => number_format($item->subtotal ?? $item->total ?? 0) . 'đ',
                    ];
                }),
            ];
        });

        return response()->json([
            'success' => true,
            'orders_count' => \App\Models\Order::count(),
            'orders' => $ordersData,
            'message' => 'Đây là danh sách đơn hàng từ Quick Order Modal'
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'error' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
        ], 500);
    }
});

// Test MShopKeeper Orders Resource với mock data trực tiếp
Route::get('/test-mshopkeeper-orders', function () {
    try {
        // Mock data trực tiếp để test admin panel
        $mockOrders = [
            [
                'OrderId' => 'dd4a1745-4d42-48d9-aeb7-a202294983b2',
                'OrderNo' => 'DT000025',
                'OrderDate' => '2025-08-19T05:30:00+07:00',
                'TotalAmount' => 28500.0,
                'Status' => 'Pending',
                'Description' => 'Đơn hàng từ Quick Order Modal',
                'Customer' => [
                    'Id' => 'debug-orderno-1755556000',
                    'Code' => 'DEBUG_ORDERNO_CUSTOMER',
                    'Name' => 'Debug OrderNo Customer',
                    'Tel' => '0999888777',
                    'Email' => 'test-orderno@debug.com',
                    'Address' => 'Hưng Lợi, Ninh Kiều, Cần Thơ'
                ],
                'OrderDetails' => [
                    [
                        'ProductId' => '324d4a93-7508-4759-9a44-6174159d2cf8',
                        'ProductCode' => '188108207',
                        'ProductName' => 'Sữa tươi Milk SECRET nguyên chất tiệt trùng 3.5% Fat - Ba Lan (12h/Th) - giá 1: 10T',
                        'Quantity' => 1.0,
                        'SellingPrice' => 28500.0,
                        'Amount' => 28500.0
                    ]
                ]
            ],
            [
                'OrderId' => 'dd4a1745-4d42-48d9-aeb7-a202294983b3',
                'OrderNo' => 'DT000024',
                'OrderDate' => '2025-08-19T05:46:00+07:00',
                'TotalAmount' => 3500.0,
                'Status' => 'Pending',
                'Description' => 'Test để hệ thống tự sinh OrderNo',
                'Customer' => [
                    'Name' => 'Test Customer',
                    'Tel' => '0123456789',
                    'Email' => 'test@example.com',
                    'Address' => 'Test Address'
                ],
                'OrderDetails' => []
            ]
        ];

        return response()->json([
            'success' => true,
            'message' => 'Mock data cho MShopKeeper Orders',
            'orders_count' => count($mockOrders),
            'orders' => collect($mockOrders)->map(function($order) {
                return [
                    'OrderNo' => $order['OrderNo'],
                    'OrderDate' => $order['OrderDate'],
                    'CustomerName' => $order['Customer']['Name'],
                    'TotalAmount' => number_format($order['TotalAmount']) . 'đ',
                    'Status' => $order['Status'],
                    'Description' => $order['Description'],
                ];
            }),
            'admin_url' => url('/admin/m-shop-keeper-orders'),
            'note' => 'Đây là mock data để test admin panel MShopKeeper Orders'
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'error' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
        ], 500);
    }
});



// Admin page để xem đơn đặt hàng MShopKeeper (route chính)
Route::get('/admin/m-shop-keeper-orders', function () {
    return redirect('/admin/mshopkeeper-orders');
});

// Admin page để xem đơn đặt hàng MShopKeeper
Route::get('/admin/mshopkeeper-orders', function () {
    try {
        // Mock data cho đơn đặt hàng MShopKeeper
        $mockOrders = [
            [
                'OrderId' => 'dd4a1745-4d42-48d9-aeb7-a202294983b2',
                'OrderNo' => 'DT000025',
                'OrderDate' => '2025-08-19T05:30:00+07:00',
                'TotalAmount' => 28500.0,
                'Status' => 'Pending',
                'Description' => 'Đơn hàng từ Quick Order Modal',
                'Customer' => [
                    'Name' => 'Debug OrderNo Customer',
                    'Tel' => '0999888777',
                    'Email' => 'test-orderno@debug.com',
                    'Address' => 'Hưng Lợi, Ninh Kiều, Cần Thơ'
                ],
                'OrderDetails' => [
                    [
                        'ProductCode' => '188108207',
                        'ProductName' => 'Sữa tươi Milk SECRET nguyên chất tiệt trùng 3.5% Fat - Ba Lan (12h/Th)',
                        'Quantity' => 1.0,
                        'SellingPrice' => 28500.0,
                        'Amount' => 28500.0
                    ]
                ]
            ],
            [
                'OrderId' => 'dd4a1745-4d42-48d9-aeb7-a202294983b3',
                'OrderNo' => 'DT000024',
                'OrderDate' => '2025-08-19T05:46:00+07:00',
                'TotalAmount' => 3500.0,
                'Status' => 'Pending',
                'Description' => 'Test để hệ thống tự sinh OrderNo',
                'Customer' => [
                    'Name' => 'Test Customer',
                    'Tel' => '0123456789',
                    'Email' => 'test@example.com',
                    'Address' => 'Test Address'
                ],
                'OrderDetails' => [
                    [
                        'ProductCode' => '188108207',
                        'ProductName' => 'Bàn chải chà khe hở, góc chết',
                        'Quantity' => 1.0,
                        'SellingPrice' => 3500.0,
                        'Amount' => 3500.0
                    ]
                ]
            ]
        ];

        return view('admin.mshopkeeper-orders', [
            'orders' => $mockOrders,
            'title' => 'Đơn đặt hàng MShopKeeper'
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'error' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
        ], 500);
    }
});

// Manually add missing columns
Route::get('/fix-orders-table', function () {
    try {
        \Illuminate\Support\Facades\DB::statement('
            ALTER TABLE orders
            ADD COLUMN mshopkeeper_order_id VARCHAR(255) NULL COMMENT "OrderId từ MShopKeeper API" AFTER note,
            ADD COLUMN mshopkeeper_order_no VARCHAR(255) NULL COMMENT "OrderNo từ MShopKeeper API" AFTER mshopkeeper_order_id,
            ADD COLUMN mshopkeeper_customer_id BIGINT UNSIGNED NULL COMMENT "ID của MShopKeeper customer" AFTER mshopkeeper_order_no
        ');

        \Illuminate\Support\Facades\DB::statement('
            ALTER TABLE orders
            ADD INDEX idx_mshopkeeper_order_id (mshopkeeper_order_id),
            ADD INDEX idx_mshopkeeper_order_no (mshopkeeper_order_no),
            ADD INDEX idx_mshopkeeper_customer_id (mshopkeeper_customer_id)
        ');

        return response()->json([
            'success' => true,
            'message' => 'Orders table fixed successfully',
            'columns_after' => \Illuminate\Support\Facades\Schema::getColumnListing('orders'),
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'error' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
        ], 500);
    }
});

// Check logs for order creation errors
Route::get('/debug-order-logs', function () {
    try {
        $logFile = storage_path('logs/laravel.log');

        if (!file_exists($logFile)) {
            return response()->json(['error' => 'Log file not found']);
        }

        $logs = file_get_contents($logFile);
        $lines = explode("\n", $logs);

        // Get last 50 lines that contain "order" or "Order"
        $orderLogs = array_filter($lines, function($line) {
            return stripos($line, 'order') !== false || stripos($line, 'local database') !== false;
        });

        $recentOrderLogs = array_slice($orderLogs, -20);

        return response()->json([
            'recent_order_logs' => $recentOrderLogs,
            'log_file_size' => filesize($logFile),
            'log_file_exists' => true,
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'error' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
        ], 500);
    }
});

// Test Quick Order with local saving
Route::get('/test-quick-order-local', function () {
    try {
        // Create test customer
        $customer = \App\Models\MShopKeeperCustomer::firstOrCreate([
            'email' => 'test-local@debug.com'
        ], [
            'mshopkeeper_id' => 'debug-local-' . time(),
            'code' => 'DEBUG_LOCAL_CUSTOMER',
            'name' => 'Debug Local Customer',
            'tel' => '0987654321',
            'addr' => 'Debug Local Address',
            'email' => 'test-local@debug.com',
            'sync_status' => 'synced',
        ]);

        // Login as test customer
        auth('mshopkeeper_customer')->login($customer);

        // Add test product to cart
        $cartService = app(\App\Services\MShopKeeperCartService::class);
        $product = \App\Models\MShopKeeperInventoryItem::first();
        if ($product) {
            $cartService->addToCart($product->id, 2);
        }

        // Test order service with local saving
        $orderService = app(\App\Services\MShopKeeperOrderService::class);
        $orderData = [
            'shipping_address' => 'Test local saving address',
            'payment_method' => 'cod',
            'note' => 'Test order with local database saving',
        ];

        $result = $orderService->createOrderFromCart($orderData);

        return response()->json([
            'success' => $result['success'] ?? false,
            'message' => $result['message'] ?? 'No message',
            'mshopkeeper_order' => $result['order'] ?? null,
            'local_order' => $result['local_order'] ?? null,
            'customer' => [
                'id' => $customer->id,
                'name' => $customer->name,
                'tel' => $customer->tel,
                'addr' => $customer->addr,
            ]
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'error' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString()
        ], 500);

    } catch (\Exception $e) {
        return response()->json([
            'error' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
        ], 500);
    }
});





// Thêm route cho bài viết và dịch vụ
Route::controller(PostController::class)->group(function () {
    Route::get('/danh-muc-bai-viet/{slug}', 'category')->name('posts.category');
    Route::get('/danh-muc-bai-viet', 'categories')->name('posts.categories');
    Route::get('/bai-viet/{slug}', 'show')->name('posts.show');

    // Route tổng thể cho tất cả bài viết với filter
    Route::get('/bai-viet', 'index')->name('posts.index');

    // Redirect các route cũ về trang filter tổng thể
    Route::get('/dich-vu', function() {
        return redirect()->route('posts.index', ['type' => 'service']);
    })->name('posts.services');

    Route::get('/tin-tuc', function() {
        return redirect()->route('posts.index', ['type' => 'news']);
    })->name('posts.news');

    Route::get('/khoa-hoc', function() {
        return redirect()->route('posts.index', ['type' => 'course']);
    })->name('posts.courses');
});

// Thêm route cho nhân viên
Route::controller(EmployeeController::class)->group(function () {
    // Route danh sách nhân viên - yêu cầu đăng nhập
    Route::get('/nhan-vien', 'index')->name('employee.index')->middleware('auth');

    // Route profile công khai
    Route::get('/nhan-vien/{slug}', 'profile')->name('employee.profile');
    Route::get('/nhan-vien/{slug}/qr-code', 'showQrCode')->name('employee.qr-code');
    Route::get('/nhan-vien/{slug}/qr-download', 'downloadQrCode')->name('employee.qr-download');
});

// Routes cho customer authentication - MShopKeeper
Route::controller(CustomerAuthController::class)->group(function () {
    // DISABLED: Không còn sử dụng trang đăng nhập riêng biệt - chỉ dùng popup
    // Route::get('/khach-hang/dang-nhap', 'showLoginForm')->name('customer.login')->middleware('mshopkeeper.guest');
    // Route::post('/khach-hang/dang-nhap', 'login')->middleware('mshopkeeper.guest');
    // Route::get('/khach-hang/dang-ky', 'showRegisterForm')->name('customer.register')->middleware('mshopkeeper.guest');
    // Route::post('/khach-hang/dang-ky', 'register')->middleware('mshopkeeper.guest');

    // Route::get('/khach-hang/tao-mat-khau', 'showCreatePasswordForm')->name('customer.create-password');
    // Route::post('/khach-hang/tao-mat-khau', 'createPassword')->name('customer.create-password.store');
    Route::post('/khach-hang/dang-xuat', 'logout')->name('customer.logout')->middleware('mshopkeeper.auth');
    Route::get('/khach-hang/thong-tin', 'showProfile')->name('customer.profile')->middleware('mshopkeeper.auth');
});

// Routes riêng cho Modal Authentication - tránh xung đột với form chính
Route::prefix('modal')->controller(ModalAuthController::class)->group(function () {
    Route::post('/dang-nhap', 'login')->name('modal.login')->middleware('mshopkeeper.guest');
    Route::post('/dang-ky', 'register')->name('modal.register')->middleware('mshopkeeper.guest');
    Route::post('/tao-mat-khau', 'createPassword')->name('modal.create-password');
});

// Test route removed - functionality working

// API Routes cho realtime customer checking
Route::prefix('api/customer')->group(function () {
    Route::get('/check-phone/{phone}', [CustomerAuthController::class, 'checkPhone'])
        ->name('api.customer.check-phone');

    Route::post('/verify-identity', [CustomerAuthController::class, 'verifyIdentity'])
        ->name('api.customer.verify-identity');

    Route::post('/create-password-verified', [CustomerAuthController::class, 'createPasswordVerified'])
        ->name('api.customer.create-password-verified');
});

// API Routes cho auth status
Route::prefix('api')->group(function () {
    Route::get('/auth/status', function () {
        return response()->json([
            'authenticated' => Auth::guard('mshopkeeper_customer')->check(),
            'user' => Auth::guard('mshopkeeper_customer')->user()
        ]);
    });
});

// Test route
Route::get('/test-auth', function () {
    return response()->file(base_path('test_auth_flow.html'));
});

// Fallback route cho customer.login - redirect về trang chủ với popup
Route::get('/khach-hang/dang-nhap', function () {
    return redirect('/')->with('show_login_popup', true);
})->name('customer.login');



// Routes cho password reset
Route::controller(PasswordResetController::class)->group(function () {
    Route::get('/khach-hang/quen-mat-khau', 'showResetRequestForm')->name('customer.password.request')->middleware('mshopkeeper.guest');
    Route::post('/khach-hang/quen-mat-khau', 'sendResetLink')->name('customer.password.email')->middleware('mshopkeeper.guest');
    Route::get('/khach-hang/dat-lai-mat-khau/{token}', 'showResetForm')->name('customer.password.reset');
    Route::post('/khach-hang/dat-lai-mat-khau', 'resetPassword')->name('customer.password.update');
    Route::get('/khach-hang/doi-mat-khau', 'showChangePasswordForm')->name('customer.password.change')->middleware('mshopkeeper.auth');
    Route::post('/khach-hang/doi-mat-khau', 'changePassword')->name('customer.password.change.store')->middleware('mshopkeeper.auth');
});



// Routes cho customer orders (yêu cầu đăng nhập)
Route::middleware('mshopkeeper.auth')->group(function () {
    Route::controller(CustomerOrderController::class)->group(function () {
        Route::get('/khach-hang/don-hang', 'index')->name('customer.orders.index');
        Route::get('/khach-hang/don-hang/{orderNumber}', 'show')->name('customer.orders.show');
        Route::patch('/khach-hang/don-hang/{orderNumber}/huy', 'cancel')->name('customer.orders.cancel');
    });
});

// SEO routes
Route::controller(SitemapController::class)->group(function () {
    Route::get('/sitemap.xml', 'index')->name('sitemap');
    Route::get('/robots.txt', 'robots')->name('robots');
});

// Routes tìm kiếm
Route::controller(SearchController::class)->group(function () {
    Route::get('/tim-kiem', 'all')->name('search.all');
    Route::get('/tim-kiem/san-pham', 'products')->name('products.search'); // Redirect to MSKeeper
    Route::get('/tim-kiem/bai-viet', 'posts')->name('posts.search');
});

// Route clear cache
Route::post('/clear-cache', function () {
    \App\Providers\ViewServiceProvider::refreshCache('navigation');
    return response()->json(['message' => 'Cache cleared successfully!']);
})->name('clear.cache');

Route::get('/run-storage-link', function () {
    try {
        Artisan::call('storage:link');
        return response()->json(['message' => 'Storage linked successfully!'], 200);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
});

// MShopKeeper API Test Routes
Route::prefix('test-mshopkeeper')->controller(MShopKeeperTestController::class)->group(function () {
    Route::get('/', 'index')->name('mshopkeeper.test.index');
    Route::get('/auth', 'testAuthentication')->name('mshopkeeper.test.auth');
    Route::get('/categories', 'testCategories')->name('mshopkeeper.test.categories');
    Route::get('/categories-tree', 'testCategoriesTree')->name('mshopkeeper.test.categories-tree');
    Route::get('/branchs', 'testBranchs')->name('mshopkeeper.test.branchs');
    Route::get('/customers', 'testCustomers')->name('mshopkeeper.test.customers');
    Route::get('/member-levels', 'testMemberLevels')->name('mshopkeeper.test.member-levels');

    // Customer APIs - Tìm kiếm và thông tin khách hàng
    Route::get('/customers-by-info', 'testCustomersByInfo')->name('mshopkeeper.test.customers-by-info');
    Route::get('/customers-lomas-search', 'testLomasCustomerSearch')->name('mshopkeeper.test.customers-lomas-search');
    Route::get('/customers-point-paging', 'testCustomersPointPaging')->name('mshopkeeper.test.customers-point-paging');

    Route::get('/full-test', 'fullTest')->name('mshopkeeper.test.full');
    Route::get('/clear-cache', 'clearCache')->name('mshopkeeper.test.clear-cache');
});

// MShopKeeper HTML Test Dashboard
Route::get('/mshopkeeper-dashboard', function () {
    return view('mshopkeeper-test');
})->name('mshopkeeper.dashboard');

// Debug registration route
Route::get('/debug-registration/{phone}', function ($phone) {
    try {
        $authService = app(\App\Services\MShopKeeperCustomerAuthService::class);

        // Find customer by phone
        $customer = \App\Models\MShopKeeperCustomer::where('tel', $phone)->first();

        if (!$customer) {
            return response()->json(['error' => 'Customer not found'], 404);
        }

        $userData = [
            'name' => $customer->name,
            'phone' => $customer->tel,
            'email' => $customer->email,
            'password' => 'test123456',
            'gender' => $customer->gender,
            'address' => $customer->address,
            'identify_number' => $customer->identify_number,
        ];

        $result = $authService->register($userData);

        return response()->json([
            'customer_info' => [
                'id' => $customer->id,
                'name' => $customer->name,
                'phone' => $customer->tel,
                'has_password' => $customer->hasPassword()
            ],
            'registration_result' => $result
        ], 200, [], JSON_PRETTY_PRINT);

    } catch (\Exception $e) {
        return response()->json([
            'error' => $e->getMessage(),
            'trace' => explode("\n", $e->getTraceAsString())
        ], 500, [], JSON_PRETTY_PRINT);
    }
})->name('debug.registration');

// Debug MShopKeeper API response
Route::get('/debug-mshopkeeper-api/{phone}', function ($phone) {
    try {
        $mshopkeeperService = app(\App\Services\MShopKeeperService::class);

        // Test getCustomersByInfo
        $result = $mshopkeeperService->getCustomersByInfo($phone);

        return response()->json([
            'phone' => $phone,
            'api_result' => $result,
            'customers_count' => isset($result['data']['customers']) ? count($result['data']['customers']) : 0,
            'first_customer' => isset($result['data']['customers'][0]) ? $result['data']['customers'][0] : null,
            'first_customer_keys' => isset($result['data']['customers'][0]) ? array_keys($result['data']['customers'][0]) : []
        ], 200, [], JSON_PRETTY_PRINT);

    } catch (\Exception $e) {
        return response()->json([
            'error' => $e->getMessage(),
            'trace' => explode("\n", $e->getTraceAsString())
        ], 500, [], JSON_PRETTY_PRINT);
    }
})->name('debug.mshopkeeper.api');



// Debug route - tạm thời
Route::get('/debug-products', function () {
    $product = \App\Models\Product::with('images')->where('is_hot', true)->first();
    if ($product) {
        $data = [
            'product_name' => $product->name,
            'images_count' => $product->images->count(),
            'images' => $product->images->map(function($image) {
                return [
                    'id' => $image->id,
                    'image_link' => $image->image_link,
                    'is_main' => $image->is_main,
                    'status' => $image->status,
                    'order' => $image->order
                ];
            }),
            'getProductImageUrl_result' => getProductImageUrl($product)
        ];
        return response()->json($data, 200, [], JSON_PRETTY_PRINT);
    }
    return response()->json(['message' => 'No hot products found'], 404);
});

// Debug post route
Route::get('/debug-post/{id}', function ($id) {
    $post = \App\Models\Post::find($id);
    if ($post) {
        $data = [
            'id' => $post->id,
            'title' => $post->title,
            'slug' => $post->slug,
            'status' => $post->status,
            'type' => $post->type,
            'show_thumbnail' => $post->show_thumbnail,
            'thumbnail' => $post->thumbnail,
            'content_length' => strlen($post->content ?? ''),
            'content_builder_count' => is_array($post->content_builder) ? count($post->content_builder) : 0,
            'content_builder_data' => $post->content_builder,
            'updated_at' => $post->updated_at,
            'route_url' => route('posts.show', $post->slug)
        ];
        return response()->json($data, 200, [], JSON_PRETTY_PRINT);
    }
    return response()->json(['message' => 'Post not found'], 404);
});

// Debug partners route
Route::get('/debug-partners', function() {
    $partners = \App\Models\Partner::all();
    $activePartners = \App\Models\Partner::where('status', 'active')->orderBy('order')->get();

    return response()->json([
        'total_partners' => $partners->count(),
        'active_partners' => $activePartners->count(),
        'all_partners' => $partners->map(function($p) {
            return [
                'id' => $p->id,
                'name' => $p->name,
                'status' => $p->status,
                'order' => $p->order,
                'logo_link' => $p->logo_link
            ];
        }),
        'active_partners_data' => $activePartners->map(function($p) {
            return [
                'id' => $p->id,
                'name' => $p->name,
                'status' => $p->status,
                'order' => $p->order,
                'logo_link' => $p->logo_link
            ];
        })
    ], 200, [], JSON_PRETTY_PRINT);
});

// Debug route để tìm lỗi ::class
Route::get('/debug-class-error', function () {
    try {
        // Test 1: Kiểm tra các service providers
        $providers = app()->getLoadedProviders();
        $results = ['providers_loaded' => count($providers)];

        // Test 2: Kiểm tra MShopKeeperService
        try {
            $serviceClass = \App\Services\MShopKeeperService::class;
            $results['mshopkeeper_service_class'] = $serviceClass;
            $results['mshopkeeper_service_exists'] = class_exists($serviceClass);

            if (class_exists($serviceClass)) {
                $service = app($serviceClass);
                $results['mshopkeeper_service_resolved'] = true;
                $results['mshopkeeper_service_type'] = get_class($service);
            }
        } catch (\Throwable $e) {
            $results['mshopkeeper_service_error'] = [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ];
        }

        // Test 3: Kiểm tra Filament resources
        try {
            $resourceClass = \App\Filament\Admin\Resources\MShopKeeperCustomerResource::class;
            $results['resource_class'] = $resourceClass;
            $results['resource_exists'] = class_exists($resourceClass);
        } catch (\Throwable $e) {
            $results['resource_error'] = [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ];
        }

        // Test 4: Kiểm tra static::class trong các context khác nhau
        try {
            $testClass = new class {
                public static function getClass() {
                    return static::class;
                }
            };
            $results['static_class_test'] = $testClass::getClass();
        } catch (\Throwable $e) {
            $results['static_class_error'] = [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ];
        }

        return response()->json($results, 200, [], JSON_PRETTY_PRINT);

    } catch (\Throwable $e) {
        return response()->json([
            'error' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => explode("\n", $e->getTraceAsString())
        ], 500, [], JSON_PRETTY_PRINT);
    }
});

// Debug route để test Filament resource trực tiếp
Route::get('/debug-filament-resource', function () {
    try {
        $results = [];

        // Test 1: Kiểm tra resource class
        $resourceClass = \App\Filament\Admin\Resources\MShopKeeperCustomerResource::class;
        $results['resource_class'] = $resourceClass;
        $results['resource_exists'] = class_exists($resourceClass);

        // Test 2: Thử gọi các static methods
        try {
            $results['can_access'] = $resourceClass::canAccess();
        } catch (\Throwable $e) {
            $results['can_access_error'] = [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ];
        }

        // Test 3: Thử gọi getNavigationGroup
        try {
            $results['navigation_group'] = $resourceClass::getNavigationGroup();
        } catch (\Throwable $e) {
            $results['navigation_group_error'] = [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ];
        }

        // Test 4: Thử khởi tạo ListMShopKeeperCustomers
        try {
            $pageClass = \App\Filament\Admin\Resources\MShopKeeperCustomerResource\Pages\ListMShopKeeperCustomers::class;
            $results['page_class'] = $pageClass;
            $results['page_exists'] = class_exists($pageClass);

            // Thử tạo instance
            $page = new $pageClass();
            $results['page_created'] = true;
            $results['page_type'] = get_class($page);
        } catch (\Throwable $e) {
            $results['page_error'] = [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => array_slice(explode("\n", $e->getTraceAsString()), 0, 10)
            ];
        }

        return response()->json($results, 200, [], JSON_PRETTY_PRINT);

    } catch (\Throwable $e) {
        return response()->json([
            'error' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => explode("\n", $e->getTraceAsString())
        ], 500, [], JSON_PRETTY_PRINT);
    }
});

// Debug route để test admin path trực tiếp
Route::get('/debug-admin-path', function () {
    try {
        // Simulate admin request
        $results = [];
        $results['current_url'] = request()->url();
        $results['path'] = request()->path();
        $results['is_admin_path'] = str_starts_with(request()->path(), 'admin');

        // Test Filament panel
        try {
            $panel = \Filament\Facades\Filament::getCurrentPanel();
            $results['current_panel'] = $panel ? $panel->getId() : 'null';
        } catch (\Throwable $e) {
            $results['panel_error'] = $e->getMessage();
        }

        // Test auth
        try {
            $user = \Illuminate\Support\Facades\Auth::user();
            $results['auth_user'] = $user ? $user->id : 'null';
        } catch (\Throwable $e) {
            $results['auth_error'] = $e->getMessage();
        }

        return response()->json($results, 200, [], JSON_PRETTY_PRINT);

    } catch (\Throwable $e) {
        return response()->json([
            'error' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine()
        ], 500, [], JSON_PRETTY_PRINT);
    }
});

// Debug route để simulate chính xác request admin/mshopkeeper-customers
Route::get('/debug-simulate-admin-request', function () {
    try {
        $results = [];

        // Test 1: Simulate admin path
        $results['simulated_path'] = 'admin/mshopkeeper-customers';

        // Test 2: Check if Filament is trying to resolve resources
        try {
            // Get all registered Filament resources
            $panel = \Filament\Facades\Filament::getDefaultPanel();
            $results['panel_id'] = $panel ? $panel->getId() : 'null';

            if ($panel) {
                $resources = $panel->getResources();
                $results['resources_count'] = count($resources);
                $results['resources'] = array_keys($resources);

                // Check if our problematic resource is in the list
                $problematicResource = \App\Filament\Admin\Resources\MShopKeeperCustomerResource::class;
                $results['problematic_resource_registered'] = in_array($problematicResource, $resources);
            }
        } catch (\Throwable $e) {
            $results['filament_error'] = [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ];
        }

        // Test 3: Try to manually trigger resource discovery
        try {
            $resourceClass = \App\Filament\Admin\Resources\MShopKeeperCustomerResource::class;

            // Test các static methods có thể được gọi
            $results['resource_tests'] = [];

            // Test getUrl
            try {
                $url = $resourceClass::getUrl();
                $results['resource_tests']['getUrl'] = $url;
            } catch (\Throwable $e) {
                $results['resource_tests']['getUrl_error'] = $e->getMessage();
            }

            // Test getRouteBaseName
            try {
                $routeBaseName = $resourceClass::getRouteBaseName();
                $results['resource_tests']['getRouteBaseName'] = $routeBaseName;
            } catch (\Throwable $e) {
                $results['resource_tests']['getRouteBaseName_error'] = $e->getMessage();
            }

            // Test getPages
            try {
                $pages = $resourceClass::getPages();
                $results['resource_tests']['getPages'] = array_keys($pages);
            } catch (\Throwable $e) {
                $results['resource_tests']['getPages_error'] = $e->getMessage();
            }

        } catch (\Throwable $e) {
            $results['resource_manual_test_error'] = [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ];
        }

        return response()->json($results, 200, [], JSON_PRETTY_PRINT);

    } catch (\Throwable $e) {
        return response()->json([
            'error' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => array_slice(explode("\n", $e->getTraceAsString()), 0, 10)
        ], 500, [], JSON_PRETTY_PRINT);
    }
});


















