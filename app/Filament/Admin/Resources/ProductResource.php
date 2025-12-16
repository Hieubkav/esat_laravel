<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\ProductResource\Pages;
use App\Filament\Admin\Resources\ProductResource\RelationManagers\ProductImagesRelationManager;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use App\Services\ImageService;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;

use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use App\Constants\NavigationGroups;
use App\Traits\HasRoleBasedAccess;

class ProductResource extends Resource
{
    use HasRoleBasedAccess;
    protected static ?string $model = Product::class;

    protected static ?string $modelLabel = 'sản phẩm';

    protected static ?string $pluralModelLabel = 'sản phẩm';

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';

    protected static ?string $navigationGroup = null;

    protected static ?string $navigationLabel = 'Sản phẩm';

    protected static ?int $navigationSort = 1;



    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Thông tin sản phẩm')
                    ->schema([
                        TextInput::make('name')
                            ->label('Tên sản phẩm')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (?string $state, callable $set) => $set('slug', $state ? Str::slug($state) : '')),

                        TextInput::make('slug')
                            ->label('Đường dẫn')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255)
                            ->hidden(),

                        Select::make('category_id')
                            ->label('Danh mục')
                            ->relationship('productCategory', 'name')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->createOptionForm([
                                Forms\Components\TextInput::make('name')
                                    ->label('Tên danh mục')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\Toggle::make('status')
                                    ->label('Hiển thị')
                                    ->default(true),
                            ])
                            ->createOptionModalHeading('Tạo danh mục mới'),

                        TextInput::make('brand')
                            ->label('Thương hiệu')
                            ->maxLength(255),

                        TextInput::make('price')
                            ->label('Giá bán')
                            ->numeric()
                            ->prefix('VNĐ')
                            ->helperText('Để trống nếu muốn hiển thị "Liên Hệ"'),

                        Toggle::make('is_hot')
                            ->label('Nổi bật')
                            ->default(false)
                            ->onColor('success')
                            ->offColor('danger'),

                        Select::make('status')
                            ->label('Trạng thái')
                            ->options([
                                'active' => 'Hiển thị',
                                'inactive' => 'Ẩn',
                            ])
                            ->default('active')
                            ->required(),

                        TextInput::make('order')
                            ->label('Thứ tự hiển thị')
                            ->integer()
                            ->default(0)
                            ->hidden(),
                    ])->columns(2),

                Section::make('Mô tả sản phẩm')
                    ->schema([
                        RichEditor::make('description')
                            ->label('Mô tả')
                            ->fileAttachmentsDisk('public')
                            ->fileAttachmentsDirectory('products')
                            ->columnSpanFull(),
                    ]),


            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('order')
                    ->label('Thứ tự')
                    ->sortable(),

                ImageColumn::make('thumbnail')
                    ->label('Hình đại diện')
                    ->defaultImageUrl(fn() => asset('images/default-product.jpg'))
                    ->circular()
                    ->getStateUsing(function (Product $record) {
                        $firstImage = $record->productImages()->orderBy('order', 'asc')->first();
                        return $firstImage ? $firstImage->image_link : null;
                    }),

                TextColumn::make('name')
                    ->label('Tên sản phẩm')
                    ->searchable()
                    ->sortable()
                    ->limit(40),

                TextColumn::make('price')
                    ->label('Giá bán')
                    ->formatStateUsing(fn ($state) => $state && $state > 0 ? number_format($state, 0, ',', '.') . 'đ' : 'Liên Hệ')
                    ->sortable(),

                TextColumn::make('productCategory.name')
                    ->label('Danh mục')
                    ->searchable()
                    ->sortable(),

                IconColumn::make('is_hot')
                    ->label('Nổi bật')
                    ->boolean()
                    ->sortable(),

                TextColumn::make('status')
                    ->label('Trạng thái')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'inactive' => 'danger',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'active' => 'Hiển thị',
                        'inactive' => 'Ẩn',
                    })
                    ->sortable(),
            ])
            ->reorderable('order')
            ->filters([
                Tables\Filters\SelectFilter::make('category_id')
                    ->relationship('productCategory', 'name')
                    ->label('Danh mục'),

                Tables\Filters\TernaryFilter::make('is_hot')
                    ->label('Nổi bật'),

                Tables\Filters\SelectFilter::make('status')
                    ->label('Trạng thái')
                    ->options([
                        'active' => 'Hiển thị',
                        'inactive' => 'Ẩn',
                    ]),
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
            ProductImagesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['productCategory', 'productImages' => function($query) {
                $query->orderBy('order', 'asc')->limit(1);
            }]);
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