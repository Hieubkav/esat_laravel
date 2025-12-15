<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Order;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;

class LatestOrders extends BaseWidget
{
    protected static ?string $heading = 'Đơn hàng mới nhất';
    protected static ?int $sort = 7;
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
                Order::query()
                    ->with(['customer'])
                    ->latest()
                    ->limit(10)
            )
            ->columns([
                TextColumn::make('order_number')
                    ->label('Mã đơn hàng')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('customer.name')
                    ->label('Khách hàng')
                    ->searchable()
                    ->sortable()
                    ->default('Khách vãng lai'),

                TextColumn::make('total')
                    ->label('Tổng tiền')
                    ->money('VND')
                    ->sortable(),

                TextColumn::make('status')
                    ->label('Trạng thái')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'processing' => 'info',
                        'completed' => 'success',
                        'cancelled' => 'danger',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending' => 'Chờ xử lý',
                        'processing' => 'Đang xử lý',
                        'completed' => 'Hoàn thành',
                        'cancelled' => 'Đã hủy',
                    })
                    ->sortable(),

                TextColumn::make('payment_method')
                    ->label('Thanh toán')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'cod' => 'warning',
                        'bank_transfer' => 'info',
                        'online' => 'success',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'cod' => 'COD',
                        'bank_transfer' => 'Chuyển khoản',
                        'online' => 'Online',
                        default => 'Chưa xác định',
                    }),

                TextColumn::make('created_at')
                    ->label('Ngày tạo')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->actions([
                // Tables\Actions\Action::make('view')
                //     ->label('Xem')
                //     ->icon('heroicon-m-eye')
                //     ->url(fn (Order $record): string => route('filament.admin.resources.orders.view', $record))
                //     ->openUrlInNewTab(),
            ])
            ->defaultSort('created_at', 'desc')
            ->paginated(false);
    }
}
