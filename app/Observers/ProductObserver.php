<?php

namespace App\Observers;

use App\Models\Product;
use App\Services\ImageService;

class ProductObserver
{
    protected $imageService;

    public function __construct(ImageService $imageService)
    {
        $this->imageService = $imageService;
    }

    /**
     * Handle the Product "created" event.
     */
    public function created(Product $product): void
    {
        // Hình ảnh được xử lý riêng thông qua bảng product_images
    }

    /**
     * Handle the Product "updated" event.
     */
    public function updated(Product $product): void
    {
        // Các hình ảnh được xử lý riêng thông qua bảng product_images
    }

    /**
     * Handle the Product "deleted" event.
     */
    public function deleted(Product $product): void
    {
        // Xóa tất cả hình ảnh liên quan trong productImages
        foreach ($product->productImages as $productImage) {
            if ($productImage->image_link) {
                $this->imageService->deleteImage($productImage->image_link);
            }
        }
    }

    /**
     * Handle the Product "restored" event.
     */
    public function restored(Product $product): void
    {
        //
    }

    /**
     * Handle the Product "force deleted" event.
     */
    public function forceDeleted(Product $product): void
    {
        //
    }
}
