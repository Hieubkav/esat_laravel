<?php

namespace App\Livewire\Public;

use Livewire\Component;
use App\Helpers\ViewDataHelper;

class DynamicMenu extends Component
{
    public $menuItems = [];
    public $isMobile = false;

    protected $listeners = ['refreshMenu' => 'loadMenuItems'];

    public function mount($isMobile = false)
    {
        $this->isMobile = $isMobile;
        $this->loadMenuItems();
    }

    public function loadMenuItems()
    {
        // Load data từ ViewDataHelper (có cache tự động clear khi có thay đổi)
        $this->menuItems = ViewDataHelper::get('menuItems', collect([]));
    }

    public function refreshMenu()
    {
        $this->loadMenuItems();
    }

    public function render()
    {
        return view('livewire.public.dynamic-menu');
    }
}
