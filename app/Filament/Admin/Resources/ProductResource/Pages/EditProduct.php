<?php

namespace App\Filament\Admin\Resources\ProductResource\Pages;

use App\Filament\Admin\Resources\ProductResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditProduct extends EditRecord
{
    protected static string $resource = ProductResource::class;

    public function getTitle(): string
    {
        return 'Chỉnh sửa Sản phẩm';
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('viewFrontend')
                ->label('Mở trang')
                ->icon('heroicon-o-eye')
                ->color('info')
                ->url(fn () => route('products.show', $this->getRecord()->slug))
                ->openUrlInNewTab(),
            Actions\DeleteAction::make()
                ->label('Xóa'),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return 'Sản phẩm đã được cập nhật thành công';
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Đảm bảo stock không bao giờ là NULL
        if (!isset($data['stock']) || $data['stock'] === null || $data['stock'] === '') {
            $data['stock'] = 0;
        }

        // Đảm bảo stock là số nguyên không âm
        $data['stock'] = max(0, (int) $data['stock']);

        return $data;
    }
}