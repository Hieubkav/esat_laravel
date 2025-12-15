<?php

namespace App\Filament\Admin\Resources\MShopKeeperCustomerPointResource\Pages;

use App\Filament\Admin\Resources\MShopKeeperCustomerPointResource;
use App\Models\MShopKeeperCustomerPoint;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\Grid;
use Filament\Support\Enums\FontWeight;

class ViewMShopKeeperCustomerPoint extends ViewRecord
{
    protected static string $resource = MShopKeeperCustomerPointResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('back')
                ->label('Quay lại')
                ->url($this->getResource()::getUrl('index'))
                ->icon('heroicon-o-arrow-left')
                ->color('gray'),
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Thông tin khách hàng')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('full_name')
                                    ->label('Tên đầy đủ')
                                    ->weight(FontWeight::Bold)
                                    ->size('lg'),

                                TextEntry::make('tel')
                                    ->label('Số điện thoại')
                                    ->copyable()
                                    ->copyMessage('Đã copy số điện thoại')
                                    ->formatStateUsing(function (?string $state): string {
                                        return $state ?: 'Không có';
                                    }),

                                TextEntry::make('original_id')
                                    ->label('ID gốc')
                                    ->copyable()
                                    ->copyMessage('Đã copy ID')
                                    ->fontFamily('mono'),
                            ]),
                    ])
                    ->collapsible(),

                Section::make('Thông tin điểm thưởng')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('total_point')
                                    ->label('Tổng điểm')
                                    ->formatStateUsing(function (int $state): string {
                                        return number_format($state) . ' điểm';
                                    })
                                    ->weight(FontWeight::Bold)
                                    ->size('lg')
                                    ->color(function (int $state): string {
                                        return match (true) {
                                            $state >= 5000 => 'danger',
                                            $state >= 2000 => 'warning',
                                            $state >= 1000 => 'info',
                                            $state >= 500 => 'success',
                                            default => 'gray'
                                        };
                                    }),

                                TextEntry::make('point_level')
                                    ->label('Hạng thẻ')
                                    ->getStateUsing(function (MShopKeeperCustomerPoint $record): string {
                                        return $record->point_level;
                                    })
                                    ->badge()
                                    ->color(function (string $state): string {
                                        return match ($state) {
                                            'VIP' => 'danger',
                                            'Vàng' => 'warning',
                                            'Bạc' => 'info',
                                            'Đồng' => 'success',
                                            default => 'gray'
                                        };
                                    }),

                                TextEntry::make('formatted_points')
                                    ->label('Điểm định dạng')
                                    ->getStateUsing(function (MShopKeeperCustomerPoint $record): string {
                                        return $record->formatted_points;
                                    }),
                            ]),
                    ])
                    ->collapsible(),

                Section::make('Thông tin đồng bộ')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('sync_status')
                                    ->label('Trạng thái sync')
                                    ->formatStateUsing(function (string $state): string {
                                        return match($state) {
                                            'synced' => 'Đã đồng bộ',
                                            'pending' => 'Chờ đồng bộ',
                                            'error' => 'Lỗi đồng bộ',
                                            default => $state
                                        };
                                    })
                                    ->badge()
                                    ->color(function (string $state): string {
                                        return match($state) {
                                            'synced' => 'success',
                                            'pending' => 'warning',
                                            'error' => 'danger',
                                            default => 'gray'
                                        };
                                    }),

                                TextEntry::make('last_synced_at')
                                    ->label('Lần sync cuối')
                                    ->dateTime('d/m/Y H:i:s')
                                    ->since()
                                    ->placeholder('Chưa sync'),

                                TextEntry::make('sync_error')
                                    ->label('Lỗi sync')
                                    ->placeholder('Không có lỗi')
                                    ->color('danger')
                                    ->visible(fn (MShopKeeperCustomerPoint $record): bool => !empty($record->sync_error)),
                            ]),
                    ])
                    ->collapsible(),

                Section::make('Thông tin hệ thống')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('created_at')
                                    ->label('Tạo lúc')
                                    ->dateTime('d/m/Y H:i:s'),

                                TextEntry::make('updated_at')
                                    ->label('Cập nhật lúc')
                                    ->dateTime('d/m/Y H:i:s'),
                            ]),
                    ])
                    ->collapsible()
                    ->collapsed(),

                Section::make('Dữ liệu thô từ API')
                    ->schema([
                        TextEntry::make('raw_data')
                            ->label('Raw Data')
                            ->formatStateUsing(function (?array $state): string {
                                return $state ? json_encode($state, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : 'Không có dữ liệu';
                            })
                            ->fontFamily('mono')
                            ->placeholder('Không có dữ liệu thô'),
                    ])
                    ->collapsible()
                    ->collapsed(),
            ]);
    }

    public function getTitle(): string
    {
        return 'Chi tiết điểm thẻ thành viên: ' . $this->record->full_name;
    }

    public function getHeading(): string
    {
        return $this->record->full_name;
    }

    public function getSubheading(): ?string
    {
        return $this->record->formatted_points . ' - Hạng ' . $this->record->point_level;
    }
}
