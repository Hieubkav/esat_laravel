<?php

namespace App\Livewire\MShopKeeper;

use Livewire\Component;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Services\MShopKeeperCartService;
use App\Services\MShopKeeperCustomerAuthService;
use App\Services\MShopKeeperOrderService;

class CartPage extends Component
{
    public $cart;
    public $validation;
    public $isLoading = false;

    // Quick Order Modal
    public $showQuickOrderModal = false;
    public $shippingAddress = '';
    public $paymentMethod = 'cod';
    public $orderNote = '';
    public $isSubmittingOrder = false;

    protected $cartService;
    protected $authService;
    protected $orderService;

    public function boot()
    {
        $this->cartService  = app(MShopKeeperCartService::class);
        $this->authService  = app(MShopKeeperCustomerAuthService::class);
        $this->orderService = app(MShopKeeperOrderService::class);
    }

    public function mount()
    {
        $this->loadCart();
        $this->initializeQuickOrderForm();
    }

    /** =========================
     *  Computed properties (Livewire 3)
     *  ========================= */
    public function getIsAuthenticatedProperty(): bool
    {
        return Auth::guard('mshopkeeper_customer')->check();
    }

    public function getCustomerProperty()
    {
        return $this->authService ? $this->authService->user() : null;
    }

    /** =========================
     *  Cart logic
     *  ========================= */
    public function loadCart()
    {
        if (!$this->isAuthenticated) {
            $this->cart = null;
            $this->validation = [
                'valid'   => false,
                'message' => 'Vui lòng đăng nhập để xem giỏ hàng',
            ];
            return;
        }

        $this->cart = $this->cartService->getCart();

        // Ensure products are loaded for image display
        if ($this->cart && $this->cart->items) {
            $this->cart->load('items.product');
        }

        $this->validation = $this->cartService->validateCart();
    }

    public function updateQuantity($cartItemId, $newQuantity)
    {
        $this->isLoading = true;

        try {
            if ($newQuantity <= 0) {
                $this->removeItem($cartItemId);
                return;
            }

            $result = $this->cartService->updateQuantity($cartItemId, $newQuantity);

            if ($result['success']) {
                $this->loadCart();
                $this->dispatch('cartUpdated');
                $this->dispatch('showNotification', [
                    'message' => $result['message'],
                    'type'    => 'success',
                ]);
            } else {
                $this->dispatch('showNotification', [
                    'message' => $result['message'],
                    'type'    => 'error',
                ]);
            }
        } catch (\Exception $e) {
            $this->dispatch('showNotification', [
                'message' => 'Có lỗi xảy ra, vui lòng thử lại',
                'type'    => 'error',
            ]);
        } finally {
            $this->isLoading = false;
        }
    }

    public function removeItem($cartItemId)
    {
        $this->isLoading = true;

        try {
            $result = $this->cartService->removeFromCart($cartItemId);

            if ($result['success']) {
                $this->loadCart();
                $this->dispatch('cartUpdated');
                $this->dispatch('showNotification', [
                    'message' => $result['message'],
                    'type'    => 'success',
                ]);
            } else {
                $this->dispatch('showNotification', [
                    'message' => $result['message'],
                    'type'    => 'error',
                ]);
            }
        } catch (\Exception $e) {
            $this->dispatch('showNotification', [
                'message' => 'Có lỗi xảy ra, vui lòng thử lại',
                'type'    => 'error',
            ]);
        } finally {
            $this->isLoading = false;
        }
    }

    public function clearCart()
    {
        $this->isLoading = true;

        try {
            $result = $this->cartService->clearCart();

            if ($result['success']) {
                $this->loadCart();
                $this->dispatch('cartUpdated');
                $this->dispatch('showNotification', [
                    'message' => $result['message'],
                    'type'    => 'success',
                ]);
            } else {
                $this->dispatch('showNotification', [
                    'message' => $result['message'],
                    'type'    => 'error',
                ]);
            }
        } catch (\Exception $e) {
            $this->dispatch('showNotification', [
                'message' => 'Có lỗi xảy ra, vui lòng thử lại',
                'type'    => 'error',
            ]);
        } finally {
            $this->isLoading = false;
        }
    }

    public function increaseQuantity($cartItemId, $currentQuantity)
    {
        $this->updateQuantity($cartItemId, $currentQuantity + 1);
    }

    public function decreaseQuantity($cartItemId, $currentQuantity)
    {
        $this->updateQuantity($cartItemId, $currentQuantity - 1);
    }

    #[On('cartUpdated')]
    #[On('customer-logged-in')]
    #[On('customer-logged-out')]
    public function refreshCart()
    {
        $this->loadCart();
        $this->initializeQuickOrderForm();
    }

