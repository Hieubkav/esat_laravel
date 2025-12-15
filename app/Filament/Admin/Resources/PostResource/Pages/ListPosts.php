<?php

namespace App\Filament\Admin\Resources\PostResource\Pages;

use App\Filament\Admin\Resources\PostResource;
use App\Filament\Admin\Pages\BaseListRecords;
use Filament\Actions;

class ListPosts extends BaseListRecords
{
    protected static string $resource = PostResource::class;

    public function getTitle(): string
    {
        return 'Danh sách Bài viết';
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Thêm bài viết'),
        ];
    }
}