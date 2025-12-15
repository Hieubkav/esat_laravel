<?php

namespace App\Filament\Admin\Resources\CustomerResource\Pages;

use App\Filament\Admin\Resources\CustomerResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\Section;

class ViewCustomer extends ViewRecord
{
    protected static string $resource = CustomerResource::class;

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Load the count for display
        $this->record->loadCount('mshopkeeperCarts');
        return $data;
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->label('Chỉnh sửa'),
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('⚠️ Cảnh báo bảo mật')
                    ->schema([
                        \Filament\Infolists\Components\TextEntry::make('security_warning')
                            ->label('')
                            ->formatStateUsing(fn () => 'Mật khẩu gốc chỉ nên được xem khi thực sự cần thiết. Đây là thông tin nhạy cảm của khách hàng.')
                            ->color('warning')
                            ->icon('heroicon-o-exclamation-triangle'),
                    ])
                    ->collapsible()
                    ->collapsed(false)
                    ->description('Thông tin bảo mật quan trọng'),
                Section::make('Thông tin khách hàng')
                    ->schema([
                        TextEntry::make('name')
                            ->label('Tên khách hàng'),

                        TextEntry::make('email')
                            ->label('Email')
                            ->placeholder('Chưa có email'),

                        TextEntry::make('phone')
                            ->label('Số điện thoại')
                            ->placeholder('Chưa có số điện thoại'),

                        TextEntry::make('address')
                            ->label('Địa chỉ')
                            ->placeholder('Chưa có địa chỉ'),

                        TextEntry::make('plain_password')
                            ->label('Mật khẩu gốc')
                            ->formatStateUsing(fn ($state) => $state ?: 'Chưa có mật khẩu gốc')
                            ->copyable()
                            ->copyMessage('Đã sao chép mật khẩu')
                            ->copyMessageDuration(1500)
                            ->color(fn ($state) => $state ? 'success' : 'warning'),

                        TextEntry::make('password')
                            ->label('Mật khẩu (Hash)')
                            ->formatStateUsing(fn ($state) => $state)
                            ->copyable()
                            ->copyMessage('Đã sao chép mật khẩu hash')
                            ->copyMessageDuration(1500)
                            ->color('gray'),
                    ])->columns(2),

                Section::make('Thông tin hệ thống')
                    ->schema([
                        TextEntry::make('status')
                            ->label('Trạng thái')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'active' => 'success',
                                'inactive' => 'danger',
                                default => 'gray',
                            }),

                        TextEntry::make('order')
                            ->label('Thứ tự hiển thị'),

                        TextEntry::make('created_at')
                            ->label('Ngày tạo')
                            ->dateTime('d/m/Y H:i:s'),

                        TextEntry::make('updated_at')
                            ->label('Cập nhật lần cuối')
                            ->dateTime('d/m/Y H:i:s'),
                    ])->columns(2),

                Section::make('Giỏ hàng MShopKeeper')
                    ->schema([
                        TextEntry::make('mshopkeeper_carts_count')
                            ->label('Số giỏ hàng')
                            ->formatStateUsing(fn ($state) => $state ?? 0)
                            ->badge()
                            ->color('info'),
                    ])->columns(1),
            ]);
    }

    public function getTitle(): string
    {
        return 'Chi tiết khách hàng: ' . $this->record->name;
    }
}
