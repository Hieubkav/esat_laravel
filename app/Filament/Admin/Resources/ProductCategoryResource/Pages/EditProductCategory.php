<?php

namespace App\Filament\Admin\Resources\ProductCategoryResource\Pages;

use App\Filament\Admin\Resources\ProductCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditProductCategory extends EditRecord
{
    protected static string $resource = ProductCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('viewFrontend')
                ->label('Mở trang')
                ->icon('heroicon-o-eye')
                ->color('info')
                ->url(fn () => route('products.category', $this->getRecord()->slug))
                ->openUrlInNewTab(),
            Actions\DeleteAction::make()
                ->label('Xóa'),
        ];
    }
    
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}