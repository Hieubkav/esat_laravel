<?php

namespace App\Filament\Admin\Resources\MShopKeeperCartResource\Pages;

use App\Filament\Admin\Resources\MShopKeeperCartResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMShopKeeperCart extends EditRecord
{
    protected static string $resource = MShopKeeperCartResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
