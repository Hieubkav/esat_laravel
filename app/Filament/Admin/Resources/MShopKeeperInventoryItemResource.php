<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\MShopKeeperInventoryItemResource\Pages;
use App\Models\MShopKeeperInventoryItem;
use App\Services\MShopKeeperService;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Notifications\Notification;
use App\Constants\NavigationGroups;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class MShopKeeperInventoryItemResource extends Resource
{
    protected static ?string $model = MShopKeeperInventoryItem::class;

    protected static ?string $navigationIcon = 'heroicon-o-cube';

    protected static ?string $navigationGroup = NavigationGroups::ECOMMERCE;

    protected static ?string $navigationLabel = 'Hàng hóa MShopKeeper';

    /**
     * Override để đảm bảo navigation group được xử lý an toàn
     */
    public static function getNavigationGroup(): ?string
    {
        try {
            return static::$navigationGroup ?? NavigationGroups::ECOMMERCE;
        } catch (\Throwable $e) {
            Log::error('Error getting navigation group for MShopKeeperInventoryItemResource', [
                'error' => $e->getMessage()
            ]);
            return 'Thương mại điện tử'; // Fallback value
        }
    }

    protected static ?string $modelLabel = 'hàng hóa MShopKeeper';

    protected static ?string $pluralModelLabel = 'hàng hóa MShopKeeper';

    protected static ?int $navigationSort = 15;

    public static function form(Form $form): Form
    {
        // Không cho phép tạo/sửa vì dữ liệu từ API
        return $form->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('picture')
                    ->label('Hình ảnh')
                    ->getStateUsing(function (MShopKeeperInventoryItem $record): string {
                        // Lấy ảnh đầu tiên từ gallery hoặc picture field
                        $galleryImages = $record->gallery_images;
                        $imageUrl = !empty($galleryImages) ? $galleryImages[0] : $record->picture;

                        if (!$imageUrl) {
                            return '<div class="flex items-center justify-center w-12 h-12 bg-gray-100 rounded-full text-gray-400 text-xs">
                                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd" />
                                        </svg>
                                    </div>';
                        }

                        return '<div class="flex items-center justify-center">
                                    <img src="' . htmlspecialchars($imageUrl) . '"
                                         class="w-12 h-12 object-cover rounded-full border-2 border-gray-200 hover:border-blue-300 hover:scale-110 transition-all duration-200 cursor-pointer"
                                         loading="lazy"
                                         alt="' . htmlspecialchars($record->name) . '"
                                         title="Click để xem chi tiết sản phẩm"
                                         onerror="this.style.display=\'none\'; this.nextElementSibling.style.display=\'flex\';"
                                    />
                                    <div class="hidden w-12 h-12 bg-red-100 rounded-full items-center justify-center text-red-400 text-xs">
                                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                </div>';
                    })
                    ->html()
                    ->tooltip(function (MShopKeeperInventoryItem $record): string {
                        return $record->name . ' (' . $record->images_count . ' ảnh)';
                    })
                    ->toggleable()
                    ->width('70px')
                    ->alignCenter(),

                TextColumn::make('images_count')
                    ->label('SL Ảnh')
                    ->formatStateUsing(function (int $state): string {
                        return (string) $state;
                    })
                    ->badge()
                    ->color(function (int $state): string {
                        return match (true) {
                            $state === 0 => 'gray',
                            $state === 1 => 'info',
                            $state > 1 => 'success',
                            default => 'gray'
                        };
                    })
                    ->tooltip(function (int $state): string {
                        if ($state === 0) {
                            return 'Không có ảnh';
                        } elseif ($state === 1) {
                            return 'Có 1 ảnh';
                        } else {
                            return "Có {$state} ảnh";
                        }
                    })
                    ->sortable()
                    ->toggleable()
                    ->width('80px'),

                TextColumn::make('code')
                    ->label('Mã hàng')
                    ->searchable()
                    ->sortable()
                    ->weight('medium')
                    ->copyable()
                    ->copyMessage('Đã copy mã hàng')
                    ->copyMessageDuration(1500)
                    ->toggleable(),

                TextColumn::make('name')
                    ->label('Tên hàng hóa')
                    ->searchable()
                    ->sortable()
                    ->limit(50)
                    ->tooltip(function (TextColumn $column): ?string {
                        $state = $column->getState();
                        return strlen($state) > 50 ? $state : null;
                    }),

                TextColumn::make('category_breadcrumb')
                    ->label('Danh mục')
                    ->getStateUsing(function (MShopKeeperInventoryItem $record): string {
                        if (!$record->category_name) {
                            return '<span class="text-gray-400 italic">Chưa phân loại</span>';
                        }

                        // Tìm category để tạo breadcrumb
                        $category = \App\Models\MShopKeeperCategory::where('name', $record->category_name)
                            ->with('parent.parent.parent')
                            ->first();

                        if ($category) {
                            return $category->breadcrumb;
                        }

                        return $record->category_name;
                    })
                    ->html()
                    ->searchable(query: function ($query, $search) {
                        return $query->where('category_name', 'like', "%{$search}%");
                    })
                    ->sortable(query: function ($query, $direction) {
                        return $query->orderBy('category_name', $direction);
                    })
                    ->limit(50)
                    ->tooltip(function (TextColumn $column): ?string {
                        $state = strip_tags($column->getState());
                        return strlen($state) > 50 ? $state : null;
                    })
                    ->toggleable(),

                BadgeColumn::make('item_type_text')
                    ->label('Loại')
                    ->getStateUsing(function (MShopKeeperInventoryItem $record): string {
                        return $record->item_type_text;
                    })
                    ->colors([
                        'primary' => 'Hàng Hoá',
                        'success' => 'Combo',
                        'warning' => 'Dịch vụ',
                    ])
                    ->toggleable(isToggledHiddenByDefault: true),

                BadgeColumn::make('is_visible')
                    ->label('Hiển thị')
                    ->getStateUsing(function (MShopKeeperInventoryItem $record): string {
                        return $record->is_visible ? 'Hiện' : 'Ẩn';
                    })
                    ->colors([
                        'success' => 'Hiện',
                        'danger' => 'Ẩn',
                    ])
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                BadgeColumn::make('is_featured')
                    ->label('Nổi bật')
                    ->getStateUsing(function (MShopKeeperInventoryItem $record): string {
                        return $record->is_featured ? 'Nổi bật' : 'Thường';
                    })
                    ->colors([
                        'warning' => 'Nổi bật',
                        'secondary' => 'Thường',
                    ])
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                BadgeColumn::make('price_hidden')
                    ->label('Hiển thị giá')
                    ->getStateUsing(function (MShopKeeperInventoryItem $record): string {
                        return $record->price_hidden ? 'Ẩn' : 'Hiện';
                    })
                    ->colors([
                        'danger' => 'Ẩn',
                        'success' => 'Hiện',
                    ])
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('selling_price')
                    ->label('Giá bán')
                    ->sortable()
                    ->alignEnd()
                    ->formatStateUsing(function (float $state): string {
                        return number_format($state) . ' VND';
                    })
                    ->color('success')
                    ->toggleable(),

                TextColumn::make('cost_price')
                    ->label('Giá mua')
                    ->sortable()
                    ->alignEnd()
                    ->formatStateUsing(function (float $state): string {
                        return number_format($state) . ' VND';
                    })
                    ->color('warning')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('profit_margin')
                    ->label('Lợi nhuận (%)')
                    ->getStateUsing(function (MShopKeeperInventoryItem $record): string {
                        return number_format($record->profit_margin, 1) . '%';
                    })
                    ->color(function (MShopKeeperInventoryItem $record): string {
                        return match (true) {
                            $record->profit_margin >= 50 => 'success',
                            $record->profit_margin >= 20 => 'warning',
                            default => 'danger'
                        };
                    })
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('total_on_hand')
                    ->label('Tồn kho')
                    ->sortable()
                    ->alignEnd()
                    ->formatStateUsing(function (int $state): string {
                        return number_format($state);
                    })
                    ->toggleable(),

                BadgeColumn::make('stock_status')
                    ->label('Trạng thái kho')
                    ->getStateUsing(function (MShopKeeperInventoryItem $record): string {
                        return $record->stock_status;
                    })
                    ->colors([
                        'success' => 'Nhiều',
                        'info' => 'Vừa',
                        'warning' => 'Ít',
                        'danger' => 'Hết hàng',
                    ])
                    ->toggleable(isToggledHiddenByDefault: true),

                BadgeColumn::make('is_item')
                    ->label('Loại mặt hàng')
                    ->formatStateUsing(function (bool $state): string {
                        return $state ? 'Sản phẩm' : 'Mẫu mã cha';
                    })
                    ->colors([
                        'success' => true,
                        'gray' => false,
                    ])
                    ->toggleable(),

                BadgeColumn::make('inactive')
                    ->label('Trạng thái')
                    ->formatStateUsing(function (bool $state): string {
                        return $state ? 'Ngừng KD' : 'Hoạt động';
                    })
                    ->colors([
                        'success' => false,
                        'danger' => true,
                    ])
                    ->toggleable(),

                TextColumn::make('unit_name')
                    ->label('Đơn vị')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('barcode')
                    ->label('Mã vạch')
                    ->copyable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('last_synced_at')
                    ->label('Sync cuối')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->since()
                    ->tooltip(function (?string $state): string {
                        return $state ? 'Sync lúc: ' . $state : 'Chưa sync';
                    }),

                BadgeColumn::make('sync_status')
                    ->label('Trạng thái sync')
                    ->colors([
                        'success' => 'synced',
                        'warning' => 'pending',
                        'danger' => 'error',
                    ])
                    ->formatStateUsing(function (string $state): string {
                        return match($state) {
                            'synced' => 'Đã sync',
                            'pending' => 'Chờ sync',
                            'error' => 'Lỗi',
                            default => $state
                        };
                    })
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('item_type')
                    ->label('Loại hàng hóa')
                    ->options([
                        1 => 'Hàng Hoá',
                        2 => 'Combo',
                        4 => 'Dịch vụ',
                    ]),

                SelectFilter::make('is_item')
                    ->label('Loại mặt hàng')
                    ->options([
                        1 => 'Sản phẩm (bán được)',
                        0 => 'Mẫu mã cha',
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return match ($data['value'] ?? null) {
                            '1' => $query->where('is_item', true),
                            '0' => $query->where('is_item', false),
                            default => $query,
                        };
                    }),

                SelectFilter::make('stock_status')
                    ->label('Trạng thái kho')
                    ->options([
                        'in_stock' => 'Còn hàng',
                        'low_stock' => 'Sắp hết (≤10)',
                        'out_of_stock' => 'Hết hàng',
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return match ($data['value'] ?? null) {
                            'in_stock' => $query->where('total_on_hand', '>', 10),
                            'low_stock' => $query->where('total_on_hand', '>', 0)->where('total_on_hand', '<=', 10),
                            'out_of_stock' => $query->where('total_on_hand', '<=', 0),
                            default => $query,
                        };
                    }),

                Filter::make('active_only')
                    ->label('Chỉ hàng đang kinh doanh')
                    ->query(fn (Builder $query): Builder => $query->where('inactive', false))
                    ->toggle(),

                Filter::make('high_profit')
                    ->label('Lợi nhuận cao (≥50%)')
                    ->query(function (Builder $query): Builder {
                        return $query->whereRaw('((selling_price - cost_price) / cost_price * 100) >= 50');
                    })
                    ->toggle(),

                SelectFilter::make('is_visible')
                    ->label('Trạng thái hiển thị')
                    ->options([
                        '1' => 'Hiển thị',
                        '0' => 'Ẩn',
                    ]),

                SelectFilter::make('is_featured')
                    ->label('Sản phẩm nổi bật')
                    ->options([
                        '1' => 'Nổi bật',
                        '0' => 'Thường',
                    ]),

                SelectFilter::make('sync_status')
                    ->label('Trạng thái sync')
                    ->options([
                        'synced' => 'Đã sync',
                        'pending' => 'Chờ sync',
                        'error' => 'Lỗi',
                    ]),

                SelectFilter::make('category_name')
                    ->label('Danh mục')
                    ->options(function () {
                        return MShopKeeperInventoryItem::whereNotNull('category_name')
                            ->distinct()
                            ->pluck('category_name', 'category_name')
                            ->sort();
                    })
                    ->searchable(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('Xem'),

                Tables\Actions\Action::make('toggle_visibility')
                    ->label(fn (MShopKeeperInventoryItem $record): string => $record->is_visible ? 'Ẩn' : 'Hiện')
                    ->icon(fn (MShopKeeperInventoryItem $record): string => $record->is_visible ? 'heroicon-o-eye-slash' : 'heroicon-o-eye')
                    ->color(fn (MShopKeeperInventoryItem $record): string => $record->is_visible ? 'warning' : 'success')
                    ->action(function (MShopKeeperInventoryItem $record): void {
                        $record->update(['is_visible' => !$record->is_visible]);
                    })
                    ->requiresConfirmation()
                    ->modalHeading(fn (MShopKeeperInventoryItem $record): string =>
                        $record->is_visible ? 'Ẩn sản phẩm khỏi web?' : 'Hiển thị sản phẩm trên web?'
                    )
                    ->modalDescription(fn (MShopKeeperInventoryItem $record): string =>
                        $record->is_visible
                            ? 'Sản phẩm sẽ không hiển thị trên website cho khách hàng.'
                            : 'Sản phẩm sẽ hiển thị trên website cho khách hàng.'
                    ),

                Tables\Actions\Action::make('toggle_featured')
                    ->label(fn (MShopKeeperInventoryItem $record): string => $record->is_featured ? 'Bỏ nổi bật' : 'Đặt nổi bật')
                    ->icon(fn (MShopKeeperInventoryItem $record): string => $record->is_featured ? 'heroicon-o-star' : 'heroicon-o-star')
                    ->color(fn (MShopKeeperInventoryItem $record): string => $record->is_featured ? 'warning' : 'gray')
                    ->action(function (MShopKeeperInventoryItem $record): void {
                        $record->update(['is_featured' => !$record->is_featured]);
                    })
                    ->requiresConfirmation()
                    ->modalHeading(fn (MShopKeeperInventoryItem $record): string =>
                        $record->is_featured ? 'Bỏ sản phẩm khỏi danh sách nổi bật?' : 'Đặt sản phẩm nổi bật?'
                    )
                    ->modalDescription(fn (MShopKeeperInventoryItem $record): string =>
                        $record->is_featured
                            ? 'Sản phẩm sẽ không hiển thị trong danh sách nổi bật trên website.'
                            : 'Sản phẩm sẽ hiển thị trong danh sách nổi bật trên website.'
                    ),

                Tables\Actions\Action::make('toggle_price_hidden')
                    ->label(fn (MShopKeeperInventoryItem $record): string => $record->price_hidden ? 'Hiện giá' : 'Ẩn giá')
                    ->icon(fn (MShopKeeperInventoryItem $record): string => $record->price_hidden ? 'heroicon-o-eye' : 'heroicon-o-eye-slash')
                    ->color(fn (MShopKeeperInventoryItem $record): string => $record->price_hidden ? 'success' : 'danger')
                    ->action(function (MShopKeeperInventoryItem $record): void {
                        $record->update(['price_hidden' => !$record->price_hidden]);
                    })
                    ->requiresConfirmation()
                    ->modalHeading(fn (MShopKeeperInventoryItem $record): string =>
                        $record->price_hidden ? 'Hiển thị giá sản phẩm?' : 'Ẩn giá sản phẩm?'
                    )
                    ->modalDescription(fn (MShopKeeperInventoryItem $record): string =>
                        $record->price_hidden
                            ? 'Giá sản phẩm sẽ hiển thị trên website thay vì "Liên hệ".'
                            : 'Giá sản phẩm sẽ bị ẩn và hiển thị "Liên hệ" trên website.'
                    ),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    // Đặt nổi bật hàng loạt
                    Tables\Actions\BulkAction::make('set_featured_selected')
                        ->label('Đặt nổi bật (đã chọn)')
                        ->icon('heroicon-o-star')
                        ->color('warning')
                        ->requiresConfirmation()
                        ->action(function ($records) {
                            $ids = $records->pluck('id');
                            if ($ids->isNotEmpty()) {
                                MShopKeeperInventoryItem::whereIn('id', $ids)->update(['is_featured' => true]);
                                Cache::forget('storefront_mshopkeeper_products');
                            }
                            Notification::make()->title('Đã đặt nổi bật cho mục đã chọn')->success()->send();
                        })
                        ->deselectRecordsAfterCompletion(),

                    // Bỏ nổi bật hàng loạt
                    Tables\Actions\BulkAction::make('unset_featured_selected')
                        ->label('Bỏ nổi bật (đã chọn)')
                        ->icon('heroicon-o-star')
                        ->color('gray')
                        ->requiresConfirmation()
                        ->action(function ($records) {
                            $ids = $records->pluck('id');
                            if ($ids->isNotEmpty()) {
                                MShopKeeperInventoryItem::whereIn('id', $ids)->update(['is_featured' => false]);
                                Cache::forget('storefront_mshopkeeper_products');
                            }
                            Notification::make()->title('Đã bỏ nổi bật các mục đã chọn')->success()->send();
                        })
                        ->deselectRecordsAfterCompletion(),

                    // Hiện sản phẩm hàng loạt
                    Tables\Actions\BulkAction::make('show_selected')
                        ->label('Hiện sản phẩm (đã chọn)')
                        ->icon('heroicon-o-eye')
                        ->color('success')
                        ->requiresConfirmation(false)
                        ->action(function ($records) {
                            $ids = $records->pluck('id');
                            if ($ids->isNotEmpty()) {
                                MShopKeeperInventoryItem::whereIn('id', $ids)->update(['is_visible' => true]);
                                Cache::forget('storefront_mshopkeeper_products');
                            }
                            Notification::make()->title('Đã hiện sản phẩm đã chọn')->success()->send();
                        })
                        ->deselectRecordsAfterCompletion(),

                    // Ẩn sản phẩm hàng loạt
                    Tables\Actions\BulkAction::make('hide_selected')
                        ->label('Ẩn sản phẩm (đã chọn)')
                        ->icon('heroicon-o-eye-slash')
                        ->color('danger')
                        ->requiresConfirmation(false)
                        ->action(function ($records) {
                            $ids = $records->pluck('id');
                            if ($ids->isNotEmpty()) {
                                MShopKeeperInventoryItem::whereIn('id', $ids)->update(['is_visible' => false]);
                                Cache::forget('storefront_mshopkeeper_products');
                            }
                            Notification::make()->title('Đã ẩn sản phẩm đã chọn')->success()->send();
                        })
                        ->deselectRecordsAfterCompletion(),

                    // Hiện giá hàng loạt
                    Tables\Actions\BulkAction::make('show_prices_selected')
                        ->label('Hiện giá (đã chọn)')
                        ->icon('heroicon-o-currency-dollar')
                        ->color('success')
                        ->requiresConfirmation(false)
                        ->action(function ($records) {
                            $ids = $records->pluck('id');
                            if ($ids->isNotEmpty()) {
                                MShopKeeperInventoryItem::whereIn('id', $ids)->update(['price_hidden' => false]);
                            }
                            Notification::make()->title('Đã hiện giá cho mục đã chọn')->success()->send();
                        })
                        ->deselectRecordsAfterCompletion(),

                    // Ẩn giá hàng loạt
                    Tables\Actions\BulkAction::make('hide_prices_selected')
                        ->label('Ẩn giá (đã chọn)')
                        ->icon('heroicon-o-currency-dollar')
                        ->color('danger')
                        ->requiresConfirmation(false)
                        ->action(function ($records) {
                            $ids = $records->pluck('id');
                            if ($ids->isNotEmpty()) {
                                MShopKeeperInventoryItem::whereIn('id', $ids)->update(['price_hidden' => true]);
                            }
                            Notification::make()->title('Đã ẩn giá cho mục đã chọn')->success()->send();
                        })
                        ->deselectRecordsAfterCompletion(),
                ]),
            ])
            ->defaultSort('name', 'asc')
            ->striped()
            ->paginated([10, 25, 50, 100]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMShopKeeperInventoryItems::route('/'),
            'view' => Pages\ViewMShopKeeperInventoryItem::route('/{record}'),
        ];
    }

    public static function getSlug(): string
    {
        return 'mshopkeeper-inventory-items';
    }

    public static function getModel(): string
    {
        return MShopKeeperInventoryItem::class;
    }

    public static function getModelInstance(): MShopKeeperInventoryItem
    {
        return new MShopKeeperInventoryItem();
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return MShopKeeperInventoryItem::query(); // Không cần eager load - dùng category_name trực tiếp
    }

    public static function getNavigationBadge(): ?string
    {
        try {
            return (string) MShopKeeperInventoryItem::active()->count();
        } catch (\Exception $e) {
            return null;
        }
    }

    public static function canCreate(): bool
    {
        return false; // Không cho phép tạo mới
    }

    public static function canEdit($record): bool
    {
        return false; // Không cho phép chỉnh sửa
    }

    public static function canDelete($record): bool
    {
        return false; // Không cho phép xóa
    }

    public static function resolveRecordRouteBinding(int | string $key): ?\Illuminate\Database\Eloquent\Model
    {
        return MShopKeeperInventoryItem::find($key);
    }

    public static function getGlobalSearchEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return static::getEloquentQuery();
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'code', 'barcode'];
    }
}
