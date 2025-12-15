<?php

namespace App\Services;

use App\Models\MShopKeeperCart;
use App\Models\MShopKeeperCartItem;
use App\Models\MShopKeeperInventoryItem;
use App\Models\Customer;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MShopKeeperCartService
{
    /**
     * Thêm sản phẩm vào giỏ hàng
     */
    public function addToCart($productId, $quantity = 1, $customerId = null)
    {
        try {
            DB::beginTransaction();

            // Lấy customer ID
            $customerId = $customerId ?? Auth::guard('mshopkeeper_customer')->id();
            
            if (!$customerId) {
                throw new \Exception('Vui lòng đăng nhập để thêm sản phẩm vào giỏ hàng');
            }

            // Kiểm tra sản phẩm tồn tại và còn hàng
            $product = MShopKeeperInventoryItem::findOrFail($productId);
            
            if ($product->total_on_hand < $quantity) {
                throw new \Exception('Số lượng sản phẩm không đủ. Còn lại: ' . $product->total_on_hand);
            }

            // Lấy hoặc tạo giỏ hàng
            $cart = MShopKeeperCart::getOrCreateForCustomer($customerId);

            // Kiểm tra sản phẩm đã có trong giỏ hàng chưa
            $cartItem = $cart->items()->where('product_id', $productId)->first();

            if ($cartItem) {
                // Cập nhật số lượng
                $newQuantity = $cartItem->quantity + $quantity;
                
                if ($product->total_on_hand < $newQuantity) {
                    throw new \Exception('Số lượng sản phẩm không đủ. Còn lại: ' . $product->total_on_hand);
                }
                
                $cartItem->update(['quantity' => $newQuantity]);
            } else {
                // Thêm sản phẩm mới
                $cart->items()->create([
                    'product_id' => $productId,
                    'quantity' => $quantity,
                ]);
            }

            DB::commit();
            
            return [
                'success' => true,
                'message' => 'Đã thêm sản phẩm vào giỏ hàng',
                'cart_count' => $cart->total_quantity
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
     * Cập nhật số lượng sản phẩm trong giỏ hàng
     */
    public function updateQuantity($cartItemId, $quantity, $customerId = null)
    {
        try {
            DB::beginTransaction();

            $customerId = $customerId ?? Auth::guard('mshopkeeper_customer')->id();
            
            if (!$customerId) {
                throw new \Exception('Vui lòng đăng nhập');
            }

            $cartItem = MShopKeeperCartItem::whereHas('cart', function($query) use ($customerId) {
                $query->where('customer_id', $customerId);
            })->findOrFail($cartItemId);

            if ($quantity <= 0) {
                $cartItem->delete();
                $message = 'Đã xóa sản phẩm khỏi giỏ hàng';
            } else {
                // Kiểm tra tồn kho
                if ($cartItem->product->total_on_hand < $quantity) {
                    throw new \Exception('Số lượng sản phẩm không đủ. Còn lại: ' . $cartItem->product->total_on_hand);
                }
                
                $cartItem->update(['quantity' => $quantity]);
                $message = 'Đã cập nhật số lượng sản phẩm';
            }

            DB::commit();
            
            return [
                'success' => true,
                'message' => $message
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
     * Xóa sản phẩm khỏi giỏ hàng
     */
    public function removeFromCart($cartItemId, $customerId = null)
    {
        try {
            $customerId = $customerId ?? Auth::guard('mshopkeeper_customer')->id();
            
            if (!$customerId) {
                throw new \Exception('Vui lòng đăng nhập');
            }

            $cartItem = MShopKeeperCartItem::whereHas('cart', function($query) use ($customerId) {
                $query->where('customer_id', $customerId);
            })->findOrFail($cartItemId);

            $cartItem->delete();
            
            return [
                'success' => true,
                'message' => 'Đã xóa sản phẩm khỏi giỏ hàng'
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Lấy giỏ hàng của customer
     */
    public function getCart($customerId = null)
    {
        $customerId = $customerId ?? Auth::guard('mshopkeeper_customer')->id();
        
        if (!$customerId) {
            return null;
        }

        return MShopKeeperCart::with(['items.product'])
            ->where('customer_id', $customerId)
            ->first();
    }

    /**
     * Đếm số lượng sản phẩm trong giỏ hàng
     */
    public function getCartCount($customerId = null)
    {
        $cart = $this->getCart($customerId);
        return $cart ? $cart->total_quantity : 0;
    }

    /**
     * Xóa toàn bộ giỏ hàng
     */
    public function clearCart($customerId = null)
    {
        try {
            $customerId = $customerId ?? Auth::guard('mshopkeeper_customer')->id();
            
            if (!$customerId) {
                throw new \Exception('Vui lòng đăng nhập');
            }

            $cart = MShopKeeperCart::where('customer_id', $customerId)->first();
            
            if ($cart) {
                $cart->items()->delete();
            }
            
            return [
                'success' => true,
                'message' => 'Đã xóa toàn bộ giỏ hàng'
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Kiểm tra tính hợp lệ của giỏ hàng (tồn kho)
     */
    public function validateCart($customerId = null)
    {
        $cart = $this->getCart($customerId);
        
        if (!$cart) {
            return ['valid' => true, 'errors' => []];
        }

        $errors = [];
        
        foreach ($cart->items as $item) {
            if ($item->product->total_on_hand < $item->quantity) {
                $errors[] = [
                    'item_id' => $item->id,
                    'product_name' => $item->product->name,
                    'requested' => $item->quantity,
                    'available' => $item->product->total_on_hand
                ];
            }
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors
        ];
    }

    /**
     * Xóa giỏ hàng của một khách hàng cụ thể (dùng cho admin)
     */
    public function clearCartForCustomer($customerId)
    {
        try {
            $cart = MShopKeeperCart::where('customer_id', $customerId)->first();
            if ($cart) {
                $cart->items()->delete();
                $cart->delete();
                return true;
            }
            return false;
        } catch (\Exception $e) {
            return false;
        }
    }
}
