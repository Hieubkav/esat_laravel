<?php

namespace App\Livewire\Public;

use Livewire\Component;
use App\Services\MShopKeeperCartService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;

class CartIcon extends Component
{
    public $cartCount = 0;

    public function mount()
    {
        $this->loadCartCount();
    }

    public function loadCartCount()
    {
        if (Auth::guard('mshopkeeper_customer')->check()) {
            $cartService = new MShopKeeperCartService();
            $this->cartCount = $cartService->getCartCount();
        } else {
            $this->cartCount = 0;
        }
    }

    #[On('cartUpdated')]
    #[On('customer-logged-in')]
    #[On('customer-logged-out')]
    public function refreshCart()
    {
        $this->loadCartCount();
    }

    public function render()
    {
        return view('livewire.public.cart-icon');
    }
}
