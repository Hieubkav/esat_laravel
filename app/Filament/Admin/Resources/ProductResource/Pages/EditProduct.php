<?php

namespace App\Filament\Admin\Resources\ProductResource\Pages;

use App\Filament\Admin\Resources\ProductResource;
use App\Models\Setting;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Str;

class EditProduct extends EditRecord
{
    protected static string $resource = ProductResource::class;

    public function getTitle(): string
    {
        return 'Chỉnh sửa Sản phẩm';
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['seo_title'] = $data['name'];

        $description = isset($data['description']) ? Str::limit(strip_tags($data['description']), 160) : '';
        $data['seo_description'] = $data['name'] . ($description ? ' - ' . $description : '');

        return $data;
    }

    protected function afterSave(): void
    {
        $product = $this->record;
        $firstImage = $product->productImages()->orderBy('order', 'asc')->first();

        if ($firstImage && $firstImage->image_link) {
            $product->og_image_link = $firstImage->image_link;
        } else {
            $settings = Setting::first();
            if ($settings && $settings->og_image_link) {
                $product->og_image_link = $settings->og_image_link;
            }
        }

        $product->saveQuietly();
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('viewFrontend')
                ->label('Mở trang')
                ->icon('heroicon-o-eye')
                ->color('info')
                ->url(fn () => $this->getRecord()->slug ? route('products.show', $this->getRecord()->slug) : '#')
                ->openUrlInNewTab()
                ->visible(fn () => filled($this->getRecord()->slug)),
            Actions\DeleteAction::make()
                ->label('Xóa'),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('edit', ['record' => $this->getRecord()]);
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return 'Sản phẩm đã được cập nhật thành công';
    }
}