<?php

namespace App\Filament\Admin\Resources\ProductResource\Pages;

use App\Filament\Admin\Resources\ProductResource;
use Filament\Resources\Pages\CreateRecord;

class CreateProduct extends CreateRecord
{
    protected static string $resource = ProductResource::class;

    public function getTitle(): string
    {
        return 'Thêm Sản phẩm Mới';
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Sản phẩm đã được thêm thành công';
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Đảm bảo stock không bao giờ là NULL
        if (!isset($data['stock']) || $data['stock'] === null || $data['stock'] === '') {
            $data['stock'] = 0;
        }

        // Đảm bảo stock là số nguyên không âm
        $data['stock'] = max(0, (int) $data['stock']);

        return $data;
    }
}