<?php

namespace App\Filament\Admin\Resources\MenuItemResource\Pages;

use App\Filament\Admin\Resources\MenuItemResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMenuItem extends EditRecord
{
    protected static string $resource = MenuItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->label('Xóa'),
        ];
    }
    
    public function getTitle(): string
    {
        return 'Chỉnh sửa Menu';
    }
    
    protected function getRedirectUrl(): string
    {
        // Ở lại trang edit thay vì chuyển về list
        return $this->getResource()::getUrl('edit', ['record' => $this->getRecord()]);
    }
    
    protected function getSavedNotificationTitle(): ?string
    {
        return 'Menu đã được cập nhật thành công';
    }
}
