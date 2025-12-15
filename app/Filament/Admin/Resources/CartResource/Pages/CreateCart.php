<?php

namespace App\Filament\Admin\Resources\CartResource\Pages;

use App\Filament\Admin\Resources\CartResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCart extends CreateRecord
{
    protected static string $resource = CartResource::class;
    
    public function getTitle(): string
    {
        return 'Tạo Giỏ hàng Mới';
    }
    
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
    
    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Giỏ hàng đã được tạo thành công';
    }
}
