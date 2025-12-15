<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\MenuItemResource\Pages;
use App\Models\MenuItem;
use App\Models\CatPost;
use App\Models\Post;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use App\Constants\NavigationGroups;
use App\Traits\HasRoleBasedAccess;

class MenuItemResource extends Resource
{
    use HasRoleBasedAccess;
    protected static ?string $model = MenuItem::class;

    protected static ?string $navigationIcon = 'heroicon-o-bars-3';

    protected static ?string $navigationGroup = NavigationGroups::WEBSITE_SETTINGS;

    protected static ?string $navigationLabel = 'Menu điều hướng';

    protected static ?string $modelLabel = 'Menu';

    protected static ?string $pluralModelLabel = 'Menu';

    protected static ?int $navigationSort = 40;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Thông tin menu')
                    ->schema([
                        Select::make('parent_id')
                            ->label('Menu cha')
                            ->options(MenuItem::all()->pluck('label', 'id'))
                            ->searchable()
                            ->nullable()
                            ->columnSpan(1),

                        TextInput::make('label')
                            ->label('Nhãn menu')
                            ->required()
                            ->maxLength(255)
                            ->columnSpan(1),

                        Select::make('type')
                            ->label('Loại menu')
                            ->options([
                                'link' => 'Liên kết trực tiếp',
                                'cat_post' => 'Chuyên mục',
                                'all_posts' => 'Tất cả bài viết',
                                'post' => 'Bài viết',
                                'cat_product' => 'Danh mục sản phẩm',
                                'all_products' => 'Tất cả sản phẩm',
                                'product' => 'Sản phẩm',
                                'display_only' => 'Chỉ hiển thị (không dẫn đến đâu)',
                            ])
                            ->required()
                            ->reactive()
                            ->columnSpanFull(),
                    ])->columns(2),

                Section::make('Cấu hình liên kết')
                    ->schema([
                        TextInput::make('link')
                            ->label('Liên kết URL')
                            ->url()
                            ->maxLength(500)
                            ->visible(fn ($get) => $get('type') === 'link')
                            ->required(fn ($get) => $get('type') === 'link'),

                        Select::make('cat_post_id')
                            ->label('Chuyên mục')
                            ->options(CatPost::all()->pluck('name', 'id'))
                            ->searchable()
                            ->visible(fn ($get) => $get('type') === 'cat_post')
                            ->required(fn ($get) => $get('type') === 'cat_post'),

                        Select::make('post_id')
                            ->label('Bài viết')
                            ->options(Post::all()->pluck('title', 'id'))
                            ->searchable()
                            ->visible(fn ($get) => $get('type') === 'post')
                            ->required(fn ($get) => $get('type') === 'post'),
                    ]),

                Section::make('Cấu hình hiển thị')
                    ->schema([
                        TextInput::make('order')
                            ->label('Thứ tự hiển thị')
                            ->integer()
                            ->default(0)
                            ->minValue(0)
                            ->columnSpan(1),

                        Toggle::make('status')
                            ->label('Hiển thị')
                            ->default(true)
                            ->onColor('success')
                            ->offColor('danger')
                            ->columnSpan(1),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->query(
                MenuItem::query()
                    ->with('parent')
                    ->orderByRaw('COALESCE(parent_id, id), parent_id IS NULL DESC, `order` ASC')
            )
            ->columns([
                TextColumn::make('order')
                    ->label('Thứ tự')
                    ->sortable()
                    ->width(80),

                TextColumn::make('label')
                    ->label('Nhãn menu')
                    ->searchable()
                    ->sortable()
                    ->weight('medium')
                    ->formatStateUsing(function ($record) {
                        if ($record->parent_id) {
                            return '└─ ' . $record->label;
                        }
                        return $record->label;
                    })
                    ->color(function ($record) {
                        return $record->parent_id ? 'gray' : 'primary';
                    }),

                TextColumn::make('parent.label')
                    ->label('Menu cha')
                    ->searchable()
                    ->placeholder('Menu gốc'),

                TextColumn::make('type')
                    ->label('Loại')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'link' => 'gray',
                        'cat_post' => 'info',
                        'all_posts' => 'blue',
                        'post' => 'success',
                        'cat_product' => 'warning',
                        'all_products' => 'orange',
                        'product' => 'cyan',
                        'display_only' => 'purple',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'link' => 'Liên kết',
                        'cat_post' => 'Danh mục bài viết',
                        'all_posts' => 'Tất cả bài viết',
                        'post' => 'Bài viết',
                        'cat_product' => 'Danh mục sản phẩm',
                        'all_products' => 'Tất cả sản phẩm',
                        'product' => 'Sản phẩm',
                        'display_only' => 'Chỉ hiển thị',
                        default => $state,
                    }),

                ToggleColumn::make('status')
                    ->label('Hiển thị')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Ngày tạo')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->label('Loại menu')
                    ->options([
                        'link' => 'Liên kết trực tiếp',
                        'cat_post' => 'Danh mục bài viết',
                        'all_posts' => 'Tất cả bài viết',
                        'post' => 'Bài viết',
                        'cat_product' => 'Danh mục sản phẩm',
                        'all_products' => 'Tất cả sản phẩm',
                        'product' => 'Sản phẩm',
                        'display_only' => 'Chỉ hiển thị (không dẫn đến đâu)',
                    ]),

                Tables\Filters\TernaryFilter::make('status')
                    ->label('Trạng thái hiển thị')
                    ->boolean()
                    ->trueLabel('Đang hiển thị')
                    ->falseLabel('Đã ẩn')
                    ->native(false),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label('Sửa'),
                Tables\Actions\DeleteAction::make()
                    ->label('Xóa'),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->label('Xóa đã chọn'),
                ]),
            ])
            ->defaultSort('order', 'asc')
            ->reorderable('order');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMenuItems::route('/'),
            'create' => Pages\CreateMenuItem::route('/create'),
            'edit' => Pages\EditMenuItem::route('/{record}/edit'),
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
