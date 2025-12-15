<?php

namespace App\Filament\Admin\Resources\MenuItemResource\Pages;

use App\Filament\Admin\Resources\MenuItemResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMenuItems extends ListRecords
{
    protected static string $resource = MenuItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Thêm menu mới'),
        ];
    }
    
    public function getTitle(): string
    {
        return 'Quản lý Menu';
    }
}
