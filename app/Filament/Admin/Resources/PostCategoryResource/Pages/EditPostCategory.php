<?php

namespace App\Filament\Admin\Resources\PostCategoryResource\Pages;

use App\Filament\Admin\Resources\PostCategoryResource;
use App\Models\CatPost;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Str;

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
        return $this->getResource()::getUrl('edit', ['record' => $this->getRecord()]);
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return 'Chuyên mục đã được cập nhật thành công';
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Tự động cập nhật slug từ name nếu name thay đổi
        if (!empty($data['name'])) {
            $currentRecord = $this->getRecord();
            
            // Chỉ cập nhật slug nếu name đã thay đổi
            if ($currentRecord->name !== $data['name']) {
                $data['slug'] = Str::slug($data['name']);
                // Đảm bảo slug unique (trừ record hiện tại)
                $originalSlug = $data['slug'];
                $counter = 1;
                while (CatPost::where('slug', $data['slug'])
                    ->where('id', '!=', $currentRecord->id)
                    ->exists()) {
                    $data['slug'] = $originalSlug . '-' . $counter;
                    $counter++;
                }
            }
        }

        return $data;
    }
}