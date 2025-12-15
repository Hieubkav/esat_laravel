<?php

namespace App\Filament\Admin\Resources\AssociationResource\Pages;

use App\Filament\Admin\Resources\AssociationResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAssociations extends ListRecords
{
    protected static string $resource = AssociationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Thêm hiệp hội mới'),
        ];
    }
    
    public function getTitle(): string
    {
        return 'Quản lý Hiệp hội';
    }
}
