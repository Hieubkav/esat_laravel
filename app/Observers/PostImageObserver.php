<?php

namespace App\Observers;

use App\Models\PostImage;
use App\Services\ImageService;
use App\Traits\HandlesFileObserver;

class PostImageObserver
{
    use HandlesFileObserver;

    protected $imageService;

    public function __construct(ImageService $imageService)
    {
        $this->imageService = $imageService;
    }

    /**
     * Handle the PostImage "created" event.
     */
    public function created(PostImage $postImage): void
    {
        // Hình ảnh đã được xử lý trong form Filament
    }

    /**
     * Handle the PostImage "updating" event.
     */
    public function updating(PostImage $postImage): void
    {
        $modelClass = get_class($postImage);
        $modelId = $postImage->id;

        // Lưu image_link cũ
        if ($postImage->isDirty('image_link')) {
            $this->storeOldFile($modelClass, $modelId, 'image_link', $postImage->getOriginal('image_link'));
        }
    }

    /**
     * Handle the PostImage "updated" event.
     */
    public function updated(PostImage $postImage): void
    {
        $modelClass = get_class($postImage);
        $modelId = $postImage->id;

        // Lấy và xóa image_link cũ
        $oldImage = $this->getAndDeleteOldFile($modelClass, $modelId, 'image_link');
        if ($oldImage) {
            $this->imageService->deleteImage($oldImage);
        }
    }

    /**
     * Handle the PostImage "deleted" event.
     */
    public function deleted(PostImage $postImage): void
    {
        // Xóa hình ảnh khi xóa record
        if ($postImage->image_link) {
            $this->imageService->deleteImage($postImage->image_link);
        }
    }

    /**
     * Handle the PostImage "restored" event.
     */
    public function restored(PostImage $postImage): void
    {
        //
    }

    /**
     * Handle the PostImage "force deleted" event.
     */
    public function forceDeleted(PostImage $postImage): void
    {
        //
    }
}
