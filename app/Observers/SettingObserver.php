<?php

namespace App\Observers;

use App\Models\Setting;
use App\Services\ImageService;
use App\Traits\HandlesFileObserver;

class SettingObserver
{
    use HandlesFileObserver;

    protected $imageService;

    public function __construct(ImageService $imageService)
    {
        $this->imageService = $imageService;
    }

    /**
     * Handle the Setting "created" event.
     */
    public function created(Setting $setting): void
    {
        // Hình ảnh đã được xử lý trong form Filament
    }

    /**
     * Handle the Setting "updating" event.
     */
    public function updating(Setting $setting): void
    {
        $modelClass = get_class($setting);
        $modelId = $setting->id;

        // Lưu logo_link cũ
        if ($setting->isDirty('logo_link')) {
            $this->storeOldFile($modelClass, $modelId, 'logo_link', $setting->getOriginal('logo_link'));
        }

        // Lưu favicon_link cũ
        if ($setting->isDirty('favicon_link')) {
            $this->storeOldFile($modelClass, $modelId, 'favicon_link', $setting->getOriginal('favicon_link'));
        }

        // Lưu og_image_link cũ
        if ($setting->isDirty('og_image_link')) {
            $this->storeOldFile($modelClass, $modelId, 'og_image_link', $setting->getOriginal('og_image_link'));
        }
    }

    /**
     * Handle the Setting "updated" event.
     */
    public function updated(Setting $setting): void
    {
        $modelClass = get_class($setting);
        $modelId = $setting->id;

        // Lấy và xóa logo_link cũ
        $oldLogo = $this->getAndDeleteOldFile($modelClass, $modelId, 'logo_link');
        if ($oldLogo) {
            $this->imageService->deleteImage($oldLogo);
        }

        // Lấy và xóa favicon_link cũ
        $oldFavicon = $this->getAndDeleteOldFile($modelClass, $modelId, 'favicon_link');
        if ($oldFavicon) {
            $this->imageService->deleteImage($oldFavicon);
        }

        // Lấy và xóa og_image_link cũ
        $oldOgImage = $this->getAndDeleteOldFile($modelClass, $modelId, 'og_image_link');
        if ($oldOgImage) {
            $this->imageService->deleteImage($oldOgImage);
        }
    }

    /**
     * Handle the Setting "deleted" event.
     */
    public function deleted(Setting $setting): void
    {
        // Xóa logo khi xóa setting
        if ($setting->logo_link) {
            $this->imageService->deleteImage($setting->logo_link);
        }

        // Xóa favicon khi xóa setting
        if ($setting->favicon_link) {
            $this->imageService->deleteImage($setting->favicon_link);
        }

        // Xóa OG image khi xóa setting
        if ($setting->og_image_link) {
            $this->imageService->deleteImage($setting->og_image_link);
        }
    }

    /**
     * Handle the Setting "restored" event.
     */
    public function restored(Setting $setting): void
    {
        //
    }

    /**
     * Handle the Setting "force deleted" event.
     */
    public function forceDeleted(Setting $setting): void
    {
        //
    }
}
