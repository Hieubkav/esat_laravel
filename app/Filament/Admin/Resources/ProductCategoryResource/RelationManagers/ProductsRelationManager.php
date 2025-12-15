<?php

namespace App\Filament\Admin\Resources\ProductCategoryResource\RelationManagers;

use App\Models\Product;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\RichEditor;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables;
use Illuminate\Support\Str;

class ProductsRelationManager extends RelationManager
{
    protected static string $relationship = 'products';

    protected static ?string $title = 'Sản phẩm';

    protected static ?string $recordTitleAttribute = 'name';

    public function form(Form $form): Form
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
                            ->afterStateUpdated(fn (string $state, callable $set) => $set('slug', Str::slug($state))),

                        TextInput::make('slug')
                            ->label('Đường dẫn')
                            ->required()
                            ->unique(Product::class, 'slug', ignoreRecord: true)
                            ->maxLength(255),

                        TextInput::make('sku')
                            ->label('Mã sản phẩm')
                            ->unique(Product::class, 'sku', ignoreRecord: true)
                            ->maxLength(100),

                        TextInput::make('brand')
                            ->label('Thương hiệu')
                            ->maxLength(255),
                    ])->columns(2),

                Section::make('Giá và kho')
                    ->schema([
                        TextInput::make('price')
                            ->label('Giá bán')
                            ->numeric()
                            ->prefix('VNĐ')
                            ->required(),

                        TextInput::make('compare_price')
                            ->label('Giá so sánh')
                            ->numeric()
                            ->prefix('VNĐ'),

                        TextInput::make('stock')
                            ->label('Số lượng tồn kho*')
                            ->numeric()
                            ->default(0)
                            ->minValue(0)
                            ->maxValue(999999)
                            ->step(1)
                            ->helperText('Nhập số lượng sản phẩm có trong kho (mặc định: 0)')
                            ->rules(['integer', 'min:0', 'max:999999']),

                        TextInput::make('unit')
                            ->label('Đơn vị')
                            ->maxLength(50),
                    ])->columns(2),

                Section::make('Mô tả sản phẩm')
                    ->schema([
                        RichEditor::make('description')
                            ->label('Mô tả')
                            ->fileAttachmentsDisk('public')
                            ->fileAttachmentsDirectory('products')
                            ->columnSpanFull(),
                    ]),

                Section::make('Cấu hình hiển thị')
                    ->schema([
                        TextInput::make('order')
                            ->label('Thứ tự hiển thị')
                            ->integer()
                            ->default(0),

                        Toggle::make('is_hot')
                            ->label('Sản phẩm hot')
                            ->default(false),

                        Select::make('status')
                            ->label('Trạng thái')
                            ->options([
                                'active' => 'Hiển thị',
                                'inactive' => 'Ẩn',
                            ])
                            ->default('active')
                            ->required(),
                    ])->columns(3),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('name')
                    ->label('Tên sản phẩm')
                    ->searchable()
                    ->sortable()
                    ->limit(40),

                TextColumn::make('sku')
                    ->label('Mã SP')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('price')
                    ->label('Giá bán')
                    ->money('VND')
                    ->sortable(),

                TextColumn::make('stock')
                    ->label('Tồn kho')
                    ->sortable(),

                ToggleColumn::make('is_hot')
                    ->label('Hot')
                    ->sortable(),

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

                TextColumn::make('order')
                    ->label('Thứ tự')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Ngày tạo')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_hot')
                    ->label('Sản phẩm hot'),

                Tables\Filters\SelectFilter::make('status')
                    ->label('Trạng thái')
                    ->options([
                        'active' => 'Hiển thị',
                        'inactive' => 'Ẩn',
                    ]),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Thêm sản phẩm'),
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
}
