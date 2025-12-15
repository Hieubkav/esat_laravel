<?php

namespace App\Observers;

use App\Models\Slider;
use App\Services\ImageService;
use App\Traits\HandlesFileObserver;

class SliderObserver
{
    use HandlesFileObserver;

    protected $imageService;

    public function __construct(ImageService $imageService)
    {
        $this->imageService = $imageService;
    }

    /**
     * Handle the Slider "created" event.
     */
    public function created(Slider $slider): void
    {
        // Hình ảnh đã được xử lý trong form Filament
    }

    /**
     * Handle the Slider "updating" event.
     */
    public function updating(Slider $slider): void
    {
        $modelClass = get_class($slider);
        $modelId = $slider->id;

        // Lưu image_link cũ
        if ($slider->isDirty('image_link')) {
            $this->storeOldFile($modelClass, $modelId, 'image_link', $slider->getOriginal('image_link'));
        }
    }

    /**
     * Handle the Slider "updated" event.
     */
    public function updated(Slider $slider): void
    {
        $modelClass = get_class($slider);
        $modelId = $slider->id;

        // Lấy và xóa image_link cũ
        $oldImage = $this->getAndDeleteOldFile($modelClass, $modelId, 'image_link');
        if ($oldImage) {
            $this->imageService->deleteImage($oldImage);
        }
    }

    /**
     * Handle the Slider "deleted" event.
     */
    public function deleted(Slider $slider): void
    {
        // Xóa hình ảnh khi xóa record
        if ($slider->image_link) {
            $this->imageService->deleteImage($slider->image_link);
        }
    }

    /**
     * Handle the Slider "restored" event.
     */
    public function restored(Slider $slider): void
    {
        //
    }

    /**
     * Handle the Slider "force deleted" event.
     */
    public function forceDeleted(Slider $slider): void
    {
        //
    }
}
