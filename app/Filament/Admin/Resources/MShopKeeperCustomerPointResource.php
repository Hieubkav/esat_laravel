<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\MShopKeeperCustomerPointResource\Pages;
use App\Models\MShopKeeperCustomerPoint;
use App\Services\MShopKeeperService;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Notifications\Notification;
use App\Constants\NavigationGroups;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Log;

class MShopKeeperCustomerPointResource extends Resource
{
    protected static ?string $model = MShopKeeperCustomerPoint::class;

    protected static ?string $navigationIcon = 'heroicon-o-star';

    protected static ?string $navigationGroup = NavigationGroups::ECOMMERCE;

    protected static ?string $navigationLabel = 'Điểm thẻ thành viên';

    /**
     * Override để đảm bảo navigation group được xử lý an toàn
     */
    public static function getNavigationGroup(): ?string
    {
        try {
            return static::$navigationGroup ?? NavigationGroups::ECOMMERCE;
        } catch (\Throwable $e) {
            Log::error('Error getting navigation group for MShopKeeperCustomerPointResource', [
                'error' => $e->getMessage()
            ]);
            return 'Thương mại điện tử'; // Fallback value
        }
    }

    protected static ?string $modelLabel = 'điểm thẻ thành viên';

    protected static ?string $pluralModelLabel = 'điểm thẻ thành viên';

    protected static ?int $navigationSort = 14;

    /**
     * Ẩn điểm thẻ thành viên khỏi navigation theo yêu cầu
     */
    public static function shouldRegisterNavigation(): bool
    {
        return false;
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
                TextColumn::make('full_name')
                    ->label('Tên khách hàng')
                    ->searchable()
                    ->sortable()
                    ->weight('medium')
                    ->copyable()
                    ->copyMessage('Đã copy tên khách hàng')
                    ->copyMessageDuration(1500),

                TextColumn::make('tel')
                    ->label('Số điện thoại')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->copyMessage('Đã copy số điện thoại')
                    ->copyMessageDuration(1500)
                    ->formatStateUsing(function (?string $state): string {
                        if (!$state) return 'N/A';
                        return $state;
                    }),

                TextColumn::make('total_point')
                    ->label('Tổng điểm')
                    ->sortable()
                    ->alignEnd()
                    ->formatStateUsing(function (int $state): string {
                        return number_format($state) . ' điểm';
                    })
                    ->color(function (int $state): string {
                        return match (true) {
                            $state >= 5000 => 'danger',  // VIP - Red
                            $state >= 2000 => 'warning', // Vàng - Yellow
                            $state >= 1000 => 'info',    // Bạc - Blue
                            $state >= 500 => 'success',  // Đồng - Green
                            default => 'gray'            // Thường - Gray
                        };
                    }),

                BadgeColumn::make('point_level')
                    ->label('Hạng thẻ')
                    ->getStateUsing(function (MShopKeeperCustomerPoint $record): string {
                        return $record->point_level;
                    })
                    ->colors([
                        'danger' => 'VIP',
                        'warning' => 'Vàng',
                        'info' => 'Bạc',
                        'success' => 'Đồng',
                        'gray' => 'Thường',
                    ]),

                TextColumn::make('last_synced_at')
                    ->label('Sync cuối')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable()
                    ->since()
                    ->tooltip(function (?string $state): string {
                        return $state ? 'Sync lúc: ' . $state : 'Chưa sync';
                    }),

                BadgeColumn::make('sync_status')
                    ->label('Trạng thái')
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
                    ->toggleable(),

                TextColumn::make('created_at')
                    ->label('Tạo lúc')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label('Cập nhật lúc')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('point_level')
                    ->label('Hạng thẻ')
                    ->options([
                        'VIP' => 'VIP (≥5000 điểm)',
                        'Vàng' => 'Vàng (≥2000 điểm)',
                        'Bạc' => 'Bạc (≥1000 điểm)',
                        'Đồng' => 'Đồng (≥500 điểm)',
                        'Thường' => 'Thường (<500 điểm)',
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return match ($data['value'] ?? null) {
                            'VIP' => $query->where('total_point', '>=', 5000),
                            'Vàng' => $query->where('total_point', '>=', 2000)->where('total_point', '<', 5000),
                            'Bạc' => $query->where('total_point', '>=', 1000)->where('total_point', '<', 2000),
                            'Đồng' => $query->where('total_point', '>=', 500)->where('total_point', '<', 1000),
                            'Thường' => $query->where('total_point', '<', 500),
                            default => $query,
                        };
                    }),

                Filter::make('high_value_customers')
                    ->label('Khách hàng giá trị cao')
                    ->query(fn (Builder $query): Builder => $query->where('total_point', '>=', 2000))
                    ->toggle(),

                Filter::make('with_points')
                    ->label('Có điểm tích lũy')
                    ->query(fn (Builder $query): Builder => $query->where('total_point', '>', 0))
                    ->toggle(),

                SelectFilter::make('sync_status')
                    ->label('Trạng thái sync')
                    ->options([
                        'synced' => 'Đã sync',
                        'pending' => 'Chờ sync',
                        'error' => 'Lỗi',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('Xem chi tiết'),
            ])
            ->bulkActions([
                // Không có bulk actions vì dữ liệu từ API
            ])
            ->defaultSort('total_point', 'desc')
            ->striped()
            ->paginated([10, 25, 50, 100]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMShopKeeperCustomerPoints::route('/'),
            'view' => Pages\ViewMShopKeeperCustomerPoint::route('/{record}'),
        ];
    }

    public static function getSlug(): string
    {
        return 'mshopkeeper-customer-points';
    }

    public static function getModel(): string
    {
        return MShopKeeperCustomerPoint::class;
    }

    public static function getModelInstance(): MShopKeeperCustomerPoint
    {
        return new MShopKeeperCustomerPoint();
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return MShopKeeperCustomerPoint::query();
    }

    public static function getNavigationBadge(): ?string
    {
        try {
            return (string) MShopKeeperCustomerPoint::count();
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
        return MShopKeeperCustomerPoint::find($key);
    }

    public static function getGlobalSearchEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return static::getEloquentQuery();
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['full_name', 'tel'];
    }
}
