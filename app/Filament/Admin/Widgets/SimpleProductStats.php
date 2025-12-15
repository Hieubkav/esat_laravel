<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Product;
use App\Models\CatProduct;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\IconColumn;

class SimpleProductStats extends BaseWidget
{
    protected static ?string $heading = 'Sản phẩm nổi bật';
    protected static ?int $sort = 8;
    protected int | string | array $columnSpan = [
        'sm' => 1,
        'md' => 2,
        'lg' => 3,
        'xl' => 4,
        '2xl' => 4,
    ];

    // Auto refresh every 5 seconds
    protected static ?string $pollingInterval = '5s';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Product::query()
                    ->where('status', 'active')
                    ->with(['productCategory', 'productImages'])
                    ->orderBy('is_hot', 'desc')
                    ->orderBy('order', 'asc')
                    ->limit(10)
            )
            ->columns([
                ImageColumn::make('thumbnail')
                    ->label('Hình ảnh')
                    ->circular()
                    ->defaultImageUrl(fn() => asset('images/default-product.jpg'))
                    ->getStateUsing(function (Product $record) {
                        $firstImage = $record->productImages()->orderBy('order', 'asc')->first();
                        return $firstImage ? $firstImage->image_link : null;
                    }),

                TextColumn::make('name')
                    ->label('Tên sản phẩm')
                    ->searchable()
                    ->sortable()
                    ->limit(30),

                TextColumn::make('productCategory.name')
                    ->label('Danh mục')
                    ->searchable()
                    ->sortable()
                    ->default('Chưa phân loại'),

                TextColumn::make('price')
                    ->label('Giá bán')
                    ->money('VND')
                    ->sortable(),

                TextColumn::make('stock')
                    ->label('Tồn kho')
                    ->sortable()
                    ->color(fn (int $state): string => match (true) {
                        $state > 50 => 'success',
                        $state > 10 => 'warning',
                        default => 'danger',
                    }),

                IconColumn::make('is_hot')
                    ->label('Nổi bật')
                    ->boolean()
                    ->trueIcon('heroicon-o-fire')
                    ->falseIcon('heroicon-o-minus')
                    ->trueColor('success')
                    ->falseColor('gray'),

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
                    }),
            ])
            ->actions([
                // Actions có thể thêm sau khi có routes
            ])
            ->defaultSort('is_hot', 'desc')
            ->paginated(false);
    }
}
