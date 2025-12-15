<?php

namespace App\Services;

use App\Models\WebDesign;
use Illuminate\Support\Facades\Cache;

class WebDesignService
{
    const CACHE_KEY = 'web_design_components';
    const CACHE_TTL = 3600; // 1 hour

    /**
     * Lấy tất cả cấu hình WebDesign (có cache)
     */
    public function getAllComponents(): array
    {
        return Cache::remember(self::CACHE_KEY, self::CACHE_TTL, function () {
            return WebDesign::all()->keyBy('component_key')->toArray();
        });
    }

    /**
     * Lấy dữ liệu component
     */
    public function getComponentData(string $componentKey): ?WebDesign
    {
        $components = $this->getAllComponents();

        if (!isset($components[$componentKey])) {
            return null;
        }

        return WebDesign::find($components[$componentKey]['id']);
    }

    /**
     * Kiểm tra component có hiển thị không (có cache)
     */
    public function isVisible(string $componentKey): bool
    {
        $components = $this->getAllComponents();

        if (!isset($components[$componentKey])) {
            return true; // Mặc định hiển thị nếu chưa có cấu hình
        }

        $component = $components[$componentKey];
        return $component['is_active'];
    }

    /**
     * Lấy settings của component (có cache)
     */
    public function getSettings(string $componentKey, ?string $key = null, $default = null)
    {
        $components = $this->getAllComponents();

        if (!isset($components[$componentKey]) || !$components[$componentKey]['settings']) {
            return $default;
        }

        $settings = $components[$componentKey]['settings'];

        if ($key === null) {
            return $settings;
        }

        return data_get($settings, $key, $default);
    }

    /**
     * Lấy thứ tự hiển thị (có cache)
     */
    public function getPosition(string $componentKey): int
    {
        $components = $this->getAllComponents();

        return $components[$componentKey]['position'] ?? 999;
    }

    /**
     * Xóa cache
     */
    public function clearCache(): void
    {
        Cache::forget(self::CACHE_KEY);
    }

    /**
     * Cập nhật hoặc tạo component và xóa cache
     */
    public function updateOrCreateComponent(string $componentKey, array $data): WebDesign
    {
        $result = WebDesign::updateOrCreate(
            ['component_key' => $componentKey],
            $data
        );

        $this->clearCache();

        return $result;
    }

    /**
     * Reset về cấu hình mặc định và xóa cache
     */
    public function resetToDefault(): void
    {
        WebDesign::resetToDefault();
        $this->clearCache();
    }

    /**
     * Lấy danh sách components theo thứ tự (có cache)
     */
    public function getOrderedComponents(): array
    {
        $components = $this->getAllComponents();

        // Sắp xếp theo position
        uasort($components, function ($a, $b) {
            return $a['position'] <=> $b['position'];
        });

        return $components;
    }

    /**
     * Lấy components hiển thị theo thứ tự (có cache)
     */
    public function getVisibleComponents(): array
    {
        $components = $this->getOrderedComponents();

        return array_filter($components, function ($component) {
            return $component['is_active'];
        });
    }
}
