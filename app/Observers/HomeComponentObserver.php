<?php

namespace App\Observers;

use App\Models\HomeComponent;
use App\Services\ImageService;
use Illuminate\Support\Facades\Cache;

class HomeComponentObserver
{
    protected $imageService;

    public function __construct(ImageService $imageService)
    {
        $this->imageService = $imageService;
    }

    /**
     * Handle the HomeComponent "updating" event.
     */
    public function updating(HomeComponent $component): void
    {
        if ($component->isDirty('config')) {
            $oldImages = $this->extractImagesFromConfig($component->getOriginal('config'));
            Cache::put("home_component_old_images_{$component->id}", $oldImages, now()->addMinutes(10));
        }
    }

    /**
     * Handle the HomeComponent "updated" event.
     */
    public function updated(HomeComponent $component): void
    {
        $oldImages = Cache::get("home_component_old_images_{$component->id}", []);
        Cache::forget("home_component_old_images_{$component->id}");

        if (empty($oldImages)) {
            return;
        }

        $newImages = $this->extractImagesFromConfig($component->config);

        // Xóa các ảnh cũ không còn sử dụng
        foreach ($oldImages as $oldImage) {
            if (!in_array($oldImage, $newImages)) {
                $this->imageService->deleteImage($oldImage);
            }
        }
    }

    /**
     * Handle the HomeComponent "deleted" event.
     */
    public function deleted(HomeComponent $component): void
    {
        $images = $this->extractImagesFromConfig($component->config);
        foreach ($images as $image) {
            $this->imageService->deleteImage($image);
        }
    }

    /**
     * Trích xuất tất cả đường dẫn ảnh từ config JSON
     */
    protected function extractImagesFromConfig(?array $config): array
    {
        if (!$config) {
            return [];
        }

        $images = [];
        $this->findImagesRecursive($config, $images);
        return array_filter($images);
    }

    /**
     * Tìm kiếm đệ quy các đường dẫn ảnh trong mảng
     */
    protected function findImagesRecursive($data, array &$images): void
    {
        if (!is_array($data)) {
            return;
        }

        $imageKeys = [
            'image', 'logo', 'thumbnail',
            'feature_1_image', 'feature_2_image', 'feature_3_image', 'feature_4_image',
            'bocongthuong_logo',
            'association_1_logo', 'association_2_logo', 'association_3_logo', 'association_4_logo',
        ];

        foreach ($data as $key => $value) {
            if (is_string($value) && in_array($key, $imageKeys) && !empty($value)) {
                $images[] = $value;
            } elseif (is_array($value)) {
                $this->findImagesRecursive($value, $images);
            }
        }
    }
}
