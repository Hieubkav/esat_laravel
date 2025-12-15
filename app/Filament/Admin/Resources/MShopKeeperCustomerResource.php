<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\MShopKeeperCustomerResource\Pages;
use App\Models\MShopKeeperCustomer;
use App\Services\MShopKeeperService;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;

use Filament\Tables\Filters\SelectFilter;
use Filament\Notifications\Notification;
use App\Constants\NavigationGroups;
use App\Traits\HasRoleBasedAccess;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Log;

class MShopKeeperCustomerResource extends Resource
{
    // Tạm thời comment trait để test
    // use HasRoleBasedAccess;

    protected static ?string $model = MShopKeeperCustomer::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationGroup = NavigationGroups::ECOMMERCE;

    protected static ?string $navigationLabel = 'Khách hàng MShopKeeper';

    /**
     * Override để đảm bảo navigation group được xử lý an toàn
     */
    public static function getNavigationGroup(): ?string
    {
        try {
            return static::$navigationGroup ?? NavigationGroups::ECOMMERCE;
        } catch (\Throwable $e) {
            Log::error('Error getting navigation group for MShopKeeperCustomerResource', [
                'error' => $e->getMessage()
            ]);
            return 'Thương mại điện tử'; // Fallback value
        }
    }

    protected static ?string $modelLabel = 'khách hàng MShopKeeper';

    protected static ?string $pluralModelLabel = 'khách hàng MShopKeeper';

    protected static ?int $navigationSort = 13;

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
                    ->label('Mã khách hàng')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->copyMessage('Đã sao chép mã khách hàng!')
                    ->weight('medium')
                    ->toggleable(),

                TextColumn::make('name')
                    ->label('Tên khách hàng')
                    ->searchable()
                    ->sortable()
                    ->weight('medium')
                    ->description(fn (MShopKeeperCustomer $record): string => $record->email ?? ''),

                TextColumn::make('tel')
                    ->label('Số điện thoại')
                    ->searchable()
                    ->copyable()
                    ->copyMessage('Đã sao chép số điện thoại!')
                    ->weight('medium')
                    ->toggleable(),

                TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->copyable()
                    ->copyMessage('Đã sao chép email!')
                    ->limit(30)
                    ->toggleable(),

