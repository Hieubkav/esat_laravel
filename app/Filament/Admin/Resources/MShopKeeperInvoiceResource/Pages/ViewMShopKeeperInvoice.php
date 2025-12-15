<?php

namespace App\Filament\Admin\Resources\MShopKeeperInvoiceResource\Pages;

use App\Filament\Admin\Resources\MShopKeeperInvoiceResource;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Support\Enums\FontWeight;

class ViewMShopKeeperInvoice extends ViewRecord
{
    protected static string $resource = MShopKeeperInvoiceResource::class;

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Thông tin hóa đơn')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('invoice_number')
                                    ->label('Số hóa đơn')
                                    ->weight(FontWeight::Bold)
                                    ->color('primary'),
                                
                                TextEntry::make('invoice_date')
                                    ->label('Ngày tạo')
                                    ->dateTime('d/m/Y H:i'),
                                
                                TextEntry::make('branch_name')
                                    ->label('Chi nhánh'),
                                
                                TextEntry::make('sale_channel_name')
                                    ->label('Kênh bán hàng')
                                    ->badge()
                                    ->color(fn ($state) => match(strtolower($state ?? '')) {
                                        'website' => 'success',
                                        'facebook' => 'info',
                                        'shopee' => 'warning',
                                        'lazada' => 'primary',
                                        'tiki' => 'secondary',
                                        default => 'gray',
                                    }),
                            ]),
                    ]),

                Section::make('Thông tin khách hàng')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('customer_name')
                                    ->label('Tên khách hàng')
                                    ->weight(FontWeight::SemiBold),
                                
                                TextEntry::make('customer_tel')
                                    ->label('Số điện thoại'),
                                
                                TextEntry::make('customer_address')
                                    ->label('Địa chỉ')
                                    ->columnSpanFull(),
                                
                                TextEntry::make('member_level_name')
                                    ->label('Hạng thẻ')
                                    ->badge()
                                    ->color('info'),
                            ]),
                    ]),

                Section::make('Thông tin tài chính')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('total_item_amount')
                                    ->label('Tiền hàng')
                                    ->money('VND'),
                                
                                TextEntry::make('discount_amount')
                                    ->label('Giảm giá')
                                    ->money('VND')
                                    ->color('warning'),
                                
                                TextEntry::make('total_amount')
                                    ->label('Tổng tiền')
                                    ->money('VND')
                                    ->weight(FontWeight::Bold)
                                    ->color('success'),
                                
                                TextEntry::make('cash_amount')
                                    ->label('Tiền mặt')
                                    ->money('VND'),
                                
                                TextEntry::make('card_amount')
                                    ->label('Tiền thẻ')
                                    ->money('VND'),
                                
                                TextEntry::make('voucher_amount')
                                    ->label('Voucher')
                                    ->money('VND'),
                                
                                TextEntry::make('debit_amount')
                                    ->label('Nợ')
                                    ->money('VND')
                                    ->color('danger'),
                                
                                TextEntry::make('actual_amount')
                                    ->label('Thực thu')
                                    ->money('VND')
                                    ->weight(FontWeight::Bold)
                                    ->color('primary'),
                                
                                TextEntry::make('point')
                                    ->label('Điểm tích lũy')
                                    ->suffix(' điểm'),
                            ]),
                    ]),

                Section::make('Trạng thái & Giao hàng')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('payment_status')
                                    ->label('Trạng thái thanh toán')
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
                                
                                TextEntry::make('is_cod')
                                    ->label('Thu tiền khi giao')
                                    ->formatStateUsing(fn ($state) => $state ? 'Có' : 'Không')
                                    ->badge()
                                    ->color(fn ($state) => $state ? 'warning' : 'success'),
                                
                                TextEntry::make('delivery_code')
                                    ->label('Mã vận đơn')
                                    ->copyable(),
                                
                                TextEntry::make('shipping_partner_name')
                                    ->label('Đối tác vận chuyển'),
                                
                                TextEntry::make('delivery_date')
                                    ->label('Ngày giao hàng')
                                    ->dateTime('d/m/Y H:i'),
                            ]),
                    ]),

                Section::make('Nhân viên')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('cashier')
                                    ->label('Thu ngân'),
                                
                                TextEntry::make('sale_staff')
                                    ->label('Nhân viên bán hàng'),
                            ]),
                    ]),

                Section::make('Thông tin đồng bộ')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('sync_status')
                                    ->label('Trạng thái đồng bộ')
                                    ->formatStateUsing(fn ($state) => match($state) {
                                        'synced' => 'Đã đồng bộ',
                                        'error' => 'Lỗi',
                                        'pending' => 'Chờ xử lý',
                                        default => $state,
                                    })
                                    ->badge()
                                    ->color(fn ($state) => match($state) {
                                        'synced' => 'success',
                                        'error' => 'danger',
                                        'pending' => 'warning',
                                        default => 'gray',
                                    }),
                                
                                TextEntry::make('last_synced_at')
                                    ->label('Lần sync cuối')
                                    ->dateTime('d/m/Y H:i'),
                                
                                TextEntry::make('mshopkeeper_invoice_id')
                                    ->label('ID MShopKeeper')
                                    ->copyable(),
                                
                                TextEntry::make('sync_error')
                                    ->label('Lỗi đồng bộ')
                                    ->color('danger')
                                    ->columnSpanFull()
                                    ->visible(fn ($record) => !empty($record->sync_error)),
                            ]),
                    ]),

                Section::make('Chi tiết sản phẩm')
                    ->schema([


                        // Table header với styling đẹp
                        Grid::make(6)
                            ->schema([
                                TextEntry::make('dummy_sku')
                                    ->label('')
                                    ->formatStateUsing(fn () => '**Mã SP**')
                                    ->html()
                                    ->extraAttributes(['class' => 'text-sm font-bold text-gray-700 dark:text-gray-300']),

                                TextEntry::make('dummy_name')
                                    ->label('')
                                    ->formatStateUsing(fn () => '**Tên sản phẩm**')
                                    ->html()
                                    ->columnSpan(2)
                                    ->extraAttributes(['class' => 'text-sm font-bold text-gray-700 dark:text-gray-300']),

                                TextEntry::make('dummy_quantity')
                                    ->label('')
                                    ->formatStateUsing(fn () => '**SL**')
                                    ->html()
                                    ->alignCenter()
                                    ->extraAttributes(['class' => 'text-sm font-bold text-gray-700 dark:text-gray-300']),

                                TextEntry::make('dummy_price')
                                    ->label('')
                                    ->formatStateUsing(fn () => '**Đơn giá**')
                                    ->html()
                                    ->alignEnd()
                                    ->extraAttributes(['class' => 'text-sm font-bold text-gray-700 dark:text-gray-300']),

                                TextEntry::make('dummy_amount')
                                    ->label('')
                                    ->formatStateUsing(fn () => '**Thành tiền**')
                                    ->html()
                                    ->alignEnd()
                                    ->extraAttributes(['class' => 'text-sm font-bold text-gray-700 dark:text-gray-300']),
                            ])
                            ->extraAttributes(['class' => 'border-b-2 border-gray-300 dark:border-gray-600 pb-3 mb-4 bg-gray-50 dark:bg-gray-800 rounded-t-lg px-4 py-2']),

                        RepeatableEntry::make('order_details')
                            ->label('')
                            ->schema([
                                Grid::make(6)
                                    ->schema([
                                        TextEntry::make('SKU')
                                            ->label('')
                                            ->weight(FontWeight::SemiBold)
                                            ->color('primary')
                                            ->size(TextEntry\TextEntrySize::Small),

                                        TextEntry::make('Name')
                                            ->label('')
                                            ->weight(FontWeight::Medium)
                                            ->limit(40)
                                            ->tooltip(fn ($state) => $state)
                                            ->columnSpan(2)
                                            ->size(TextEntry\TextEntrySize::Small),

                                        TextEntry::make('Quantity')
                                            ->label('')
                                            ->numeric(0)
                                            ->alignCenter()
                                            ->size(TextEntry\TextEntrySize::Small),

                                        TextEntry::make('UnitPrice')
                                            ->label('')
                                            ->money('VND')
                                            ->alignEnd()
                                            ->size(TextEntry\TextEntrySize::Small),

                                        TextEntry::make('Amount')
                                            ->label('')
                                            ->money('VND')
                                            ->weight(FontWeight::Bold)
                                            ->color('success')
                                            ->alignEnd()
                                            ->size(TextEntry\TextEntrySize::Small),
                                    ])
                                    ->extraAttributes(['class' => 'py-3 px-4 border-b border-gray-200 dark:border-gray-700']),
                            ])
                            ->contained(false),

                        // Summary section với styling đẹp
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('unique_products_count')
                                    ->label('Số loại SP')
                                    ->badge()
                                    ->color('info')
                                    ->size(TextEntry\TextEntrySize::Large),

                                TextEntry::make('total_items')
                                    ->label('Tổng số lượng')
                                    ->badge()
                                    ->color('warning')
                                    ->size(TextEntry\TextEntrySize::Large),

                                TextEntry::make('total_item_amount')
                                    ->label('Tổng tiền hàng')
                                    ->money('VND')
                                    ->weight(FontWeight::Bold)
                                    ->color('success')
                                    ->size(TextEntry\TextEntrySize::Large),
                            ])
                            ->extraAttributes(['class' => 'mt-6 pt-4 border-t-2 border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-800 rounded-b-lg px-4 py-4']),
                    ])
                    ->collapsible(),

                Section::make('Ghi chú')
                    ->schema([
                        TextEntry::make('note')
                            ->label('Ghi chú')
                            ->columnSpanFull(),
                    ])
                    ->visible(fn ($record) => !empty($record->note)),
            ]);
    }
}
