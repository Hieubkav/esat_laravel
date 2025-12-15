<?php

namespace App\Filament\Admin\Resources\MShopKeeperCustomerResource\Pages;

use App\Filament\Admin\Resources\MShopKeeperCustomerResource;
use App\Models\MShopKeeperCustomer;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\Section;

class ViewMShopKeeperCustomer extends ViewRecord
{
    protected static string $resource = MShopKeeperCustomerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('back')
                ->label('Quay lại danh sách')
                ->icon('heroicon-o-arrow-left')
                ->color('gray')
                ->url($this->getResource()::getUrl('index')),
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Thông tin cơ bản')
                    ->schema([
                        TextEntry::make('mshopkeeper_id')
                            ->label('MShopKeeper ID')
                            ->copyable()
                            ->copyMessage('Đã sao chép ID!')
                            ->copyMessageDuration(1500),
                        TextEntry::make('code')
                            ->label('Mã khách hàng')
                            ->copyable()
                            ->copyMessage('Đã sao chép mã khách hàng!')
                            ->copyMessageDuration(1500)
                            ->weight('bold')
                            ->placeholder('—'),
                        TextEntry::make('name')
                            ->label('Tên khách hàng')
                            ->weight('bold'),
                        TextEntry::make('gender_text')
                            ->label('Giới tính')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'Nam' => 'primary',
                                'Nữ' => 'success',
                                default => 'gray',
                            }),
                    ])
                    ->columns(2),

                Section::make('Thông tin liên hệ')
                    ->schema([
                        TextEntry::make('tel')
                            ->label('Số điện thoại')
                            ->copyable()
                            ->copyMessage('Đã sao chép số điện thoại!')
                            ->copyMessageDuration(1500)
                            ->placeholder('—'),
                        TextEntry::make('normalized_tel')
                            ->label('SĐT chuẩn hóa')
                            ->copyable()
                            ->copyMessage('Đã sao chép!')
                            ->copyMessageDuration(1500)
                            ->placeholder('—'),
                        TextEntry::make('standard_tel')
                            ->label('SĐT tiêu chuẩn')
                            ->copyable()
                            ->copyMessage('Đã sao chép!')
                            ->copyMessageDuration(1500)
                            ->placeholder('—'),
                        TextEntry::make('email')
                            ->label('Email')
                            ->copyable()
                            ->copyMessage('Đã sao chép email!')
                            ->copyMessageDuration(1500)
                            ->placeholder('—'),
                    ])
                    ->columns(2),

                Section::make('Địa chỉ')
                    ->schema([
                        TextEntry::make('addr')
                            ->label('Địa chỉ')
                            ->placeholder('—')
                            ->columnSpanFull(),
                        TextEntry::make('province_addr')
                            ->label('Tỉnh/Thành phố')
                            ->placeholder('—'),
                        TextEntry::make('district_addr')
                            ->label('Quận/Huyện')
                            ->placeholder('—'),
                        TextEntry::make('commune_addr')
                            ->label('Phường/Xã')
                            ->placeholder('—'),
                    ])
                    ->columns(3),

                Section::make('Thông tin thành viên')
                    ->schema([
                        TextEntry::make('membership_code')
                            ->label('Mã thẻ thành viên')
                            ->copyable()
                            ->copyMessage('Đã sao chép mã thẻ!')
                            ->copyMessageDuration(1500)
                            ->placeholder('—'),
                        TextEntry::make('member_level_id')
                            ->label('ID hạng thẻ')
                            ->copyable()
                            ->copyMessage('Đã sao chép!')
                            ->copyMessageDuration(1500)
                            ->placeholder('—'),
                        TextEntry::make('member_level_name')
                            ->label('Hạng thẻ thành viên')
                            ->badge()
                            ->color(fn (?string $state): string => match ($state) {
                                'Vàng' => 'warning',
                                'Bạc' => 'gray',
                                'Kim cương' => 'primary',
                                'Bạch kim' => 'success',
                                default => 'secondary',
                            })
                            ->placeholder('—'),
                    ])
                    ->columns(2),

                Section::make('Thông tin bổ sung')
                    ->schema([
                        TextEntry::make('description')
                            ->label('Mô tả')
                            ->placeholder('—')
                            ->columnSpanFull(),
                        TextEntry::make('identify_number')
                            ->label('Số CMND/CCCD')
                            ->copyable()
                            ->copyMessage('Đã sao chép!')
                            ->copyMessageDuration(1500)
                            ->placeholder('—'),
                    ])
                    ->columns(2),

                Section::make('Thông tin đăng nhập')
                    ->schema([
                        TextEntry::make('plain_password')
                            ->label('Mật khẩu')
                            ->copyable()
                            ->copyMessage('Đã sao chép mật khẩu!')
                            ->copyMessageDuration(1500)
                            ->icon('heroicon-o-key')
                            ->color('warning')
                            ->weight('bold')
                            ->placeholder('Chưa có mật khẩu')
                            ->tooltip('Click để sao chép mật khẩu')
                            ->extraAttributes(['class' => 'font-mono bg-yellow-50 px-2 py-1 rounded border']),
                        TextEntry::make('password')
                            ->label('Mật khẩu (Hash)')
                            ->copyable()
                            ->copyMessage('Đã sao chép hash!')
                            ->copyMessageDuration(1500)
                            ->icon('heroicon-o-lock-closed')
                            ->color('gray')
                            ->placeholder('—')
                            ->formatStateUsing(fn (?string $state): string => $state ? substr($state, 0, 20) . '...' : '—')
                            ->tooltip('Mật khẩu đã mã hóa')
                            ->extraAttributes(['class' => 'font-mono text-xs']),
                    ])
                    ->columns(2)
                    ->collapsible()
                    ->collapsed(false),

                Section::make('Thông tin sync')
                    ->schema([
                        TextEntry::make('sync_status')
                            ->label('Trạng thái sync')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'synced' => 'success',
                                'error' => 'danger',
                                'pending' => 'warning',
                                default => 'gray',
                            })
                            ->formatStateUsing(fn (string $state): string => match ($state) {
                                'synced' => 'Đã sync',
                                'error' => 'Lỗi',
                                'pending' => 'Chờ sync',
                                default => $state,
                            }),
                        TextEntry::make('last_synced_at')
                            ->label('Sync lần cuối')
                            ->dateTime('d/m/Y H:i:s')
                            ->placeholder('Chưa sync'),
                        TextEntry::make('created_at')
                            ->label('Ngày tạo')
                            ->dateTime('d/m/Y H:i:s'),
                        TextEntry::make('updated_at')
                            ->label('Ngày cập nhật')
                            ->dateTime('d/m/Y H:i:s'),
                    ])
                    ->columns(2)
                    ->collapsible(),
            ]);
    }



    public function getTitle(): string
    {
        $record = $this->getRecord();
        return 'Chi tiết khách hàng: ' . ($record->name ?? 'N/A');
    }

    public function getHeading(): string
    {
        $record = $this->getRecord();
        return 'Chi tiết khách hàng: ' . ($record->name ?? 'N/A');
    }
}
