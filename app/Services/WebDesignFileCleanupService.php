<?php

namespace App\Services;

use App\Services\ImageService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class WebDesignFileCleanupService
{
    protected $imageService;

    public function __construct(ImageService $imageService)
    {
        $this->imageService = $imageService;
    }

    /**
     * Cleanup file ảnh cũ khi save ManageWebDesign form
     */
    public function cleanupOldFiles(string $componentKey, array $oldData, array $newData): void
    {
        if ($componentKey === 'about-us') {
            $this->cleanupAboutUsServiceImages($oldData, $newData);
        }
    }

    /**
     * Cleanup service images cho about-us component
     */
    private function cleanupAboutUsServiceImages(array $oldData, array $newData): void
    {
        // Tạo danh sách ảnh cũ và mới từ upload fields
        $oldImages = $this->extractServiceImages($oldData);
        $newImages = $this->extractServiceImages($newData);
        
        // Tìm ảnh bị xóa (có trong old nhưng không có trong new)
        $deletedImages = array_diff($oldImages, $newImages);
        
        // Xóa các ảnh không còn sử dụng
        foreach ($deletedImages as $imagePath) {
            $this->deleteImageFile($imagePath);
        }
    }

    /**
     * Trích xuất danh sách ảnh từ service data
     */
    private function extractServiceImages(array $data): array
    {
        $images = [];
        
        for ($i = 1; $i <= 4; $i++) {
            // Kiểm tra upload field
            $uploadField = "service_{$i}_upload";
            if (isset($data[$uploadField]) && !empty($data[$uploadField])) {
                $uploadedFiles = $data[$uploadField];
                
                if (is_array($uploadedFiles)) {
                    foreach ($uploadedFiles as $file) {
                        if (is_string($file)) {
                            $images[] = $file;
                        }
                    }
                } elseif (is_string($uploadedFiles)) {
                    $images[] = $uploadedFiles;
                }
            }
        }
        
        return array_unique($images);
    }

    /**
     * Xóa file ảnh từ storage
     */
    private function deleteImageFile(string $imagePath): void
    {
        try {
            // imagePath đã là storage path (không có /storage/ prefix)
            $this->imageService->deleteImage($imagePath);
            Log::info("WebDesignFileCleanup: Đã xóa file ảnh cũ: {$imagePath}");
        } catch (\Exception $e) {
            Log::error("WebDesignFileCleanup: Lỗi khi xóa file ảnh: {$imagePath}", [
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Lưu trạng thái cũ vào cache để cleanup sau
     */
    public function storeOldState(string $componentKey, array $oldData): void
    {
        $cacheKey = "webdesign_cleanup_{$componentKey}_" . time();
        Cache::put($cacheKey, $oldData, now()->addMinutes(30));
        
        // Lưu cache key để có thể retrieve sau
        Cache::put("webdesign_cleanup_key_{$componentKey}", $cacheKey, now()->addMinutes(30));
    }

    /**
     * Lấy và xóa trạng thái cũ từ cache
     */
    public function getAndClearOldState(string $componentKey): ?array
    {
        $cacheKeyName = "webdesign_cleanup_key_{$componentKey}";
        $cacheKey = Cache::get($cacheKeyName);
        
        if (!$cacheKey) {
            return null;
        }
        
        $oldData = Cache::get($cacheKey);
        
        // Xóa cache
        Cache::forget($cacheKey);
        Cache::forget($cacheKeyName);
        
        return $oldData;
    }

    /**
     * Cleanup file ảnh khi chuyển từ upload sang URL
     */
    public function cleanupWhenSwitchingToUrl(string $componentKey, array $formData): void
    {
        if ($componentKey !== 'about-us') {
            return;
        }

        for ($i = 1; $i <= 4; $i++) {
            $uploadField = "service_{$i}_upload";
            $imageField = "service_{$i}_image";
            
            // Nếu có URL và không có upload, có thể đã chuyển từ upload sang URL
            if (empty($formData[$uploadField]) && !empty($formData[$imageField])) {
                // Kiểm tra xem có file upload cũ trong cache không
                $oldData = $this->getOldDataFromCache($componentKey);
                if ($oldData && !empty($oldData[$uploadField])) {
                    $oldFiles = $oldData[$uploadField];
                    if (is_array($oldFiles)) {
                        foreach ($oldFiles as $file) {
                            if (is_string($file)) {
                                $this->deleteImageFile($file);
                            }
                        }
                    } elseif (is_string($oldFiles)) {
                        $this->deleteImageFile($oldFiles);
                    }
                }
            }
        }
    }

    /**
     * Lấy dữ liệu cũ từ cache (không xóa)
     */
    private function getOldDataFromCache(string $componentKey): ?array
    {
        $cacheKeyName = "webdesign_cleanup_key_{$componentKey}";
        $cacheKey = Cache::get($cacheKeyName);
        
        if (!$cacheKey) {
            return null;
        }
        
        return Cache::get($cacheKey);
    }
}
