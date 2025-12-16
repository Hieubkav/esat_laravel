<?php

namespace App\Filament\Admin\Resources\PostCategoryResource\Pages;

use App\Filament\Admin\Resources\PostCategoryResource;
use App\Models\CatPost;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Str;

class CreatePostCategory extends CreateRecord
{
    protected static string $resource = PostCategoryResource::class;

    public function getTitle(): string
    {
        return 'Thêm Chuyên mục Mới';
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Chuyên mục đã được thêm thành công';
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Tự động tạo slug từ name
        if (!empty($data['name'])) {
            $data['slug'] = Str::slug($data['name']);
            // Đảm bảo slug unique
            $originalSlug = $data['slug'];
            $counter = 1;
            while (CatPost::where('slug', $data['slug'])->exists()) {
                $data['slug'] = $originalSlug . '-' . $counter;
                $counter++;
            }
        }

        // Set default type
        $data['type'] = 'normal';

        return $data;
    }
}