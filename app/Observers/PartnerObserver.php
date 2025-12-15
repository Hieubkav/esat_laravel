<?php

namespace App\Observers;

use App\Models\Partner;
use App\Services\ImageService;
use App\Traits\HandlesFileObserver;

class PartnerObserver
{
    use HandlesFileObserver;

    protected $imageService;

    public function __construct(ImageService $imageService)
    {
        $this->imageService = $imageService;
    }

    /**
     * Handle the Partner "created" event.
     */
    public function created(Partner $partner): void
    {
        // Hình ảnh đã được xử lý trong form Filament
    }

    /**
     * Handle the Partner "updating" event.
     */
    public function updating(Partner $partner): void
    {
        $modelClass = get_class($partner);
        $modelId = $partner->id;

        // Lưu logo_link cũ
        if ($partner->isDirty('logo_link')) {
            $this->storeOldFile($modelClass, $modelId, 'logo_link', $partner->getOriginal('logo_link'));
        }
    }

    /**
     * Handle the Partner "updated" event.
     */
    public function updated(Partner $partner): void
    {
        $modelClass = get_class($partner);
        $modelId = $partner->id;

        // Lấy và xóa logo_link cũ
        $oldLogo = $this->getAndDeleteOldFile($modelClass, $modelId, 'logo_link');
        if ($oldLogo) {
            $this->imageService->deleteImage($oldLogo);
        }
    }

    /**
     * Handle the Partner "deleted" event.
     */
    public function deleted(Partner $partner): void
    {
        // Xóa hình ảnh khi xóa record
        if ($partner->logo_link) {
            $this->imageService->deleteImage($partner->logo_link);
        }
    }

    /**
     * Handle the Partner "restored" event.
     */
    public function restored(Partner $partner): void
    {
        //
    }

    /**
     * Handle the Partner "force deleted" event.
     */
    public function forceDeleted(Partner $partner): void
    {
        //
    }
}
