<?php

namespace App\Observers;

use App\Models\MShopKeeperInventoryItem;
use Illuminate\Support\Facades\Cache;

class MShopKeeperInventoryItemObserver
{
    /**
     * Handle the MShopKeeperInventoryItem "updated" event.
     */
    public function updated(MShopKeeperInventoryItem $item): void
    {
        // Xóa cache nếu các thuộc tính ảnh hưởng đến hiển thị đã thay đổi
        if ($item->wasChanged([
            'is_featured',      // thay đổi danh sách nổi bật
            'is_visible',       // ẩn/hiện trên web
            'inactive',         // trạng thái hoạt động
            'is_item',          // chỉ lấy bản ghi là item
            'selling_price',    // ảnh hưởng sắp xếp/giá hiển thị
            'cost_price',
            'total_on_hand',    // ảnh hưởng sắp xếp/tồn kho
            'category_mshopkeeper_id',
            'picture',
        ])) {
            $this->clearFeaturedProductsCache();
        }
    }

    /**
     * Handle the MShopKeeperInventoryItem "created" event.
     */
    public function created(MShopKeeperInventoryItem $item): void
    {
        // Nếu item mới được tạo với is_featured = true
        if ($item->is_featured) {
            $this->clearFeaturedProductsCache();
        }
    }

    /**
     * Handle the MShopKeeperInventoryItem "deleted" event.
     */
    public function deleted(MShopKeeperInventoryItem $item): void
    {
        // Nếu item bị xóa và đang featured
        if ($item->is_featured) {
            $this->clearFeaturedProductsCache();
        }
    }

    /**
     * Clear featured products cache
     */
    private function clearFeaturedProductsCache(): void
    {
        Cache::forget('storefront_mshopkeeper_products');
        
        // Log để debug
        \Log::info('MShopKeeper featured products cache cleared due to is_featured change');
    }
}
