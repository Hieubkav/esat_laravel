<?php

namespace App\Traits;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Services\ImageService;

trait HandlesFilamentFileCleanup
{
    /**
     * Cleanup file cũ khi FileUpload thay đổi
     */
    protected function cleanupOldFileUploads(array $oldData, array $newData, array $fileFields): void
    {
        $imageService = app(ImageService::class);
        
        foreach ($fileFields as $field) {
            $oldFiles = $oldData[$field] ?? [];
            $newFiles = $newData[$field] ?? [];
            
            // Chuẩn hóa thành array
            $oldFiles = is_array($oldFiles) ? $oldFiles : [$oldFiles];
            $newFiles = is_array($newFiles) ? $newFiles : [$newFiles];
            
            // Lọc bỏ giá trị empty
            $oldFiles = array_filter($oldFiles);
            $newFiles = array_filter($newFiles);
            
            // Tìm file bị xóa
            $deletedFiles = array_diff($oldFiles, $newFiles);
            
            // Xóa file cũ
            foreach ($deletedFiles as $file) {
                try {
                    $imageService->deleteImage($file);
                    Log::info("FilamentFileCleanup: Đã xóa file cũ: {$file}");
                } catch (\Exception $e) {
                    Log::error("FilamentFileCleanup: Lỗi khi xóa file: {$file}", [
                        'error' => $e->getMessage()
                    ]);
                }
            }
        }
    }

    /**
     * Trích xuất file paths từ FileUpload data
     */
    protected function extractFileUploads(array $data, array $fileFields): array
    {
        $files = [];
        
        foreach ($fileFields as $field) {
            if (isset($data[$field]) && !empty($data[$field])) {
                $fieldFiles = $data[$field];
                
                if (is_array($fieldFiles)) {
                    $files[$field] = array_filter($fieldFiles);
                } elseif (is_string($fieldFiles)) {
                    $files[$field] = [$fieldFiles];
                } else {
                    $files[$field] = [];
                }
            } else {
                $files[$field] = [];
            }
        }
        
        return $files;
    }

    /**
     * Lưu trạng thái file upload hiện tại để cleanup sau
     */
    protected function storeCurrentFileState(string $cacheKey, array $data, array $fileFields): void
    {
        $fileData = $this->extractFileUploads($data, $fileFields);
        
        cache()->put($cacheKey, $fileData, now()->addMinutes(30));
    }

    /**
     * Lấy và xóa trạng thái file upload cũ
     */
    protected function getAndClearFileState(string $cacheKey): ?array
    {
        $fileData = cache()->get($cacheKey);
        
        if ($fileData) {
            cache()->forget($cacheKey);
        }
        
        return $fileData;
    }

    /**
     * Tạo cache key duy nhất cho file state
     */
    protected function getFileStateCacheKey(string $prefix, string $identifier): string
    {
        return "file_state_{$prefix}_{$identifier}_" . time();
    }

    /**
     * Cleanup file khi chuyển từ upload sang URL
     */
    protected function cleanupWhenSwitchingToUrl(array $oldData, array $newData, array $uploadFields, array $urlFields): void
    {
        $imageService = app(ImageService::class);
        
        for ($i = 0; $i < count($uploadFields); $i++) {
            $uploadField = $uploadFields[$i];
            $urlField = $urlFields[$i] ?? null;
            
            if (!$urlField) continue;
            
            $oldUpload = $oldData[$uploadField] ?? [];
            $newUpload = $newData[$uploadField] ?? [];
            $newUrl = $newData[$urlField] ?? '';
            
            // Nếu có URL mới và không có upload mới, nhưng có upload cũ
            if (!empty($newUrl) && empty($newUpload) && !empty($oldUpload)) {
                $oldFiles = is_array($oldUpload) ? $oldUpload : [$oldUpload];
                
                foreach ($oldFiles as $file) {
                    if (!empty($file)) {
                        try {
                            $imageService->deleteImage($file);
                            Log::info("FilamentFileCleanup: Đã xóa file khi chuyển sang URL: {$file}");
                        } catch (\Exception $e) {
                            Log::error("FilamentFileCleanup: Lỗi khi xóa file chuyển URL: {$file}", [
                                'error' => $e->getMessage()
                            ]);
                        }
                    }
                }
            }
        }
    }

    /**
     * Validate file upload và cleanup nếu có lỗi
     */
    protected function validateAndCleanupFileUploads(array $data, array $fileFields, array $rules = []): array
    {
        $errors = [];
        $imageService = app(ImageService::class);
        
        foreach ($fileFields as $field) {
            if (!isset($data[$field]) || empty($data[$field])) {
                continue;
            }
            
            $files = is_array($data[$field]) ? $data[$field] : [$data[$field]];
            
            foreach ($files as $file) {
                if (!is_string($file)) {
                    continue;
                }
                
                // Kiểm tra file có tồn tại
                if (!Storage::disk('public')->exists($file)) {
                    $errors[] = "File không tồn tại: {$file}";
                    continue;
                }
                
                // Kiểm tra định dạng file
                $extension = pathinfo($file, PATHINFO_EXTENSION);
                $allowedExtensions = $rules[$field]['extensions'] ?? ['jpg', 'jpeg', 'png', 'webp'];
                
                if (!in_array(strtolower($extension), $allowedExtensions)) {
                    $errors[] = "File {$field} phải có định dạng: " . implode(', ', $allowedExtensions);
                    
                    // Xóa file không hợp lệ
                    try {
                        $imageService->deleteImage($file);
                        Log::info("FilamentFileCleanup: Đã xóa file không hợp lệ: {$file}");
                    } catch (\Exception $e) {
                        Log::error("FilamentFileCleanup: Lỗi khi xóa file không hợp lệ: {$file}");
                    }
                }
                
                // Kiểm tra kích thước file
                $maxSize = $rules[$field]['max_size'] ?? 2048; // KB
                $fileSize = Storage::disk('public')->size($file) / 1024; // Convert to KB
                
                if ($fileSize > $maxSize) {
                    $errors[] = "File {$field} quá lớn. Tối đa {$maxSize}KB";
                    
                    // Xóa file quá lớn
                    try {
                        $imageService->deleteImage($file);
                        Log::info("FilamentFileCleanup: Đã xóa file quá lớn: {$file}");
                    } catch (\Exception $e) {
                        Log::error("FilamentFileCleanup: Lỗi khi xóa file quá lớn: {$file}");
                    }
                }
            }
        }
        
        return $errors;
    }

    /**
     * Cleanup tất cả file trong một thư mục cụ thể
     */
    protected function cleanupDirectoryFiles(string $directory, array $keepFiles = []): int
    {
        $imageService = app(ImageService::class);
        $deletedCount = 0;
        
        $allFiles = Storage::disk('public')->files($directory);
        $filesToDelete = array_diff($allFiles, $keepFiles);
        
        foreach ($filesToDelete as $file) {
            try {
                $imageService->deleteImage($file);
                $deletedCount++;
                Log::info("FilamentFileCleanup: Đã xóa file trong cleanup directory: {$file}");
            } catch (\Exception $e) {
                Log::error("FilamentFileCleanup: Lỗi khi xóa file trong cleanup directory: {$file}");
            }
        }
        
        return $deletedCount;
    }
}
