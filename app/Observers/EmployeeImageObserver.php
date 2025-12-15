<?php

namespace App\Observers;

use App\Models\EmployeeImage;
use App\Services\ImageService;
use App\Traits\HandlesFileObserver;

class EmployeeImageObserver
{
    use HandlesFileObserver;

    protected $imageService;

    public function __construct(ImageService $imageService)
    {
        $this->imageService = $imageService;
    }

    /**
     * Handle the EmployeeImage "created" event.
     */
    public function created(EmployeeImage $employeeImage): void
    {
        // Hình ảnh đã được xử lý trong form Filament
    }

    /**
     * Handle the EmployeeImage "updating" event.
     */
    public function updating(EmployeeImage $employeeImage): void
    {
        $modelClass = get_class($employeeImage);
        $modelId = $employeeImage->id;

        // Lưu image_link cũ
        if ($employeeImage->isDirty('image_link')) {
            $this->storeOldFile($modelClass, $modelId, 'image_link', $employeeImage->getOriginal('image_link'));
        }
    }

    /**
     * Handle the EmployeeImage "updated" event.
     */
    public function updated(EmployeeImage $employeeImage): void
    {
        $modelClass = get_class($employeeImage);
        $modelId = $employeeImage->id;

        // Lấy và xóa image_link cũ
        $oldImage = $this->getAndDeleteOldFile($modelClass, $modelId, 'image_link');
        if ($oldImage) {
            $this->imageService->deleteImage($oldImage);
        }
    }

    /**
     * Handle the EmployeeImage "deleted" event.
     */
    public function deleted(EmployeeImage $employeeImage): void
    {
        // Xóa hình ảnh khi xóa record
        if ($employeeImage->image_link) {
            $this->imageService->deleteImage($employeeImage->image_link);
        }
    }

    /**
     * Handle the EmployeeImage "restored" event.
     */
    public function restored(EmployeeImage $employeeImage): void
    {
        //
    }

    /**
     * Handle the EmployeeImage "force deleted" event.
     */
    public function forceDeleted(EmployeeImage $employeeImage): void
    {
        //
    }
}
