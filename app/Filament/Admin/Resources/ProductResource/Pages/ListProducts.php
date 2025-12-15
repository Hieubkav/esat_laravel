<?php

namespace App\Filament\Admin\Resources\ProductResource\Pages;

use App\Filament\Admin\Resources\ProductResource;
use App\Filament\Admin\Pages\BaseListRecords;
use Filament\Actions;

class ListProducts extends BaseListRecords
{
    protected static string $resource = ProductResource::class;

    public function getTitle(): string
    {
        return 'Danh sách Sản phẩm';
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Thêm sản phẩm'),
        ];
    }
}