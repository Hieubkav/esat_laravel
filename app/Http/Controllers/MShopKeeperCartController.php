<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\MShopKeeperCartService;

class MShopKeeperCartController extends Controller
{
    protected $cartService;

    public function __construct(MShopKeeperCartService $cartService)
    {
        $this->cartService = $cartService;
        // Chỉ require auth cho các action khác, không phải show
        $this->middleware('mshopkeeper.auth')->except(['show']);
    }

    /**
     * Hiển thị trang giỏ hàng (Livewire)
     */
    public function show()
    {
        return view('storefront.mshopkeeper.cart.livewire-show');
    }

    /**
     * Thêm sản phẩm vào giỏ hàng (AJAX)
     */
    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:mshopkeeper_inventory_items,id',
            'quantity' => 'integer|min:1|max:100'
        ]);

        $result = $this->cartService->addToCart(
            $request->product_id,
            $request->quantity ?? 1
        );

        if ($request->expectsJson()) {
            return response()->json($result);
        }

        if ($result['success']) {
            return redirect()->back()->with('success', $result['message']);
        } else {
            return redirect()->back()->with('error', $result['message']);
        }
    }

    /**
     * Cập nhật số lượng sản phẩm trong giỏ hàng (AJAX)
     */
    public function update(Request $request, $cartItemId)
    {
        $request->validate([
            'quantity' => 'required|integer|min:0|max:100'
        ]);

        $result = $this->cartService->updateQuantity($cartItemId, $request->quantity);

        if ($request->expectsJson()) {
            return response()->json($result);
        }

        if ($result['success']) {
            return redirect()->back()->with('success', $result['message']);
        } else {
            return redirect()->back()->with('error', $result['message']);
        }
    }

    /**
     * Xóa sản phẩm khỏi giỏ hàng (AJAX)
     */
    public function remove(Request $request, $cartItemId)
    {
        $result = $this->cartService->removeFromCart($cartItemId);

        if ($request->expectsJson()) {
            return response()->json($result);
        }

        if ($result['success']) {
            return redirect()->back()->with('success', $result['message']);
        } else {
            return redirect()->back()->with('error', $result['message']);
        }
    }

    /**
     * Xóa toàn bộ giỏ hàng
     */
    public function clear(Request $request)
    {
        $result = $this->cartService->clearCart();

        if ($request->expectsJson()) {
            return response()->json($result);
        }

        if ($result['success']) {
            return redirect()->back()->with('success', $result['message']);
        } else {
            return redirect()->back()->with('error', $result['message']);
        }
    }

    /**
     * Lấy số lượng sản phẩm trong giỏ hàng (AJAX)
     */
    public function count()
    {
        $count = $this->cartService->getCartCount();
        return response()->json(['count' => $count]);
    }

    /**
     * Trang liên hệ đặt hàng từ giỏ hàng
     */
    public function contact()
    {
        $cart = $this->cartService->getCart();

        if (!$cart || $cart->items->count() === 0) {
            return redirect()->route('mshopkeeper.cart.show')
                ->with('error', 'Giỏ hàng trống, vui lòng thêm sản phẩm trước khi liên hệ');
        }

        $validation = $this->cartService->validateCart();

        if (!$validation['valid']) {
            return redirect()->route('mshopkeeper.cart.show')
                ->with('error', 'Vui lòng kiểm tra lại số lượng sản phẩm trong giỏ hàng');
        }

        return view('storefront.mshopkeeper.cart.contact', compact('cart'));
    }
}
