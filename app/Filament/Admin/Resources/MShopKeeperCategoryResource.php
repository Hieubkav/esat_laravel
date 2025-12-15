<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\MShopKeeperCategoryResource\Pages;
use App\Models\MShopKeeperCategory;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use App\Constants\NavigationGroups;
use App\Traits\HasRoleBasedAccess;

class MShopKeeperCategoryResource extends Resource
{
    use HasRoleBasedAccess;

    protected static ?string $model = MShopKeeperCategory::class;

    protected static ?string $navigationIcon = 'heroicon-o-squares-2x2';

    protected static ?string $navigationGroup = NavigationGroups::ECOMMERCE;

    protected static ?string $navigationLabel = 'Danh mục MShopKeeper';

    protected static ?string $modelLabel = 'danh mục MShopKeeper';

    protected static ?string $pluralModelLabel = 'danh mục MShopKeeper';

    protected static ?int $navigationSort = 12;

    /**
     * Override để đảm bảo navigation group được xử lý an toàn
     */
    public static function getNavigationGroup(): ?string
    {
        try {
            return static::$navigationGroup ?? NavigationGroups::ECOMMERCE;
        } catch (\Throwable $e) {
            Log::error('Error getting navigation group for MShopKeeperCategoryResource', [
                'error' => $e->getMessage()
            ]);
            return 'Thương mại điện tử'; // Fallback value
        }
    }

    public static function form(Form $form): Form
    {
        // Không cho phép tạo/sửa vì dữ liệu từ API
        return $form->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')
                    ->label('Mã danh mục')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->copyMessage('Đã sao chép mã danh mục!')
                    ->weight('medium'),

                TextColumn::make('indented_name')
                    ->label('Tên danh mục')
                    ->searchable(['name'])
                    ->sortable(['name'])
                    ->html()
                    ->weight('medium')
                    ->description(fn (MShopKeeperCategory $record): string => $record->description ?: ''),

                TextColumn::make('grade')
                    ->label('Cấp độ')
                    ->sortable()
                    ->alignCenter()
                    ->badge()
                    ->color(fn (string $state): string => match ((int) $state) {
                        0 => 'primary',
                        1 => 'success',
                        2 => 'warning',
                        default => 'gray',
                    }),

                TextColumn::make('status')
                    ->label('Trạng thái')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Hoạt động' => 'success',
                        'Không hoạt động' => 'danger',
                        default => 'gray',
                    }),

                TextColumn::make('type')
                    ->label('Loại')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Nhánh' => 'primary',
                        'Lá' => 'success',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'Nhánh' => '📂 Nhánh',
                        'Lá' => '🍃 Lá',
                        default => $state,
                    })
                    ->tooltip(fn (MShopKeeperCategory $record): string =>
                        $record->is_leaf
                            ? '🍃 Lá: Danh mục cuối cùng, không có danh mục con'
                            : '📂 Nhánh: Danh mục cha, có ' . $record->children()->count() . ' danh mục con'
                    ),

                TextColumn::make('time_since_last_sync')
                    ->label('Sync cuối')
                    ->sortable(['last_synced_at'])
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('sync_status')
                    ->label('Trạng thái sync')
                    ->badge()
                    ->color(fn (MShopKeeperCategory $record): string => $record->sync_status_color)
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('sort_order')
                    ->label('Thứ tự')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->alignCenter(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('inactive')
                    ->label('Trạng thái')
                    ->options([
                        '0' => 'Hoạt động',
                        '1' => 'Không hoạt động',
                    ])
                    ->placeholder('Tất cả trạng thái'),

                Tables\Filters\SelectFilter::make('is_leaf')
                    ->label('Loại danh mục')
                    ->options([
                        '0' => '📂 Nhánh (có danh mục con)',
                        '1' => '🍃 Lá (không có danh mục con)',
                    ])
                    ->placeholder('Tất cả loại'),

                Tables\Filters\SelectFilter::make('grade')
                    ->label('Cấp độ')
                    ->options([
                        '0' => 'Cấp 0 (Root)',
                        '1' => 'Cấp 1',
                        '2' => 'Cấp 2',
                        '3' => 'Cấp 3',
                        '4' => 'Cấp 4+',
                    ])
                    ->placeholder('Tất cả cấp độ'),

                Tables\Filters\SelectFilter::make('sync_status')
                    ->label('Trạng thái sync')
                    ->options([
                        'synced' => 'Đã sync',
                        'error' => 'Lỗi sync',
                        'pending' => 'Chờ sync',
                    ])
                    ->placeholder('Tất cả trạng thái sync'),
            ])

            ->actions([
                Tables\Actions\Action::make('view')
                    ->label('Xem')
                    ->icon('heroicon-o-eye')
                    ->color('info')
                    ->url(fn (MShopKeeperCategory $record): string => static::getUrl('view', ['record' => $record])),
            ])
            ->bulkActions([
                Tables\Actions\BulkAction::make('force_sync')
                    ->label('Force sync đã chọn')
                    ->icon('heroicon-o-arrow-path')
                    ->color('warning')
                    ->action(function (\Illuminate\Database\Eloquent\Collection $records) {
                        foreach ($records as $record) {
                            $record->update(['sync_status' => 'pending']);
                        }

                        Artisan::call('mshopkeeper:sync-categories', ['--force' => true]);

                        Notification::make()
                            ->title('Đã force sync')
                            ->body('Các danh mục đã chọn đã được đánh dấu để sync lại.')
                            ->success()
                            ->send();
                    }),
            ])
            ->defaultSort('sort_order', 'asc')
            ->striped()
            ->paginated([10, 25, 50, 100]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMShopKeeperCategories::route('/'),
            'view' => Pages\ViewMShopKeeperCategory::route('/{record}'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        try {
            return (string) MShopKeeperCategory::count();
        } catch (\Exception) {
            return '—';
        }
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'primary';
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

    public static function canDeleteAny(): bool
    {
        return false; // Không cho phép xóa hàng loạt
    }
}
