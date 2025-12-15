<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Product;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Widgets\Concerns\InteractsWithPageFilters;

class TopProducts extends BaseWidget
{
    use InteractsWithPageFilters;

    protected static ?string $heading = 'Sản phẩm bán chạy';
    protected static ?int $sort = 6;
    protected int | string | array $columnSpan = 2;

    // Auto refresh every 5 seconds
    protected static ?string $pollingInterval = '5s';

    public function table(Table $table): Table
    {
        $startDate = $this->filters['startDate'] ?? null;
        $endDate = $this->filters['endDate'] ?? null;

        return $table
            ->query(
                Product::query()
                    ->select([
                        'products.id',
                        'products.name',
                        'products.price',
                        'products.stock',
                        'products.category_id',
                        'products.created_at',
                        'products.updated_at'
                    ])
                    ->selectRaw('COALESCE(SUM(order_items.quantity), 0) as total_sold')
                    ->selectRaw('COALESCE(SUM(order_items.subtotal), 0) as total_revenue')
                    ->leftJoin('order_items', 'products.id', '=', 'order_items.product_id')
                    ->leftJoin('orders', function($join) use ($startDate, $endDate) {
                        $join->on('order_items.order_id', '=', 'orders.id')
                             ->where('orders.status', '=', 'completed');

                        if ($startDate) {
                            $join->whereDate('orders.created_at', '>=', $startDate);
                        }
                        if ($endDate) {
                            $join->whereDate('orders.created_at', '<=', $endDate);
                        }
                    })
                    ->with(['productCategory', 'productImages'])
                    ->groupBy([
                        'products.id',
                        'products.name',
                        'products.price',
                        'products.stock',
                        'products.category_id',
                        'products.created_at',
                        'products.updated_at'
                    ])
                    ->orderByDesc('total_sold')
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

                TextColumn::make('total_sold')
                    ->label('Đã bán')
                    ->sortable()
                    ->default(0)
                    ->color('success'),

                TextColumn::make('total_revenue')
                    ->label('Doanh thu')
                    ->money('VND')
                    ->sortable()
                    ->default(0)
                    ->color('success'),
            ])
            ->actions([
                // Tables\Actions\Action::make('view')
                //     ->label('Xem')
                //     ->icon('heroicon-m-eye')
                //     ->url(fn (Product $record): string => route('filament.admin.resources.products.view', $record))
                //     ->openUrlInNewTab(),
            ])
            ->defaultSort('total_sold', 'desc')
            ->paginated(false);
    }
}
