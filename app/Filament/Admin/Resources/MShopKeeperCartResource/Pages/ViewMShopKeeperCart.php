<?php

namespace App\Filament\Admin\Resources\MShopKeeperCartResource\Pages;

use App\Filament\Admin\Resources\MShopKeeperCartResource;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\RepeatableEntry;

class ViewMShopKeeperCart extends ViewRecord
{
    protected static string $resource = MShopKeeperCartResource::class;

    protected function resolveRecord($key): \Illuminate\Database\Eloquent\Model
    {
        return static::getResource()::resolveRecordRouteBinding($key)
            ->load(['customer', 'items.product']);
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Thông tin khách hàng')
                    ->schema([
                        TextEntry::make('customer.name')
                            ->label('Tên khách hàng'),

                        TextEntry::make('customer.email')
                            ->label('Email')
                            ->placeholder('Chưa có email'),

                        TextEntry::make('customer.phone')
                            ->label('Số điện thoại')
                            ->placeholder('Chưa có số điện thoại'),

                        TextEntry::make('customer.address')
                            ->label('Địa chỉ')
                            ->placeholder('Chưa có địa chỉ'),
                    ])->columns(2),

                Section::make('Thông tin giỏ hàng')
                    ->schema([
                        TextEntry::make('total_quantity')
                            ->label('Tổng số sản phẩm')
                            ->badge()
                            ->color('info'),

                        TextEntry::make('total_price')
                            ->label('Tổng giá trị')
                            ->formatStateUsing(fn ($state) => number_format($state) . 'đ')
                            ->badge()
                            ->color('success'),

                        TextEntry::make('created_at')
                            ->label('Ngày tạo')
                            ->dateTime('d/m/Y H:i:s'),

                        TextEntry::make('updated_at')
                            ->label('Cập nhật lần cuối')
                            ->dateTime('d/m/Y H:i:s'),
                    ])->columns(2),

                Section::make('Chi tiết sản phẩm')
                    ->schema([
                        RepeatableEntry::make('items')
                            ->label('')
                            ->schema([
                                TextEntry::make('product.name')
                                    ->label('Tên sản phẩm'),

                                TextEntry::make('product.code')
                                    ->label('Mã sản phẩm'),

                                TextEntry::make('quantity')
                                    ->label('Số lượng')
                                    ->badge()
                                    ->color('info'),

                                TextEntry::make('product.selling_price')
                                    ->label('Đơn giá')
                                    ->formatStateUsing(fn ($state) => number_format($state) . 'đ'),

                                TextEntry::make('subtotal')
                                    ->label('Thành tiền')
                                    ->formatStateUsing(fn ($state) => number_format($state) . 'đ')
                                    ->weight('bold'),
                            ])
                            ->columns(5)
                            ->grid(1),
                    ]),
            ]);
    }

    public function getTitle(): string
    {
        return 'Chi tiết giỏ hàng: ' . ($this->record->customer->name ?? 'N/A');
    }

    protected function getHeaderActions(): array
    {
        // Ẩn nút "Tạo đơn hàng" vì đây là flow cũ không còn sử dụng
        return [];
    }
}
