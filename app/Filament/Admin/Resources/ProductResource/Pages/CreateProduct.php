<?php

namespace App\Filament\Admin\Resources\ProductResource\Pages;

use App\Filament\Admin\Resources\ProductResource;
use App\Models\Setting;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Str;

class CreateProduct extends CreateRecord
{
    protected static string $resource = ProductResource::class;

    public function getTitle(): string
    {
        return 'Thêm Sản phẩm Mới';
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['seo_title'] = $data['name'];

        $description = isset($data['description']) ? Str::limit(strip_tags($data['description']), 160) : '';
        $data['seo_description'] = $data['name'] . ($description ? ' - ' . $description : '');

        return $data;
    }

    protected function afterCreate(): void
    {
        $product = $this->record;
        $firstImage = $product->productImages()->orderBy('order', 'asc')->first();

        if ($firstImage && $firstImage->image_link) {
            $product->og_image_link = $firstImage->image_link;
        } else {
            $settings = Setting::first();
            if ($settings && $settings->og_image_link) {
                $product->og_image_link = $settings->og_image_link;
            }
        }

        $product->saveQuietly();
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Sản phẩm đã được thêm thành công';
    }
}