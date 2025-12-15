<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\PostCategoryResource\Pages;
use App\Filament\Admin\Resources\PostCategoryResource\RelationManagers;
use App\Models\CatPost;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use App\Constants\NavigationGroups;
use App\Traits\HasRoleBasedAccess;

class PostCategoryResource extends Resource
{
    use HasRoleBasedAccess;
    protected static ?string $model = CatPost::class;

    protected static ?string $modelLabel = 'chuyên mục';

    protected static ?string $pluralModelLabel = 'chuyên mục';

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationGroup = NavigationGroups::CONTENT_MANAGEMENT;

    protected static ?string $navigationLabel = 'Chuyên mục';

    protected static ?int $navigationSort = 31;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Thông tin chuyên mục')
                    ->schema([
                        TextInput::make('name')
                            ->label('Tên chuyên mục')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (string $state, callable $set) => $set('slug', Str::slug($state))),

                        TextInput::make('slug')
                            ->label('Đường dẫn')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),

                        Textarea::make('description')
                            ->label('Mô tả')
                            ->rows(3)
                            ->maxLength(1000),

                        Select::make('type')
                            ->label('Loại chuyên mục')
                            ->options([
                                'normal' => 'Bài viết thường',
                                'news' => 'Tin tức',
                                'service' => 'Dịch vụ',
                                'course' => 'Khóa học',
                            ])
                            ->default('normal')
                            ->required(),
                    ]),

                Section::make('Cấu hình hiển thị')
                    ->schema([
                        TextInput::make('order')
                            ->label('Thứ tự hiển thị')
                            ->integer()
                            ->default(0),

                        Toggle::make('status')
                            ->label('Hiển thị')
                            ->default(true)
                            ->onColor('success')
                            ->offColor('danger'),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('order')
                    ->label('Thứ tự')
                    ->sortable(),

                TextColumn::make('name')
                    ->label('Tên chuyên mục')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('slug')
                    ->label('Đường dẫn')
                    ->searchable(),

                TextColumn::make('type')
                    ->label('Loại')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'normal' => 'gray',
                        'news' => 'info',
                        'service' => 'success',
                        'course' => 'warning',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'normal' => 'Bài viết thường',
                        'news' => 'Tin tức',
                        'service' => 'Dịch vụ',
                        'course' => 'Khóa học',
                        default => $state,
                    })
                    ->sortable(),

                TextColumn::make('posts_count')
                    ->label('Số bài viết')
                    ->counts('posts')
                    ->sortable(),

                ToggleColumn::make('status')
                    ->label('Hiển thị')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Ngày tạo')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label('Sửa'),
                Tables\Actions\DeleteAction::make()
                    ->label('Xóa'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Xóa đã chọn'),
                ]),
            ])
            ->defaultSort('order', 'asc');
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\CategoryPostsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPostCategories::route('/'),
            'create' => Pages\CreatePostCategory::route('/create'),
            'edit' => Pages\EditPostCategory::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'success';
    }
}