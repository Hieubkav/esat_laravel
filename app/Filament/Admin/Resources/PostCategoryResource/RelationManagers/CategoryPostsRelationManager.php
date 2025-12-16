<?php

namespace App\Filament\Admin\Resources\PostCategoryResource\RelationManagers;

use App\Filament\Admin\Resources\PostResource;
use App\Models\Post;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables;

class CategoryPostsRelationManager extends RelationManager
{
    protected static string $relationship = 'posts';

    protected static ?string $title = 'Bài viết';

    protected static ?string $recordTitleAttribute = 'title';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('recordId')
                    ->label('Chọn bài viết')
                    ->options(Post::where('status', 'active')->pluck('title', 'id'))
                    ->searchable()
                    ->preload()
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->columns([
                ImageColumn::make('thumbnail')
                    ->label('Hình')
                    ->defaultImageUrl(fn() => asset('images/default-post.jpg'))
                    ->size(60)
                    ->extraImgAttributes(['class' => 'object-cover rounded-lg']),

                TextColumn::make('title')
                    ->label('Tiêu đề')
                    ->searchable()
                    ->sortable()
                    ->limit(50),

                TextColumn::make('status')
                    ->label('Trạng thái')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'inactive' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'active' => 'Hiển thị',
                        'inactive' => 'Ẩn',
                        default => $state,
                    })
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Trạng thái')
                    ->options([
                        'active' => 'Hiển thị',
                        'inactive' => 'Ẩn',
                    ]),
            ])
            ->headerActions([
                Tables\Actions\AttachAction::make()
                    ->label('Thêm bài viết có sẵn')
                    ->preloadRecordSelect()
                    ->recordSelectSearchColumns(['title', 'slug']),
            ])
            ->actions([
                Tables\Actions\Action::make('edit')
                    ->label('Sửa')
                    ->icon('heroicon-m-pencil-square')
                    ->url(fn ($record) => PostResource::getUrl('edit', ['record' => $record]))
                    ->openUrlInNewTab(),
                Tables\Actions\DetachAction::make()
                    ->label('Gỡ khỏi danh mục'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DetachBulkAction::make()
                        ->label('Gỡ khỏi danh mục'),
                ]),
            ])
            ->defaultSort('posts.created_at', 'desc');
    }
}
