<?php

namespace App\Filament\Admin\Resources\AssociationResource\Pages;

use App\Filament\Admin\Resources\AssociationResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAssociation extends EditRecord
{
    protected static string $resource = AssociationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->label('Xóa'),
        ];
    }
    
    public function getTitle(): string
    {
        return 'Chỉnh sửa Hiệp hội';
    }
    
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
    
    protected function getSavedNotificationTitle(): ?string
    {
        return 'Hiệp hội đã được cập nhật thành công';
    }
}