                TextColumn::make('addr')
                    ->label('Địa chỉ')
                    ->limit(40)
                    ->tooltip(fn (MShopKeeperCustomer $record): string => $record->addr ?? '')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('gender_text')
                    ->label('Giới tính')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Nam' => 'primary',
                        'Nữ' => 'success',
                        default => 'gray',
                    }),

                TextColumn::make('member_level_name')
                    ->label('Hạng thẻ')
                    ->badge()
                    ->color(fn (?string $state): string => match ($state) {
                        'Vàng' => 'warning',
                        'Bạc' => 'gray',
                        'Kim cương' => 'primary',
                        'Bạch kim' => 'success',
                        default => 'secondary',
                    })
                    ->toggleable(),

                // Thông tin địa chỉ chi tiết
                TextColumn::make('province_addr')
                    ->label('Tỉnh/TP')
                    ->limit(20)
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('district_addr')
                    ->label('Quận/Huyện')
                    ->limit(20)
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('commune_addr')
                    ->label('Phường/Xã')
                    ->limit(20)
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                // Thông tin liên hệ bổ sung
                TextColumn::make('normalized_tel')
                    ->label('SĐT chuẩn hóa')
                    ->searchable()
                    ->copyable()
                    ->copyMessage('Đã sao chép!')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('standard_tel')
                    ->label('SĐT tiêu chuẩn')
                    ->searchable()
                    ->copyable()
                    ->copyMessage('Đã sao chép!')
                    ->toggleable(isToggledHiddenByDefault: true),

                // Thông tin thành viên
                TextColumn::make('membership_code')
                    ->label('Mã thẻ TV')
                    ->searchable()
                    ->copyable()
                    ->copyMessage('Đã sao chép mã thẻ!')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('member_level_id')
                    ->label('ID hạng thẻ')
                    ->searchable()
                    ->copyable()
                    ->copyMessage('Đã sao chép!')
                    ->toggleable(isToggledHiddenByDefault: true),

                // Thông tin bổ sung
                TextColumn::make('identify_number')
                    ->label('CMND/CCCD')
                    ->searchable()
                    ->copyable()
                    ->copyMessage('Đã sao chép!')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('description')
                    ->label('Mô tả')
                    ->limit(30)
                    ->tooltip(fn (?string $state): ?string => $state)
                    ->toggleable(isToggledHiddenByDefault: true),

                // Thông tin giới tính
                TextColumn::make('gender_text')
                    ->label('Giới tính')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Nam' => 'primary',
                        'Nữ' => 'pink',
                        default => 'gray',
                    })
                    ->toggleable(isToggledHiddenByDefault: true),

                // Thông tin sync
                TextColumn::make('sync_status')
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
                    })
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('last_synced_at')
                    ->label('Sync lần cuối')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                // Thông tin hệ thống
                TextColumn::make('mshopkeeper_id')
                    ->label('MShopKeeper ID')
                    ->searchable()
                    ->copyable()
                    ->copyMessage('Đã sao chép ID!')
                    ->limit(20)
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->label('Ngày tạo')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label('Ngày cập nhật')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('sync_status')
                    ->label('Trạng thái sync')
                    ->options([
                        'synced' => 'Đã sync',
                        'error' => 'Lỗi',
                        'pending' => 'Chờ sync',
                    ])
                    ->default('synced'),

                SelectFilter::make('member_level_name')
                    ->label('Hạng thẻ thành viên')
                    ->options(function () {
                        try {
                            return MShopKeeperCustomer::whereNotNull('member_level_name')
                                ->where('member_level_name', '!=', '')
                                ->distinct()
                                ->pluck('member_level_name', 'member_level_name')
                                ->toArray();
                        } catch (\Exception $e) {
                            Log::error('Error loading member levels for filter', [
                                'error' => $e->getMessage()
                            ]);

                            return [];
                        }
                    }),

                SelectFilter::make('gender')
                    ->label('Giới tính')
                    ->options([
                        0 => 'Nam',
                        1 => 'Nữ',
                    ]),

                SelectFilter::make('province_addr')
                    ->label('Tỉnh/Thành phố')
                    ->options(function () {
                        try {
                            return MShopKeeperCustomer::whereNotNull('province_addr')
                                ->where('province_addr', '!=', '')
                                ->distinct()
                                ->orderBy('province_addr')
                                ->pluck('province_addr', 'province_addr')
                                ->toArray();
                        } catch (\Exception $e) {
                            Log::error('Error loading provinces for filter', [
                                'error' => $e->getMessage()
                            ]);

                            return [];
                        }
                    }),

                SelectFilter::make('has_email')
                    ->label('Có Email')
                    ->options([
                        'yes' => 'Có email',
                        'no' => 'Không có email',
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return match ($data['value'] ?? null) {
                            'yes' => $query->whereNotNull('email')->where('email', '!=', ''),
                            'no' => $query->where(function ($q) {
                                $q->whereNull('email')->orWhere('email', '');
                            }),
                            default => $query,
                        };
                    }),

                SelectFilter::make('has_membership')
                    ->label('Có thẻ thành viên')
                    ->options([
                        'yes' => 'Có thẻ thành viên',
                        'no' => 'Không có thẻ',
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return match ($data['value'] ?? null) {
                            'yes' => $query->whereNotNull('membership_code')->where('membership_code', '!=', ''),
                            'no' => $query->where(function ($q) {
                                $q->whereNull('membership_code')->orWhere('membership_code', '');
                            }),
                            default => $query,
                        };
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('Xem chi tiết'),
            ])
            ->bulkActions([
                // Không có bulk actions vì dữ liệu từ API
            ])
            ->defaultSort('Name', 'asc')
            ->striped()
            ->paginated([10, 25, 50, 100]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMShopKeeperCustomers::route('/'),
            'view' => Pages\ViewMShopKeeperCustomer::route('/{record}'),
        ];
    }

    public static function getSlug(): string
    {
        return 'mshopkeeper-customers';
    }

    public static function getModel(): string
    {
        return MShopKeeperCustomer::class;
    }

    public static function getModelInstance(): MShopKeeperCustomer
    {
        return new MShopKeeperCustomer();
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return MShopKeeperCustomer::query();
    }



    public static function getNavigationBadge(): ?string
    {
        try {
            return (string) MShopKeeperCustomer::count();
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
        return MShopKeeperCustomer::find($key);
    }

    public static function getGlobalSearchEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return static::getEloquentQuery();
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['Name', 'Tel', 'Email', 'Code'];
    }
}
