<?php

namespace App\Observers;

use App\Models\Association;
use App\Services\ImageService;
use App\Traits\HandlesFileObserver;

class AssociationObserver
{
    use HandlesFileObserver;

    protected $imageService;

    public function __construct(ImageService $imageService)
    {
        $this->imageService = $imageService;
    }

    /**
     * Handle the Association "created" event.
     */
    public function created(Association $association): void
    {
        // Hình ảnh đã được xử lý trong form Filament
    }

    /**
     * Handle the Association "updating" event.
     */
    public function updating(Association $association): void
    {
        $modelClass = get_class($association);
        $modelId = $association->id;

        // Lưu image_link cũ
        if ($association->isDirty('image_link')) {
            $this->storeOldFile($modelClass, $modelId, 'image_link', $association->getOriginal('image_link'));
        }
    }

    /**
     * Handle the Association "updated" event.
     */
    public function updated(Association $association): void
    {
        $modelClass = get_class($association);
        $modelId = $association->id;

        // Lấy và xóa image_link cũ
        $oldImage = $this->getAndDeleteOldFile($modelClass, $modelId, 'image_link');
        if ($oldImage) {
            $this->imageService->deleteImage($oldImage);
        }
    }

    /**
     * Handle the Association "deleted" event.
     */
    public function deleted(Association $association): void
    {
        // Xóa hình ảnh khi xóa record
        if ($association->image_link) {
            $this->imageService->deleteImage($association->image_link);
        }
    }

    /**
     * Handle the Association "restored" event.
     */
    public function restored(Association $association): void
    {
        //
    }

    /**
     * Handle the Association "force deleted" event.
     */
    public function forceDeleted(Association $association): void
    {
        //
    }
}
