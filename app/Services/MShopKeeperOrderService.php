<?php

namespace App\Services;

use App\Services\MShopKeeperService;
use App\Services\MShopKeeperCartService;
use App\Services\MShopKeeperCustomerAuthService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class MShopKeeperOrderService
{
    protected $mshopkeeperService;
    protected $cartService;
    protected $authService;
    
    public function __construct(
        MShopKeeperService $mshopkeeperService,
        MShopKeeperCartService $cartService,
        MShopKeeperCustomerAuthService $authService
    ) {
        $this->mshopkeeperService = $mshopkeeperService;
        $this->cartService = $cartService;
        $this->authService = $authService;
    }
    
    /**
     * Tạo đơn hàng trực tiếp lên MShopKeeper (không lưu nội bộ)
     */
    public function createOrderFromCart($orderData = [])
    {
        try {
            // 1. Kiểm tra đăng nhập
            if (!$this->authService->check()) {
                Log::warning('User not authenticated for order creation');
                throw new \Exception('Vui lòng đăng nhập để đặt hàng');
            }

            $customer = $this->authService->user();
            if (!$customer) {
                Log::warning('Customer not found after authentication check');
                throw new \Exception('Không tìm thấy thông tin khách hàng');
            }

            Log::info('Customer authenticated for order', [
                'customer_id' => $customer->id,
                'customer_name' => $customer->name,
                'customer_tel' => $customer->tel,
                'customer_addr' => $customer->addr,
            ]);
            
            // 2. Lấy giỏ hàng
            $cart = $this->cartService->getCart();
            if (!$cart || $cart->items->isEmpty()) {
                Log::warning('Order creation failed: Empty cart', [
                    'customer_id' => $customer->id,
                    'cart_exists' => $cart ? 'yes' : 'no',
                    'cart_items_count' => $cart ? $cart->items->count() : 0
                ]);

                return [
                    'success' => false,
                    'message' => 'Giỏ hàng của bạn đang trống. Vui lòng thêm sản phẩm trước khi đặt hàng.'
                ];
            }

            // 3. Validate giỏ hàng
            $validation = $this->cartService->validateCart();
            if (!$validation['valid']) {
                Log::warning('Order creation failed: Invalid cart', [
                    'customer_id' => $customer->id,
                    'validation_message' => $validation['message']
                ]);

                return [
                    'success' => false,
                    'message' => 'Giỏ hàng có sản phẩm không hợp lệ: ' . $validation['message']
                ];
            }
            
            // 4. Build payload cho MShopKeeper
            $orderPayload = $this->buildMShopKeeperOrderPayload($customer, $cart, $orderData);

            Log::info('MShopKeeper order payload', [
                'payload' => $orderPayload,
                'customer_id' => $customer->id,
                'cart_items_count' => $cart->items->count()
            ]);

            // Validate payload trước khi gửi
            $validation = $this->validateOrderPayload($orderPayload);
            if (!$validation['valid']) {
                Log::error('Order payload validation failed', [
                    'errors' => $validation['errors'],
                    'payload' => $orderPayload
                ]);

                return [
                    'success' => false,
                    'message' => 'Dữ liệu đơn hàng không hợp lệ: ' . implode(', ', $validation['errors'])
                ];
            }

            // 5. Tạo đơn hàng trực tiếp lên MShopKeeper
            $result = $this->mshopkeeperService->createOrder($orderPayload);

            Log::info('MShopKeeper API response', [
                'result' => $result,
                'customer_id' => $customer->id
            ]);

            if (!$result['success']) {
                $errorMessage = $result['error']['message'] ?? $result['message'] ?? 'Lỗi không xác định từ MShopKeeper API';

                Log::error('MShopKeeper order creation failed', [
                    'error_message' => $errorMessage,
                    'full_result' => $result,
                    'customer_id' => $customer->id
                ]);

                return [
                    'success' => false,
                    'message' => 'Không thể tạo đơn hàng: ' . $errorMessage
                ];
            }
            
            // 6. Lưu đơn hàng vào database local
            $localOrder = $this->saveOrderToLocal($customer, $cart, $orderData, $result['data']);

            // 7. Xóa giỏ hàng sau khi đặt hàng thành công
            $this->cartService->clearCart();

            Log::info('Order created successfully in MShopKeeper and saved locally', [
                'mshopkeeper_order_id' => $result['data']['OrderId'],
                'mshopkeeper_order_no' => $result['data']['OrderNo'],
                'local_order_id' => $localOrder?->id,
                'local_order_number' => $localOrder?->order_number,
                'local_order_saved' => $localOrder !== null,
                'customer_id' => $customer->id
            ]);

            return [
                'success' => true,
                'order' => $result['data'],
                'local_order' => $localOrder,
                'message' => 'Đặt hàng thành công!'
            ];
            
        } catch (\Exception $e) {
            Log::error('Create order failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'customer' => $customer ?? null,
                'order_data' => $orderData,
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return [
                'success' => false,
                'message' => 'Lỗi tạo đơn hàng: ' . $e->getMessage() . ' (Line: ' . $e->getLine() . ')'
            ];
        }
    }
    
    /**
     * Build payload cho MShopKeeper Order API
     */
    private function buildMShopKeeperOrderPayload($customer, $cart, $orderData)
    {
        $items = $cart->items;
        $totalAmount = $items->sum('subtotal');
        
        // Không gửi OrderNo - để hệ thống MShopKeeper tự sinh
        // Theo API doc: OrderNo không bắt buộc, hệ thống sẽ tự sinh với format DT + số

        // Parse địa chỉ (tạm thời dùng default)
        $defaultProvince = 'VN101'; // Hà Nội
        $defaultDistrict = 'VN10113'; // Cầu Giấy
        $defaultWard = 'VN1011303'; // Dịch Vọng

        return [
            // OrderNo bỏ đi - để hệ thống tự sinh
            'OrderDate' => now()->toISOString(),
            'DiscountRate' => 0.0,
            'DiscountAmount' => 0.0,
            'ScopeOfApplication' => 1,
            'TotalAmount' => (float) $totalAmount,
            'ToProvinceOrCityId' => $defaultProvince,
            'ToProvinceOrCityName' => 'Hà Nội',
            'ToDistrictId' => $defaultDistrict,
            'ToDistrictName' => 'Quận Cầu Giấy',
            'ToWardOrCommuneId' => $defaultWard,
            'ToWardOrCommuneName' => 'Phường Dịch Vọng',
            'Description' => $orderData['note'] ?? 'Đơn hàng từ website',
            'Customer' => [
                'Id' => $customer->mshopkeeper_id ?: Str::uuid(),
                'Gender' => $customer->gender ?? 0,
                'Code' => $customer->code ?: 'CUST_' . $customer->id,
                'Name' => $orderData['shipping_name'] ?? $customer->name,
                'Tel' => $orderData['shipping_phone'] ?? $customer->tel,
                'Email' => $orderData['shipping_email'] ?? $customer->email,
                'Address' => $orderData['shipping_address'] ?? $customer->addr
            ],
            'OrderDetails' => $this->buildOrderDetails($items),
            'OrderDelivery' => [
                'Weight' => 1000, // Default 1kg
                'Length' => 10,
                'Width' => 10, 
                'Height' => 10,
                'DeliveryAmount' => 0.0, // Free shipping
                'Receiver' => [
                    'Id' => Str::uuid(),
                    'Gender' => 0,
                    'Code' => 'RECV_' . time(),
                    'Name' => $orderData['shipping_name'] ?? $customer->name,
                    'Tel' => $orderData['shipping_phone'] ?? $customer->tel,
                    'Email' => $orderData['shipping_email'] ?? $customer->email,
                    'Address' => $orderData['shipping_address'] ?? $customer->addr
                ]
            ],
            'BranchId' => $this->getValidBranchId()
        ];
    }
    
    private function buildOrderDetails($items)
    {
        $details = [];
        $sortOrder = 1;
        
        foreach ($items as $item) {
            $product = $item->product;
            
            $details[] = [
                'ProductId' => $product->mshopkeeper_id ?: Str::uuid(),
                'ProductType' => 1, // Hàng hóa
                'ProductCode' => $product->code ?: 'PROD_' . $product->id,
                'ProductName' => $product->name,
                'Quantity' => (float) $item->quantity,
                'SellingPrice' => (float) $product->selling_price,
                'Amount' => (float) $item->subtotal,
                'DiscountAmount' => 0.0,
                'DiscountRate' => 0.0,
                'SortOrder' => $sortOrder++,
                'UnitId' => $product->unit_id ?: '746EC67B-D56B-4DF7-8187-2FB7FCF31216'
            ];
        }
        
        return $details;
    }

    /**
     * Lưu đơn hàng vào database local
     */
    private function saveOrderToLocal($customer, $cart, $orderData, $mshopkeeperOrder)
    {
        try {
            Log::info('Starting to save order to local database', [
                'customer_id' => $customer->id,
                'cart_items_count' => $cart->items->count(),
                'mshopkeeper_order_id' => $mshopkeeperOrder['OrderId'] ?? null,
                'mshopkeeper_order_no' => $mshopkeeperOrder['OrderNo'] ?? null,
            ]);

            // Tạo order number cho local
            $orderNumber = 'WEB_' . date('Ymd') . '_' . strtoupper(substr(uniqid(), -4));

            // Tính tổng tiền từ cart
            $totalAmount = $cart->items->sum('subtotal');

            Log::info('Order data prepared', [
                'order_number' => $orderNumber,
                'total_amount' => $totalAmount,
                'payment_method' => $orderData['payment_method'] ?? 'cod',
                'shipping_address' => $orderData['shipping_address'] ?? $customer->addr,
            ]);

            // Tạo đơn hàng local
            $order = \App\Models\Order::create([
                'customer_id' => null, // Không có customer_id vì dùng MShopKeeper customer
                'order_number' => $orderNumber,
                'total' => $totalAmount,
                'status' => 'pending',
                'payment_method' => $orderData['payment_method'] ?? 'cod',
                'shipping_address' => $orderData['shipping_address'] ?? $customer->addr,
                'shipping_name' => $customer->name, // Thêm tên khách hàng
                'shipping_phone' => $customer->tel, // Thêm số điện thoại
                'shipping_email' => $customer->email, // Thêm email
                'note' => $orderData['note'] ?? '',
                // Thêm thông tin MShopKeeper
                'mshopkeeper_order_id' => $mshopkeeperOrder['OrderId'] ?? null,
                'mshopkeeper_order_no' => $mshopkeeperOrder['OrderNo'] ?? null,
                'mshopkeeper_customer_id' => $customer->id,
            ]);

            Log::info('Local order created successfully', [
                'local_order_id' => $order->id,
                'local_order_number' => $order->order_number,
            ]);

            // Tạo order items
            foreach ($cart->items as $cartItem) {
                try {
                    \App\Models\OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => null, // Không có product_id vì dùng MShopKeeper product
                        'product_name' => $cartItem->product ? $cartItem->product->name : 'Unknown Product',
                        'quantity' => $cartItem->quantity,
                        'price' => $cartItem->product ? $cartItem->product->selling_price : 0,
                        'subtotal' => $cartItem->product ? $cartItem->subtotal : 0,
                        // Thêm thông tin MShopKeeper
                        'mshopkeeper_product_id' => $cartItem->product_id,
                        'mshopkeeper_product_code' => $cartItem->product ? $cartItem->product->code : null,
                        'mshopkeeper_product_name' => $cartItem->product ? $cartItem->product->name : null,
                    ]);

                    Log::info('Order item created', [
                        'order_id' => $order->id,
                        'product_id' => $cartItem->product_id,
                        'quantity' => $cartItem->quantity,
                        'price' => $cartItem->product ? $cartItem->product->selling_price : 0,
                    ]);

                } catch (\Exception $itemException) {
                    Log::error('Failed to create order item', [
                        'error' => $itemException->getMessage(),
                        'order_id' => $order->id,
                        'cart_item' => $cartItem,
                        'trace' => $itemException->getTraceAsString()
                    ]);
                    // Continue với items khác
                }
            }

            Log::info('Order saved to local database', [
                'local_order_id' => $order->id,
                'local_order_number' => $order->order_number,
                'mshopkeeper_order_id' => $mshopkeeperOrder['OrderId'] ?? null,
                'mshopkeeper_order_no' => $mshopkeeperOrder['OrderNo'] ?? null,
                'total_amount' => $totalAmount,
                'items_count' => $cart->items->count()
            ]);

            return $order;

        } catch (\Exception $e) {
            Log::error('Failed to save order to local database', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'mshopkeeper_order' => $mshopkeeperOrder,
                'customer_id' => $customer->id,
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'sql_error' => $e instanceof \Illuminate\Database\QueryException ? $e->getSql() : null,
            ]);

            // Không throw exception để không làm fail toàn bộ process
            // Đơn hàng đã tạo thành công trên MShopKeeper rồi
            return null;
        }
    }

    /**
     * Lấy BranchId hợp lệ từ API
     */
    private function getValidBranchId()
    {
        try {
            // Gọi API để lấy danh sách chi nhánh
            $result = $this->mshopkeeperService->getBranches([
                'IsIncludeInactiveBranch' => false,
                'IsIncludeChainOfBranch' => false
            ]);

            if ($result['success'] && !empty($result['data'])) {
                // Lấy chi nhánh đầu tiên hoặc chi nhánh mặc định
                $branches = $result['data'];

                // Ưu tiên kho tổng (IsBaseDepot = true)
                foreach ($branches as $branch) {
                    if ($branch['IsBaseDepot'] ?? false) {
                        Log::info('Using base depot branch', ['branch_id' => $branch['Id'], 'branch_name' => $branch['Name']]);
                        return $branch['Id'];
                    }
                }

                // Nếu không có kho tổng, lấy chi nhánh đầu tiên
                $firstBranch = $branches[0];
                Log::info('Using first available branch', ['branch_id' => $firstBranch['Id'], 'branch_name' => $firstBranch['Name']]);
                return $firstBranch['Id'];
            }

            Log::warning('No branches found, using fallback BranchId');

        } catch (\Exception $e) {
            Log::error('Failed to get branches from API', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }

        // Fallback to config or default
        return config('mshopkeeper.default_branch_id', '8C3D6B0D-3B58-4379-BFFB-1CCEA7A7F884');
    }

    /**
     * Validate order payload theo API spec
     */
    private function validateOrderPayload($payload)
    {
        $errors = [];

        // Validate required fields
        if (empty($payload['BranchId'])) {
            $errors[] = 'BranchId là bắt buộc';
        }

        if (empty($payload['OrderDate'])) {
            $errors[] = 'OrderDate là bắt buộc';
        }

        if (!isset($payload['ScopeOfApplication'])) {
            $errors[] = 'ScopeOfApplication là bắt buộc';
        }

        // Validate Customer
        if (empty($payload['Customer'])) {
            $errors[] = 'Customer là bắt buộc';
        } else {
            $customer = $payload['Customer'];
            if (empty($customer['Id'])) {
                $errors[] = 'Customer.Id là bắt buộc';
            }
            if (empty($customer['Code'])) {
                $errors[] = 'Customer.Code là bắt buộc';
            }
            if (empty($customer['Name'])) {
                $errors[] = 'Customer.Name là bắt buộc';
            }
        }

        // Validate OrderDetails
        if (empty($payload['OrderDetails']) || !is_array($payload['OrderDetails'])) {
            $errors[] = 'OrderDetails là bắt buộc và phải là array';
        } else {
            foreach ($payload['OrderDetails'] as $index => $detail) {
                $prefix = "OrderDetails[$index]";

                if (empty($detail['ProductId'])) {
                    $errors[] = "$prefix.ProductId là bắt buộc";
                }
                if (empty($detail['ProductType'])) {
                    $errors[] = "$prefix.ProductType là bắt buộc";
                }
                if (empty($detail['ProductCode'])) {
                    $errors[] = "$prefix.ProductCode là bắt buộc";
                }
                if (empty($detail['ProductName'])) {
                    $errors[] = "$prefix.ProductName là bắt buộc";
                }
                if (empty($detail['UnitId'])) {
                    $errors[] = "$prefix.UnitId là bắt buộc";
                }
                if (!isset($detail['Quantity']) || $detail['Quantity'] <= 0) {
                    $errors[] = "$prefix.Quantity phải > 0";
                }
                if (!isset($detail['SellingPrice']) || $detail['SellingPrice'] <= 0) {
                    $errors[] = "$prefix.SellingPrice phải > 0";
                }
                if (!isset($detail['Amount']) || $detail['Amount'] <= 0) {
                    $errors[] = "$prefix.Amount phải > 0";
                }
                if (!isset($detail['SortOrder'])) {
                    $errors[] = "$prefix.SortOrder là bắt buộc";
                }
            }
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors
        ];
    }

    /**
     * Lấy thông tin đơn hàng từ MShopKeeper (nếu cần)
     */
    public function getOrderInfo($orderNo)
    {
        // TODO: Implement nếu cần lấy thông tin đơn hàng từ MShopKeeper
        // Hiện tại chưa có API get order by OrderNo
        return [
            'success' => false,
            'message' => 'Chức năng chưa được hỗ trợ'
        ];
    }
}
