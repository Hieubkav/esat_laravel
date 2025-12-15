<?php

namespace App\Observers;

use App\Models\WebDesign;
use App\Services\WebDesignService;
use App\Services\ImageService;
use App\Traits\HandlesFileObserver;
use Illuminate\Support\Facades\Log;

class WebDesignObserver
{
    use HandlesFileObserver;

    protected $imageService;

    public function __construct(ImageService $imageService)
    {
        $this->imageService = $imageService;
    }
    /**
     * Handle the WebDesign "created" event.
     */
    public function created(WebDesign $webDesign): void
    {
        $this->clearCache();
    }

    /**
     * Handle the WebDesign "updating" event.
     */
    public function updating(WebDesign $webDesign): void
    {
        // Lưu content cũ để so sánh và cleanup file
        if ($webDesign->isDirty('content')) {
            $modelClass = get_class($webDesign);
            $modelId = $webDesign->id;
            $oldContent = $webDesign->getOriginal('content');

            $this->storeOldFile($modelClass, $modelId, 'content', json_encode($oldContent));
        }
    }

    /**
     * Handle the WebDesign "updated" event.
     */
    public function updated(WebDesign $webDesign): void
    {
        // Cleanup file ảnh cũ nếu content thay đổi
        if ($webDesign->wasChanged('content')) {
            $this->cleanupOldServiceImages($webDesign);
        }

        $this->clearCache();
    }

    /**
     * Handle the WebDesign "deleted" event.
     */
    public function deleted(WebDesign $webDesign): void
    {
        // Cleanup tất cả file ảnh trong content khi xóa WebDesign
        $this->cleanupAllServiceImages($webDesign);
        $this->clearCache();
    }

    /**
     * Handle the WebDesign "restored" event.
     */
    public function restored(WebDesign $webDesign): void
    {
        $this->clearCache();
    }

    /**
     * Handle the WebDesign "force deleted" event.
     */
    public function forceDeleted(WebDesign $webDesign): void
    {
        $this->clearCache();
    }

    /**
     * Clear WebDesign cache
     */
    private function clearCache(): void
    {
        app(WebDesignService::class)->clearCache();
    }

    /**
     * Cleanup file ảnh cũ khi content thay đổi
     */
    private function cleanupOldServiceImages(WebDesign $webDesign): void
    {
        $modelClass = get_class($webDesign);
        $modelId = $webDesign->id;

        // Lấy content cũ từ cache
        $oldContentJson = $this->getAndDeleteOldFile($modelClass, $modelId, 'content');
        if (!$oldContentJson) {
            return;
        }

        $oldContent = json_decode($oldContentJson, true);
        $newContent = $webDesign->content;

        // Chỉ xử lý cho component about-us có services
        if ($webDesign->component_key === 'about-us') {
            $this->cleanupServiceImagesForAboutUs($oldContent, $newContent);
        }
    }

    /**
     * Cleanup tất cả file ảnh khi xóa WebDesign
     */
    private function cleanupAllServiceImages(WebDesign $webDesign): void
    {
        if ($webDesign->component_key === 'about-us' && isset($webDesign->content['services'])) {
            foreach ($webDesign->content['services'] as $service) {
                if (isset($service['image']) && $service['image']) {
                    $this->deleteImageFile($service['image']);
                }
            }
        }
    }

    /**
     * Cleanup service images cho about-us component
     */
    private function cleanupServiceImagesForAboutUs(?array $oldContent, ?array $newContent): void
    {
        $oldServices = $oldContent['services'] ?? [];
        $newServices = $newContent['services'] ?? [];

        // Tạo danh sách ảnh cũ và mới
        $oldImages = [];
        $newImages = [];

        foreach ($oldServices as $service) {
            if (isset($service['image']) && $service['image'] && str_starts_with($service['image'], '/storage/')) {
                $oldImages[] = $service['image'];
            }
        }

        foreach ($newServices as $service) {
            if (isset($service['image']) && $service['image'] && str_starts_with($service['image'], '/storage/')) {
                $newImages[] = $service['image'];
            }
        }

        // Tìm ảnh bị xóa (có trong old nhưng không có trong new)
        $deletedImages = array_diff($oldImages, $newImages);

        // Xóa các ảnh không còn sử dụng
        foreach ($deletedImages as $imagePath) {
            $this->deleteImageFile($imagePath);
        }
    }

    /**
     * Xóa file ảnh từ storage
     */
    private function deleteImageFile(string $imagePath): void
    {
        // Chuyển từ /storage/ path sang storage path
        $storagePath = str_replace('/storage/', '', $imagePath);

        try {
            $this->imageService->deleteImage($storagePath);
            Log::info("WebDesign: Đã xóa file ảnh cũ: {$storagePath}");
        } catch (\Exception $e) {
            Log::error("WebDesign: Lỗi khi xóa file ảnh: {$storagePath}", [
                'error' => $e->getMessage()
            ]);
        }
    }
}
