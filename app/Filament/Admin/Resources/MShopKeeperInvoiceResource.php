<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\MShopKeeperInvoiceResource\Pages;
use App\Models\MShopKeeperInvoice;
use App\Constants\NavigationGroups;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\TextColumn;

class MShopKeeperInvoiceResource extends Resource
{
    protected static ?string $model = MShopKeeperInvoice::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationLabel = 'Hóa đơn MShopKeeper';

    protected static ?string $modelLabel = 'Hóa đơn MShopKeeper';

    protected static ?string $pluralModelLabel = 'Hóa đơn MShopKeeper';

    protected static ?string $navigationGroup = NavigationGroups::ECOMMERCE;

    protected static ?int $navigationSort = 14;

    // Read-only resource - không cho phép tạo/sửa/xóa
    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit($record): bool
    {
        return false;
    }

    public static function canDelete($record): bool
    {
        return false;
    }

    public static function canDeleteAny(): bool
    {
        return false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('invoice_number')
                    ->label('Số hóa đơn')
                    ->disabled(),
                Forms\Components\TextInput::make('customer_name')
                    ->label('Tên khách hàng')
                    ->disabled(),
                Forms\Components\TextInput::make('total_amount')
                    ->label('Tổng tiền')
                    ->disabled(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('invoice_number')
                    ->label('Số hóa đơn')
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::Bold)
                    ->color('primary'),

                TextColumn::make('customer_name')
                    ->label('Khách hàng')
                    ->searchable()
                    ->sortable()
                    ->limit(30),

                TextColumn::make('customer_tel')
                    ->label('Điện thoại')
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('total_amount')
                    ->label('Tổng tiền')
                    ->money('VND')
                    ->sortable()
                    ->weight(FontWeight::SemiBold)
                    ->color('success'),

                TextColumn::make('payment_status')
                    ->label('Trạng thái')
                    ->formatStateUsing(fn ($state) => match($state) {
                        1 => 'Chưa thanh toán',
                        2 => 'Ghi nợ',
                        3 => 'Đã thanh toán',
                        4 => 'Đã hủy',
                        5 => 'Chờ giao hàng',
                        6 => 'Đang giao hàng',
                        7 => 'Giao hàng thất bại',
                        8 => 'Giao hàng hoàn thành',
                        9 => 'Đã chuyển hoàn',
                        10 => 'Chờ thu COD',
                        default => 'Không xác định',
                    })
                    ->badge()
                    ->color(fn ($state) => match($state) {
                        1, 10 => 'warning',
                        2 => 'info',
                        3, 8 => 'success',
                        4, 7, 9 => 'danger',
                        5, 6 => 'primary',
                        default => 'gray',
                    }),

                TextColumn::make('sale_channel_name')
                    ->label('Kênh bán')
                    ->badge()
                    ->color(fn ($state) => match(strtolower($state ?? '')) {
                        'website' => 'success',
                        'facebook' => 'info',
                        'shopee' => 'warning',
                        'lazada' => 'primary',
                        'tiki' => 'secondary',
                        default => 'gray',
                    })
                    ->toggleable(),

                TextColumn::make('unique_products_count')
                    ->label('Sản phẩm')
                    ->formatStateUsing(fn ($record) => $record->hasOrderDetails()
                        ? $record->unique_products_count . ' loại (' . $record->total_items . ' SP)'
                        : 'Không có'
                    )
                    ->badge()
                    ->color(fn ($record) => $record->hasOrderDetails() ? 'info' : 'gray')
                    ->tooltip(fn ($record) => $record->hasOrderDetails()
                        ? 'Có ' . $record->unique_products_count . ' loại sản phẩm với tổng ' . $record->total_items . ' sản phẩm'
                        : 'Không có thông tin chi tiết sản phẩm'
                    )
                    ->toggleable(),

                TextColumn::make('invoice_date')
                    ->label('Ngày tạo')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),

