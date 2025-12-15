<?php

if (!function_exists('webDesignVisible')) {
    /**
     * Kiểm tra xem một component có được hiển thị không
     *
     * @param string $componentKey
     * @return bool
     */
    function webDesignVisible(string $componentKey): bool
    {
        return app(\App\Services\WebDesignService::class)->isVisible($componentKey);
    }
}

if (!function_exists('webDesignData')) {
    /**
     * Lấy dữ liệu của một component
     *
     * @param string $componentKey
     * @return \App\Models\WebDesign|null
     */
    function webDesignData(string $componentKey): ?\App\Models\WebDesign
    {
        return app(\App\Services\WebDesignService::class)->getComponentData($componentKey);
    }
}

if (!function_exists('webDesignContent')) {
    /**
     * Lấy content của một component
     *
     * @param string $componentKey
     * @param string|null $key
     * @param mixed $default
     * @return mixed
     */
    function webDesignContent(string $componentKey, ?string $key = null, $default = null)
    {
        $component = webDesignData($componentKey);
        if (!$component || !$component->content) {
            return $default;
        }

        if ($key === null) {
            return $component->content;
        }

        return data_get($component->content, $key, $default);
    }
}

if (!function_exists('webDesignSettings')) {
    /**
     * Lấy cấu hình settings của một component
     *
     * @param string $componentKey
     * @param string|null $key
     * @param mixed $default
     * @return mixed
     */
    function webDesignSettings(string $componentKey, ?string $key = null, $default = null)
    {
        return app(\App\Services\WebDesignService::class)->getSettings($componentKey, $key, $default);
    }
}

if (!function_exists('getAllWebDesignComponents')) {
    /**
     * Lấy tất cả components theo thứ tự
     *
     * @return array
     */
    function getAllWebDesignComponents(): array
    {
        return app(\App\Services\WebDesignService::class)->getOrderedComponents();
    }
}

if (!function_exists('getVisibleWebDesignComponents')) {
    /**
     * Lấy các components hiển thị theo thứ tự
     *
     * @return array
     */
    function getVisibleWebDesignComponents(): array
    {
        return app(\App\Services\WebDesignService::class)->getVisibleComponents();
    }
}
