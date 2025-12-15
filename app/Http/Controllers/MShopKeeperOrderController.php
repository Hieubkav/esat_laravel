<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\MShopKeeperOrderService;
use App\Services\MShopKeeperCartService;
use App\Services\MShopKeeperCustomerAuthService;

class MShopKeeperOrderController extends Controller
{
    protected $orderService;
    protected $cartService;
    protected $authService;

    public function __construct(
        MShopKeeperOrderService $orderService,
        MShopKeeperCartService $cartService,
        MShopKeeperCustomerAuthService $authService
    ) {
        $this->orderService = $orderService;
        $this->cartService = $cartService;
        $this->authService = $authService;
        $this->middleware('mshopkeeper.auth');
    }

    /**
     * Hiển thị trang checkout
     */
    public function checkout()
    {
        $cart = $this->cartService->getCart();
        
        if (!$cart || $cart->items->isEmpty()) {
            return redirect()->route('mshopkeeper.cart.show')
                ->with('error', 'Giỏ hàng trống. Vui lòng thêm sản phẩm trước khi đặt hàng.');
        }

        // Validate giỏ hàng
        $validation = $this->cartService->validateCart();
        if (!$validation['valid']) {
            return redirect()->route('mshopkeeper.cart.show')
                ->with('error', 'Giỏ hàng có sản phẩm không hợp lệ. Vui lòng kiểm tra lại.');
        }

        $customer = $this->authService->user();

        return view('storefront.mshopkeeper.checkout.index', compact('cart', 'customer'));
    }

    /**
     * Xử lý đặt hàng
     */
    public function placeOrder(Request $request)
    {
        $request->validate([
            'shipping_name' => 'required|string|max:255',
            'shipping_phone' => 'required|string|max:20',
            'shipping_email' => 'nullable|email|max:255',
            'shipping_address' => 'required|string|max:500',
            'payment_method' => 'required|in:cod,bank_transfer',
            'note' => 'nullable|string|max:1000',
        ], [
            'shipping_name.required' => 'Vui lòng nhập tên người nhận',
            'shipping_phone.required' => 'Vui lòng nhập số điện thoại',
            'shipping_address.required' => 'Vui lòng nhập địa chỉ giao hàng',
            'payment_method.required' => 'Vui lòng chọn phương thức thanh toán',
            'payment_method.in' => 'Phương thức thanh toán không hợp lệ',
        ]);

        // Tạo đơn hàng từ giỏ hàng (không cần customerId nữa)
        $result = $this->orderService->createOrderFromCart($request->all());

        if ($result['success']) {
            // Giỏ hàng đã được xóa trong service
            return redirect()->route('mshopkeeper.cart.show')
                ->with('success', 'Đặt hàng thành công! Mã đơn hàng: ' . $result['order']['OrderNo']);
        } else {
            return redirect()->back()
                ->withInput()
                ->with('error', $result['message']);
        }
    }

    /**
     * API endpoint để đặt hàng nhanh (AJAX)
     */
    public function quickOrder(Request $request)
    {
        $request->validate([
            'shipping_name' => 'required|string|max:255',
            'shipping_phone' => 'required|string|max:20',
            'shipping_address' => 'required|string|max:500',
            'payment_method' => 'required|in:cod,bank_transfer',
        ]);

        // Tạo đơn hàng từ giỏ hàng (không cần customerId nữa)
        $result = $this->orderService->createOrderFromCart($request->all());

        if ($result['success']) {
            // Giỏ hàng đã được xóa trong service
            return response()->json([
                'success' => true,
                'message' => 'Đặt hàng thành công!',
                'order_number' => $result['order']['OrderNo'],
                'redirect_url' => route('mshopkeeper.cart.show')
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => $result['message']
            ], 400);
        }
    }
}
