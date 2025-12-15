<?php

namespace App\Filament\Admin\Resources\MShopKeeperInventoryItemResource\Pages;

use App\Filament\Admin\Resources\MShopKeeperInventoryItemResource;
use App\Models\MShopKeeperInventoryItem;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\Grid;


use Filament\Support\Enums\FontWeight;

class ViewMShopKeeperInventoryItem extends ViewRecord
{
    protected static string $resource = MShopKeeperInventoryItemResource::class;

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
                Section::make('Thông tin cơ bản')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                ImageEntry::make('picture')
                                    ->label('Ảnh sản phẩm')
                                    ->size(200)
                                    ->defaultImageUrl('/images/no-image.svg')
                                    ->extraAttributes(['class' => 'rounded-lg shadow-md']),

                                Grid::make(1)
                                    ->schema([
                                        TextEntry::make('name')
                                            ->label('Tên hàng hóa')
                                            ->weight(FontWeight::Bold)
                                            ->size('lg'),

                                        TextEntry::make('code')
                                            ->label('Mã hàng')
                                            ->copyable()
                                            ->copyMessage('Đã copy mã hàng')
                                            ->fontFamily('mono'),

                                        TextEntry::make('selling_price')
                                            ->label('Giá bán')
                                            ->formatStateUsing(function ($state): string {
                                                return number_format((float)$state) . ' VND';
                                            })
                                            ->weight(FontWeight::Bold)
                                            ->color('success'),
                                    ]),

