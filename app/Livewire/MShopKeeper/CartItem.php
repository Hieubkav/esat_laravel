<?php

namespace App\Livewire\MShopKeeper;

use Livewire\Component;
use App\Models\MShopKeeperCartItem;
use App\Services\MShopKeeperCartService;

class CartItem extends Component
{
    public $cartItem;
    public $isLoading = false;

    protected $cartService;

    public function boot()
    {
        $this->cartService = app(MShopKeeperCartService::class);
    }

    public function mount(MShopKeeperCartItem $cartItem)
    {
        $this->cartItem = $cartItem;
    }

    /**
     * Cập nhật số lượng
     */
    public function updateQuantity($newQuantity)
    {
        $this->isLoading = true;

        try {
            if ($newQuantity <= 0) {
                $this->removeItem();
                return;
            }

            $result = $this->cartService->updateQuantity($this->cartItem->id, $newQuantity);

            if ($result['success']) {
                $this->cartItem->refresh();
                $this->dispatch('cartUpdated');
                $this->dispatch('showNotification', [
                    'message' => $result['message'],
                    'type' => 'success'
                ]);
            } else {
                $this->dispatch('showNotification', [
                    'message' => $result['message'],
                    'type' => 'error'
                ]);
            }
        } catch (\Exception $e) {
            $this->dispatch('showNotification', [
                'message' => 'Có lỗi xảy ra, vui lòng thử lại',
                'type' => 'error'
            ]);
        } finally {
            $this->isLoading = false;
        }
    }

    /**
     * Xóa item
     */
    public function removeItem()
    {
        $this->isLoading = true;

        try {
            $result = $this->cartService->removeFromCart($this->cartItem->id);

            if ($result['success']) {
                $this->dispatch('cartUpdated');
                $this->dispatch('itemRemoved', $this->cartItem->id);
                $this->dispatch('showNotification', [
                    'message' => $result['message'],
                    'type' => 'success'
                ]);
            } else {
                $this->dispatch('showNotification', [
                    'message' => $result['message'],
                    'type' => 'error'
                ]);
            }
        } catch (\Exception $e) {
            $this->dispatch('showNotification', [
                'message' => 'Có lỗi xảy ra, vui lòng thử lại',
                'type' => 'error'
            ]);
        } finally {
            $this->isLoading = false;
        }
    }

    /**
     * Tăng số lượng
     */
    public function increase()
    {
        $this->updateQuantity($this->cartItem->quantity + 1);
    }

    /**
     * Giảm số lượng
     */
    public function decrease()
    {
        $this->updateQuantity($this->cartItem->quantity - 1);
    }

    public function render()
    {
        return view('livewire.mshopkeeper.cart-item');
    }
}
