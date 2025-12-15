<?php

namespace App\Filament\Admin\Resources\PostCategoryResource\Pages;

use App\Filament\Admin\Resources\PostCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPostCategory extends EditRecord
{
    protected static string $resource = PostCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('viewFrontend')
                ->label('Mở trang')
                ->icon('heroicon-o-eye')
                ->color('info')
                ->url(fn () => route('posts.index', ['category' => $this->getRecord()->id]))
                ->openUrlInNewTab(),
            Actions\DeleteAction::make()
                ->label('Xóa'),
        ];
    }

    public function getTitle(): string
    {
        return 'Chỉnh sửa Chuyên mục';
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return 'Chuyên mục đã được cập nhật thành công';
    }
}