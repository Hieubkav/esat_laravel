<?php

namespace App\Observers;

use App\Models\ProductImage;
use App\Services\ImageService;
use App\Traits\HandlesFileObserver;

class ProductImageObserver
{
    use HandlesFileObserver;

    protected $imageService;

    public function __construct(ImageService $imageService)
    {
        $this->imageService = $imageService;
    }

    /**
     * Handle the ProductImage "created" event.
     */
    public function created(ProductImage $productImage): void
    {
        // Hình ảnh đã được xử lý trong form Filament
    }

    /**
     * Handle the ProductImage "updating" event.
     */
    public function updating(ProductImage $productImage): void
    {
        $modelClass = get_class($productImage);
        $modelId = $productImage->id;

        // Lưu image_link cũ
        if ($productImage->isDirty('image_link')) {
            $this->storeOldFile($modelClass, $modelId, 'image_link', $productImage->getOriginal('image_link'));
        }
    }

    /**
     * Handle the ProductImage "updated" event.
     */
    public function updated(ProductImage $productImage): void
    {
        $modelClass = get_class($productImage);
        $modelId = $productImage->id;

        // Lấy và xóa image_link cũ
        $oldImage = $this->getAndDeleteOldFile($modelClass, $modelId, 'image_link');
        if ($oldImage) {
            $this->imageService->deleteImage($oldImage);
        }
    }

    /**
     * Handle the ProductImage "deleted" event.
     */
    public function deleted(ProductImage $productImage): void
    {
        // Xóa hình ảnh khi xóa record
        if ($productImage->image_link) {
            $this->imageService->deleteImage($productImage->image_link);
        }
    }

    /**
     * Handle the ProductImage "restored" event.
     */
    public function restored(ProductImage $productImage): void
    {
        //
    }

    /**
     * Handle the ProductImage "force deleted" event.
     */
    public function forceDeleted(ProductImage $productImage): void
    {
        //
    }
}
