<?php

namespace App\Filament\Admin\Resources\CartResource\Pages;

use App\Filament\Admin\Resources\CartResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewCart extends ViewRecord
{
    protected static string $resource = CartResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->label('Chỉnh sửa'),
        ];
    }
    
    public function getTitle(): string
    {
        return 'Chi tiết Giỏ hàng';
    }
}