                                Grid::make(1)
                                    ->schema([
                                        TextEntry::make('total_on_hand')
                                            ->label('Tồn kho')
                                            ->formatStateUsing(function ($state): string {
                                                return number_format((int)$state);
                                            })
                                            ->weight(FontWeight::Bold)
                                            ->size('lg'),

                                        TextEntry::make('total_on_hand')
                                            ->label('Trạng thái kho')
                                            ->formatStateUsing(function (int $state): string {
                                                return match (true) {
                                                    $state > 100 => 'Nhiều hàng',
                                                    $state > 10 => 'Vừa đủ',
                                                    $state > 0 => 'Ít hàng',
                                                    default => 'Hết hàng'
                                                };
                                            })
                                            ->badge()
                                            ->color(function (int $state): string {
                                                return match (true) {
                                                    $state > 100 => 'success',
                                                    $state > 10 => 'info',
                                                    $state > 0 => 'warning',
                                                    default => 'danger'
                                                };
                                            }),

                                        TextEntry::make('item_type')
                                            ->label('Loại hàng hóa')
                                            ->formatStateUsing(function (int $state): string {
                                                return match($state) {
                                                    1 => 'Hàng Hoá',
                                                    2 => 'Combo',
                                                    4 => 'Dịch vụ',
                                                    default => 'Không xác định'
                                                };
                                            })
                                            ->badge()
                                            ->color('primary'),

                                        TextEntry::make('is_visible')
                                            ->label('Hiển thị trên web')
                                            ->formatStateUsing(function (bool $state): string {
                                                return $state ? 'Có' : 'Không';
                                            })
                                            ->badge()
                                            ->color(function (bool $state): string {
                                                return $state ? 'success' : 'danger';
                                            }),

                                        TextEntry::make('is_featured')
                                            ->label('Sản phẩm nổi bật')
                                            ->formatStateUsing(function (bool $state): string {
                                                return $state ? 'Nổi bật' : 'Thường';
                                            })
                                            ->badge()
                                            ->color(function (bool $state): string {
                                                return $state ? 'warning' : 'secondary';
                                            }),
                                    ]),
                            ]),
                    ])
                    ->collapsible(),

                Section::make('Thông tin bổ sung')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('barcode')
                                    ->label('Mã vạch')
                                    ->copyable()
                                    ->copyMessage('Đã copy mã vạch')
                                    ->fontFamily('mono')
                                    ->placeholder('Không có'),

                                TextEntry::make('unit_name')
                                    ->label('Đơn vị tính')
                                    ->placeholder('Không có'),

                                TextEntry::make('color')
                                    ->label('Màu sắc')
                                    ->placeholder('Không có'),

                                TextEntry::make('size')
                                    ->label('Kích thước')
                                    ->placeholder('Không có'),

                                TextEntry::make('material')
                                    ->label('Chất liệu')
                                    ->placeholder('Không có'),

                                TextEntry::make('inactive')
                                    ->label('Trạng thái')
                                    ->formatStateUsing(function (bool $state): string {
                                        return $state ? 'Ngừng kinh doanh' : 'Đang hoạt động';
                                    })
                                    ->badge()
                                    ->color(function (bool $state): string {
                                        return $state ? 'danger' : 'success';
                                    }),
                            ]),

                        TextEntry::make('description')
                            ->label('Mô tả')
                            ->placeholder('Không có mô tả')
                            ->columnSpanFull(),
                    ])
                    ->collapsible()
                    ->collapsed(),

                Section::make('Thư viện ảnh sản phẩm')
                    ->schema([
                        TextEntry::make('gallery_images_count')
                            ->label('Số lượng ảnh')
                            ->getStateUsing(function (MShopKeeperInventoryItem $record): string {
                                $count = $record->gallery_images_count;
                                return $count . ' ảnh';
                            })
                            ->badge()
                            ->color(function (MShopKeeperInventoryItem $record): string {
                                return $record->gallery_images_count > 1 ? 'success' : 'gray';
                            }),

                        TextEntry::make('gallery_images')
                            ->label('Danh sách ảnh')
                            ->getStateUsing(function (MShopKeeperInventoryItem $record): string {
                                $images = $record->gallery_images;

                                if (empty($images)) {
                                    return 'Không có ảnh nào';
                                }

                                $imageList = [];
                                foreach ($images as $index => $imageUrl) {
                                    $imageList[] = "📷 Ảnh " . ($index + 1) . ": " . basename(parse_url($imageUrl, PHP_URL_PATH));
                                }

                                return implode("\n", $imageList);
                            })
                            ->columnSpanFull()
                            ->placeholder('Không có ảnh'),

                        TextEntry::make('gallery_preview')
                            ->label('Xem trước ảnh')
                            ->getStateUsing(function (MShopKeeperInventoryItem $record): string {
                                $images = $record->gallery_images;

                                if (empty($images)) {
                                    return 'Không có ảnh để xem trước';
                                }

                                // Tạo HTML để hiển thị ảnh
                                $html = '<div style="display: flex; flex-wrap: wrap; gap: 10px;">';
                                foreach ($images as $index => $imageUrl) {
                                    $html .= '<div style="text-align: center;">';
                                    $html .= '<img src="' . $imageUrl . '" style="width: 150px; height: 150px; object-fit: cover; border-radius: 8px; border: 1px solid #ddd;" alt="Ảnh ' . ($index + 1) . '" />';
                                    $html .= '<p style="margin: 5px 0; font-size: 12px;">Ảnh ' . ($index + 1) . '</p>';
                                    $html .= '</div>';
                                }
                                $html .= '</div>';

                                return $html;
                            })
                            ->html()
                            ->columnSpanFull(),
                    ])
                    ->collapsible()
                    ->collapsed()
                    ->visible(function (MShopKeeperInventoryItem $record): bool {
                        return $record->gallery_images_count > 0;
                    }),


            ]);
    }

    public function getTitle(): string
    {
        return 'Chi tiết hàng hóa: ' . $this->record->name;
    }

    public function getHeading(): string
    {
        return $this->record->name;
    }

    public function getSubheading(): ?string
    {
        $itemType = match($this->record->item_type) {
            1 => 'Hàng Hoá',
            2 => 'Combo',
            4 => 'Dịch vụ',
            default => 'Không xác định'
        };

        $price = number_format($this->record->selling_price) . ' VND';

        return $this->record->code . ' - ' . $itemType . ' - ' . $price;
    }
}
