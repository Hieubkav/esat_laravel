<?php

namespace App\Traits;

use Illuminate\Support\Facades\Cache;

trait HandlesFileObserver
{
    /**
     * Lưu file cũ vào cache để xóa sau khi update
     */
    protected function storeOldFile(string $modelClass, int $modelId, string $field, ?string $oldFilePath): void
    {
        if (!$oldFilePath) {
            return;
        }

        $cacheKey = $this->getCacheKey($modelClass, $modelId, $field);
        Cache::put($cacheKey, $oldFilePath, now()->addMinutes(10)); // Lưu 10 phút
    }

    /**
     * Lấy và xóa file cũ từ cache
     */
    protected function getAndDeleteOldFile(string $modelClass, int $modelId, string $field): ?string
    {
        $cacheKey = $this->getCacheKey($modelClass, $modelId, $field);
        $oldFilePath = Cache::get($cacheKey);
        
        if ($oldFilePath) {
            Cache::forget($cacheKey);
        }
        
        return $oldFilePath;
    }

    /**
     * Tạo cache key duy nhất
     */
    private function getCacheKey(string $modelClass, int $modelId, string $field): string
    {
        return "old_file_{$modelClass}_{$modelId}_{$field}";
    }
}
