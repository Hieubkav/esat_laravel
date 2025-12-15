<?php

namespace App\Filament\Admin\Resources\MShopKeeperCategoryResource\Pages;

use App\Filament\Admin\Resources\MShopKeeperCategoryResource;
use App\Models\MShopKeeperCategory;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\RepeatableEntry;

class ViewMShopKeeperCategory extends ViewRecord
{
    protected static string $resource = MShopKeeperCategoryResource::class;

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
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('name')
                                    ->label('Tên danh mục')
                                    ->weight('bold'),
                                TextEntry::make('code')
                                    ->label('Mã danh mục')
                                    ->placeholder('—'),
                                TextEntry::make('mshopkeeper_id')
                                    ->label('MShopKeeper ID')
                                    ->copyable()
                                    ->copyMessage('Đã sao chép ID!')
                                    ->copyMessageDuration(1500),
                                TextEntry::make('description')
                                    ->label('Mô tả')
                                    ->placeholder('—')
                                    ->columnSpanFull(),
                            ]),
                    ])
                    ->collapsible(),

                Section::make('Thuộc tính')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('grade')
                                    ->label('Cấp độ')
                                    ->badge()
                                    ->color(fn (string $state): string => match ((int) $state) {
                                        0 => 'primary',
                                        1 => 'success',
                                        2 => 'warning',
                                        default => 'gray',
                                    })
                                    ->formatStateUsing(fn (string $state): string => match ((int) $state) {
                                        0 => 'Cấp 1 (Root)',
                                        1 => 'Cấp 2',
                                        2 => 'Cấp 3',
                                        default => "Cấp " . ((int) $state + 1),
                                    }),
                                TextEntry::make('inactive')
                                    ->label('Trạng thái')
                                    ->badge()
                                    ->color(fn (bool $state): string => $state ? 'danger' : 'success')
                                    ->formatStateUsing(fn (bool $state): string => $state ? 'Không hoạt động' : 'Hoạt động'),
                                TextEntry::make('type')
                                    ->label('Loại')
                                    ->badge()
                                    ->color(fn (string $state): string => $state === 'Leaf' ? 'info' : 'secondary'),
                                TextEntry::make('sort_order')
                                    ->label('Thứ tự')
                                    ->numeric(),
                            ]),
                    ])
                    ->collapsible(),

                Section::make('Cấu trúc phân cấp')
                    ->schema([
                        TextEntry::make('parent.name')
                            ->label('Danh mục cha')
                            ->placeholder('Không có danh mục cha')
                            ->url(fn (MShopKeeperCategory $record): ?string => 
                                $record->parent ? static::$resource::getUrl('view', ['record' => $record->parent]) : null
                            )
                            ->color('primary'),
                        TextEntry::make('full_path')
                            ->label('Đường dẫn đầy đủ')
                            ->placeholder('—')
                            ->copyable()
                            ->copyMessage('Đã sao chép đường dẫn!')
                            ->copyMessageDuration(1500),
                        TextEntry::make('children_count')
                            ->label('Số danh mục con')
                            ->getStateUsing(fn (MShopKeeperCategory $record): int => $record->children->count())
                            ->numeric()
                            ->suffix(' danh mục'),
                    ])
                    ->collapsible()
                    ->visible(fn (MShopKeeperCategory $record): bool => 
                        $record->parent !== null || $record->children->count() > 0 || $record->full_path !== null
                    ),

                Section::make('Danh mục con')
                    ->schema([
                        RepeatableEntry::make('children')
                            ->label('')
                            ->schema([
                                Grid::make(3)
                                    ->schema([
                                        TextEntry::make('name')
                                            ->label('Tên danh mục')
                                            ->weight('bold')
                                            ->url(fn (MShopKeeperCategory $record): string =>
                                                static::$resource::getUrl('view', ['record' => $record])
                                            )
                                            ->color('primary'),
                                        TextEntry::make('code')
                                            ->label('Mã')
                                            ->placeholder('—'),
                                        TextEntry::make('type')
                                            ->label('Loại')
                                            ->badge()
                                            ->color(fn (string $state): string => $state === 'Leaf' ? 'info' : 'secondary'),
                                    ]),
                            ])
                            ->columnSpanFull(),
                    ])
                    ->collapsible()
                    ->visible(fn (MShopKeeperCategory $record): bool => $record->children->count() > 0),

                Section::make('Thông tin đồng bộ')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('sync_status')
                                    ->label('Trạng thái sync')
                                    ->badge()
                                    ->color(fn (?string $state): string => match ($state) {
                                        'synced' => 'success',
                                        'pending' => 'warning',
                                        'failed' => 'danger',
                                        default => 'gray',
                                    })
                                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                                        'synced' => 'Đã đồng bộ',
                                        'pending' => 'Đang chờ',
                                        'failed' => 'Thất bại',
                                        default => 'Chưa xác định',
                                    }),
                                TextEntry::make('last_synced_at')
                                    ->label('Lần sync cuối')
                                    ->dateTime('d/m/Y H:i:s')
                                    ->placeholder('Chưa sync'),
                                TextEntry::make('created_at')
                                    ->label('Ngày tạo')
                                    ->dateTime('d/m/Y H:i:s'),
                                TextEntry::make('updated_at')
                                    ->label('Cập nhật cuối')
                                    ->dateTime('d/m/Y H:i:s'),
                            ]),
                    ])
                    ->collapsible(),
            ]);
    }

    public function getTitle(): string
    {
        return "Chi tiết: {$this->getRecord()->name}";
    }

    public function getHeading(): string
    {
        return $this->getRecord()->name;
    }

    public function getSubheading(): ?string
    {
        $record = $this->getRecord();
        return "MShopKeeper ID: {$record->mshopkeeper_id} • " . 
               ($record->inactive ? 'Không hoạt động' : 'Hoạt động') . 
               " • {$record->type}";
    }
}
