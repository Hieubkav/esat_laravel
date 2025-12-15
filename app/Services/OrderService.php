<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\MShopKeeperCart;
use App\Models\Customer;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderService
{
    /**
     * Tạo đơn hàng từ giỏ hàng MShopKeeper
     */
    public function createOrderFromCart($customerId, $orderData = [])
    {
        try {
            DB::beginTransaction();

            // Lấy giỏ hàng
            $cart = MShopKeeperCart::where('customer_id', $customerId)
                ->with(['items.product'])
                ->first();

            if (!$cart || $cart->items->isEmpty()) {
                throw new \Exception('Giỏ hàng trống hoặc không tồn tại');
            }

            // Lấy thông tin khách hàng
            $customer = Customer::find($customerId);
            if (!$customer) {
                throw new \Exception('Khách hàng không tồn tại');
            }

            // Tạo đơn hàng
            $order = Order::create([
                'customer_id' => $customerId,
                'order_number' => $this->generateOrderNumber(),
                'total' => $cart->total_price, // Sử dụng total_price thay vì total
                'status' => 'pending',
                'payment_method' => $orderData['payment_method'] ?? 'cod',
                'payment_status' => 'pending',
                'shipping_name' => $orderData['shipping_name'] ?? $customer->name,
                'shipping_phone' => $orderData['shipping_phone'] ?? $customer->phone,
                'shipping_email' => $orderData['shipping_email'] ?? $customer->email,
                'shipping_address' => $orderData['shipping_address'] ?? $customer->address,
                'note' => $orderData['note'] ?? null,
            ]);

            // Tạo order items từ cart items
            foreach ($cart->items as $cartItem) {
                $productPrice = $cartItem->product->selling_price ?? 0;
                $subtotal = $cartItem->quantity * $productPrice;

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => null, // Không sử dụng products cũ
                    'mshopkeeper_product_id' => $cartItem->product_id, // Sử dụng MShopKeeper product ID
                    'product_name' => $cartItem->product->name ?? 'Sản phẩm không xác định',
                    'product_code' => $cartItem->product->code ?? null,
                    'quantity' => $cartItem->quantity,
                    'price' => $productPrice,
                    'subtotal' => $subtotal,
                ]);
            }

            // Tính lại tổng tiền
            $order->calculateTotal();

            DB::commit();

            return [
                'success' => true,
                'order' => $order,
                'message' => 'Đơn hàng đã được tạo thành công'
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Tạo mã đơn hàng duy nhất
     */
    private function generateOrderNumber()
    {
        do {
            $orderNumber = 'DH' . date('Ymd') . strtoupper(Str::random(4));
        } while (Order::where('order_number', $orderNumber)->exists());

        return $orderNumber;
    }

    /**
     * Lấy đơn hàng của khách hàng
     */
    public function getCustomerOrders($customerId, $status = null, $limit = 10)
    {
        $query = Order::where('customer_id', $customerId)
            ->with(['items.product'])
            ->orderBy('created_at', 'desc');

        if ($status) {
            $query->where('status', $status);
        }

        return $query->paginate($limit);
    }

    /**
     * Lấy thống kê đơn hàng của khách hàng
     */
    public function getCustomerOrderStats($customerId)
    {
        return [
            'total' => Order::where('customer_id', $customerId)->count(),
            'pending' => Order::where('customer_id', $customerId)->where('status', 'pending')->count(),
            'processing' => Order::where('customer_id', $customerId)->whereIn('status', ['confirmed', 'processing', 'shipping'])->count(),
            'completed' => Order::where('customer_id', $customerId)->where('status', 'delivered')->count(),
            'cancelled' => Order::where('customer_id', $customerId)->whereIn('status', ['cancelled', 'refunded'])->count(),
        ];
    }

    /**
     * Hủy đơn hàng (chỉ cho phép hủy đơn pending)
     */
    public function cancelOrder($orderId, $customerId)
    {
        try {
            $order = Order::where('id', $orderId)
                ->where('customer_id', $customerId)
                ->where('status', 'pending')
                ->first();

            if (!$order) {
                throw new \Exception('Không thể hủy đơn hàng này');
            }

            $order->update(['status' => 'cancelled']);

            return [
                'success' => true,
                'message' => 'Đã hủy đơn hàng thành công'
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }
}
