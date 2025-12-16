<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\MenuItemResource\Pages;
use App\Models\MenuItem;
use App\Models\CatPost;
use App\Models\CatProduct;
use App\Models\Post;
use App\Models\Product;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Forms\Components\Actions\Action;
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
                Section::make()
                    ->schema([
                        TextInput::make('label')
                            ->label('Nhãn menu')
                            ->required()
                            ->maxLength(255),

                        Select::make('parent_id')
                            ->label('Menu cha')
                            ->options(MenuItem::all()->pluck('label', 'id'))
                            ->searchable()
                            ->nullable(),

                        TextInput::make('link')
                            ->label('Liên kết URL')
                            ->maxLength(500)
                            ->placeholder('/bai-viet hoặc https://example.com')
                            ->helperText('Nhập trực tiếp hoặc dùng nút chọn nhanh bên phải')
                            ->suffixAction(
                                Action::make('quickSelect')
                                    ->icon('heroicon-o-link')
                                    ->tooltip('Chọn nhanh liên kết')
                                    ->form([
                                        Select::make('source_type')
                                            ->label('Loại nguồn')
                                            ->options([
                                                'post' => 'Bài viết',
                                                'cat_post' => 'Danh mục bài viết',
                                                'all_posts' => 'Tất cả bài viết',
                                                'product' => 'Sản phẩm',
                                                'cat_product' => 'Danh mục sản phẩm',
                                                'all_products' => 'Tất cả sản phẩm',
                                            ])
                                            ->required()
                                            ->live()
                                            ->afterStateUpdated(fn (Set $set) => $set('source_id', null)),

                                        Select::make('source_id')
                                            ->label('Chọn mục')
                                            ->options(function (Get $get) {
                                                return match ($get('source_type')) {
                                                    'post' => Post::where('status', 'active')
                                                        ->orderBy('created_at', 'desc')
                                                        ->pluck('title', 'id'),
                                                    'cat_post' => CatPost::where('status', true)
                                                        ->orderBy('order')
                                                        ->pluck('name', 'id'),
                                                    'product' => Product::where('status', 'active')
                                                        ->orderBy('created_at', 'desc')
                                                        ->pluck('name', 'id'),
                                                    'cat_product' => CatProduct::where('status', true)
                                                        ->orderBy('order')
                                                        ->pluck('name', 'id'),
                                                    default => [],
                                                };
                                            })
                                            ->searchable()
                                            ->preload()
                                            ->visible(fn (Get $get) => in_array($get('source_type'), ['post', 'cat_post', 'product', 'cat_product']))
                                            ->required(fn (Get $get) => in_array($get('source_type'), ['post', 'cat_post', 'product', 'cat_product'])),
                                    ])
                                    ->action(function (array $data, Set $set) {
                                        $link = match ($data['source_type']) {
                                            'post' => Post::find($data['source_id'])?->slug
                                                ? '/bai-viet/' . Post::find($data['source_id'])->slug
                                                : null,
                                            'cat_post' => CatPost::find($data['source_id'])?->slug
                                                ? '/bai-viet/chuyen-muc/' . CatPost::find($data['source_id'])->slug
                                                : null,
                                            'all_posts' => '/bai-viet',
                                            'product' => Product::find($data['source_id'])?->slug
                                                ? '/san-pham/' . Product::find($data['source_id'])->slug
                                                : null,
                                            'cat_product' => CatProduct::find($data['source_id'])?->slug
                                                ? '/san-pham/danh-muc/' . CatProduct::find($data['source_id'])->slug
                                                : null,
                                            'all_products' => '/san-pham',
                                            default => null,
                                        };

                                        if ($link) {
                                            $set('link', $link);
                                        }
                                    })
                                    ->modalHeading('Chọn nhanh liên kết')
                                    ->modalSubmitActionLabel('Áp dụng')
                                    ->modalWidth('md')
                            ),

                        Toggle::make('status')
                            ->label('Hiển thị')
                            ->default(true)
                            ->onColor('success')
                            ->offColor('danger'),

                        TextInput::make('order')
                            ->default(0)
                            ->hidden(),
                    ]),
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

                TextColumn::make('link')
                    ->label('Liên kết')
                    ->limit(40)
                    ->tooltip(fn ($record) => $record->link)
                    ->placeholder('Không có liên kết')
                    ->searchable(),

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