                TextColumn::make('sync_status')
                    ->label('Đồng bộ')
                    ->badge()
                    ->color(fn ($state) => match($state) {
                        'synced' => 'success',
                        'error' => 'danger',
                        'pending' => 'warning',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => match($state) {
                        'synced' => 'Đã đồng bộ',
                        'error' => 'Lỗi',
                        'pending' => 'Chờ xử lý',
                        default => $state,
                    }),
            ])
            ->filters([
                SelectFilter::make('payment_status')
                    ->label('Trạng thái thanh toán')
                    ->options([
                        1 => 'Chưa thanh toán',
                        2 => 'Ghi nợ',
                        3 => 'Đã thanh toán',
                        4 => 'Đã hủy',
                        5 => 'Chờ giao hàng',
                        6 => 'Đang giao hàng',
                        7 => 'Giao hàng thất bại',
                        8 => 'Giao hàng hoàn thành',
                        9 => 'Đã chuyển hoàn',
                        10 => 'Chờ thu COD',
                    ]),

                SelectFilter::make('sale_channel_name')
                    ->label('Kênh bán hàng')
                    ->options([
                        'Website' => 'Website',
                        'Facebook' => 'Facebook',
                        'Shopee' => 'Shopee',
                        'Lazada' => 'Lazada',
                        'Tiki' => 'Tiki',
                    ]),

                SelectFilter::make('sync_status')
                    ->label('Trạng thái đồng bộ')
                    ->options([
                        'synced' => 'Đã đồng bộ',
                        'error' => 'Lỗi',
                        'pending' => 'Chờ xử lý',
                    ]),

                Filter::make('from_website')
                    ->label('Từ Website')
                    ->query(fn (Builder $query): Builder => $query->fromWebsite())
                    ->toggle(),

                Filter::make('has_order_details')
                    ->label('Có chi tiết sản phẩm')
                    ->query(fn (Builder $query): Builder => $query->whereNotNull('raw_data')->whereRaw("JSON_EXTRACT(raw_data, '$.OrderDetails') IS NOT NULL"))
                    ->toggle(),

                Filter::make('invoice_date')
                    ->form([
                        Forms\Components\DatePicker::make('from_date')
                            ->label('Từ ngày'),
                        Forms\Components\DatePicker::make('to_date')
                            ->label('Đến ngày'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from_date'],
                                fn (Builder $query, $date): Builder => $query->whereDate('invoice_date', '>=', $date),
                            )
                            ->when(
                                $data['to_date'],
                                fn (Builder $query, $date): Builder => $query->whereDate('invoice_date', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->defaultSort('invoice_date', 'desc')
            ->poll('30s');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMShopKeeperInvoices::route('/'),
            'view' => Pages\ViewMShopKeeperInvoice::route('/{record}'),
        ];
    }

    /**
     * Navigation badge hiển thị tổng số hóa đơn
     */
    public static function getNavigationBadge(): ?string
    {
        try {
            $totalCount = static::getModel()::count();

            if ($totalCount === 0) {
                return null;
            }

            return number_format($totalCount);
        } catch (\Exception) {
            return null;
        }
    }

    /**
     * Màu sắc cho navigation badge
     */
    public static function getNavigationBadgeColor(): ?string
    {
        return 'success';
    }

    /**
     * Tooltip cho navigation badge
     */
    public static function getNavigationBadgeTooltip(): ?string
    {
        try {
            $stats = static::getModel()::getSyncStats();
            $revenueStats = static::getModel()::getRevenueStats();

            return "Tổng: " . number_format($stats['total']) . " hóa đơn\n" .
                   "Đã sync: " . number_format($stats['synced']) . "\n" .
                   "Doanh thu hôm nay: " . number_format($revenueStats['today_revenue'], 0, ',', '.') . " ₫\n" .
                   "Sync cuối: " . ($stats['last_sync'] ? $stats['last_sync']->diffForHumans() : 'Chưa có');
        } catch (\Exception) {
            return "Hóa đơn MShopKeeper";
        }
    }
}