    /** =========================
     *  Quick Order
     *  ========================= */
    public function initializeQuickOrderForm()
    {
        if ($this->isAuthenticated) {
            $customer = $this->customer;
            if ($customer) {
                $this->shippingAddress = $customer->addr ?? '';
            }
        }
    }

    public function openQuickOrderModal()
    {
        if (!$this->isAuthenticated) {
            $this->dispatch('showNotification', [
                'message' => 'Vui lòng đăng nhập để đặt hàng',
                'type'    => 'error',
            ]);
            return;
        }

        if (!$this->cart || $this->cart->items->isEmpty()) {
            $this->dispatch('showNotification', [
                'message' => 'Giỏ hàng trống, vui lòng thêm sản phẩm trước khi đặt hàng',
                'type'    => 'error',
            ]);
            return;
        }

        $this->showQuickOrderModal = true;
        $this->initializeQuickOrderForm();
    }

    public function closeQuickOrderModal()
    {
        $this->showQuickOrderModal = false;
        $this->resetQuickOrderForm();
    }

    public function resetQuickOrderForm()
    {
        $this->orderNote        = '';
        $this->paymentMethod    = 'cod';
        $this->isSubmittingOrder = false;
        $this->resetValidation();

        $this->initializeQuickOrderForm();
    }

    protected function getQuickOrderRules()
    {
        return [
            'shippingAddress' => 'required|string|max:500',
            'paymentMethod'   => 'required|in:cod,bank_transfer',
            'orderNote'       => 'nullable|string|max:1000',
        ];
    }

    protected function getQuickOrderMessages()
    {
        return [
            'shippingAddress.required' => 'Vui lòng nhập địa chỉ giao hàng',
            'shippingAddress.max'      => 'Địa chỉ giao hàng không được quá 500 ký tự',
            'paymentMethod.required'   => 'Vui lòng chọn phương thức thanh toán',
            'paymentMethod.in'         => 'Phương thức thanh toán không hợp lệ',
            'orderNote.max'            => 'Ghi chú không được quá 1000 ký tự',
        ];
    }

    public function submitQuickOrder()
    {
        $this->validate($this->getQuickOrderRules(), $this->getQuickOrderMessages());
        $this->isSubmittingOrder = true;

        try {
            Log::info('Quick order submission started', [
                'customer_id'      => Auth::guard('mshopkeeper_customer')->id(),
                'shipping_address' => $this->shippingAddress,
                'payment_method'   => $this->paymentMethod,
                'order_note'       => $this->orderNote,
            ]);

            $orderData = [
                'shipping_address' => $this->shippingAddress,
                'payment_method'   => $this->paymentMethod,
                'note'             => $this->orderNote,
            ];

            $result = $this->orderService->createOrderFromCart($orderData);

            Log::info('Order service result', [
                'success' => $result['success'] ?? false,
                'message' => $result['message'] ?? 'No message',
                'order'   => $result['order'] ?? null,
            ]);

            if ($result['success']) {
                $this->closeQuickOrderModal();
                $this->loadCart();

                $this->dispatch('cartUpdated');
                $this->dispatch('showNotification', [
                    'message' => 'Đặt hàng thành công! Mã đơn hàng: ' . $result['order']['OrderNo'],
                    'type'    => 'success',
                ]);
            } else {
                $this->dispatch('showNotification', [
                    'message' => $result['message'],
                    'type'    => 'error',
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Quick order submission failed', [
                'error'       => $e->getMessage(),
                'trace'       => $e->getTraceAsString(),
                'customer_id' => Auth::guard('mshopkeeper_customer')->id(),
                'file'        => $e->getFile(),
                'line'        => $e->getLine(),
                'order_data'  => [
                    'shipping_address' => $this->shippingAddress,
                    'payment_method'   => $this->paymentMethod,
                ],
            ]);

            $errorMessage = $e->getMessage();

            if (str_contains($errorMessage, 'Call Order Service Failed')) {
                $errorMessage = 'Hệ thống đặt hàng tạm thời gặp sự cố. Vui lòng thử lại sau hoặc liên hệ hỗ trợ.';
            } elseif (str_contains($errorMessage, 'Giỏ hàng')) {
                // giữ nguyên
            } else {
                $errorMessage = 'Có lỗi xảy ra khi đặt hàng. Vui lòng thử lại.';
            }

            if (app()->environment('local')) {
                $errorMessage .= ' (Debug: ' . basename($e->getFile()) . ':' . $e->getLine() . ')';
            }

            $this->dispatch('showNotification', [
                'message' => $errorMessage,
                'type'    => 'error',
            ]);
        } finally {
            $this->isSubmittingOrder = false;
        }
    }

    public function render()
    {
        return view('livewire.mshopkeeper.cart-page');
    }
}
