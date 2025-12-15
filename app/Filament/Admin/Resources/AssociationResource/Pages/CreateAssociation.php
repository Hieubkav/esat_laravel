<?php

namespace App\Filament\Admin\Resources\AssociationResource\Pages;

use App\Filament\Admin\Resources\AssociationResource;
use Filament\Resources\Pages\CreateRecord;

class CreateAssociation extends CreateRecord
{
    protected static string $resource = AssociationResource::class;
    
    public function getTitle(): string
    {
        return 'Thêm Hiệp hội Mới';
    }
    
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
    
    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Hiệp hội đã được thêm thành công';
    }
}
