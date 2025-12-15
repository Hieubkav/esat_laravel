<?php

namespace App\Filament\Admin\Resources\CartResource\Pages;

use App\Filament\Admin\Resources\CartResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCart extends EditRecord
{
    protected static string $resource = CartResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make()
                ->label('Xem chi tiết'),
            Actions\DeleteAction::make()
                ->label('Xóa'),
        ];
    }
    
    public function getTitle(): string
    {
        return 'Chỉnh sửa Giỏ hàng';
    }
    
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
    
    protected function getSavedNotificationTitle(): ?string
    {
        return 'Giỏ hàng đã được cập nhật thành công';
    }
}
