<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\CartResource\Pages;
use App\Filament\Admin\Resources\CartResource\RelationManagers;
use App\Models\Cart;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;

use Filament\Tables\Table;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use App\Constants\NavigationGroups;
use App\Traits\HasRoleBasedAccess;

class CartResource extends Resource
{
    use HasRoleBasedAccess;
    protected static ?string $model = Cart::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';

    protected static ?string $navigationGroup = NavigationGroups::PRODUCT_MANAGEMENT;

    protected static ?string $navigationLabel = 'Giỏ hàng';

    protected static ?string $modelLabel = 'Giỏ hàng';

    protected static ?string $pluralModelLabel = 'Giỏ hàng';

    protected static ?int $navigationSort = 23;

    /**
     * Tạm ẩn khỏi navigation - sẽ kết nối với eshop API sau
     * TODO: Kích hoạt lại khi cần thiết
     */
    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Thông tin giỏ hàng')
                    ->schema([
                        Select::make('user_id')
                            ->label('Khách hàng')
                            ->relationship('customer', 'name')
                            ->searchable()
                            ->preload()
                            ->columnSpan(1),

                        TextInput::make('session_id')
                            ->label('Session ID')
                            ->maxLength(255)
                            ->columnSpan(1),

                        TextInput::make('total')
                            ->label('Tổng tiền')
                            ->numeric()
                            ->prefix('₫')
                            ->disabled()
                            ->columnSpan(1),

                        Select::make('status')
                            ->label('Trạng thái')
                            ->options([
                                'active' => 'Đang hoạt động',
                                'abandoned' => 'Đã bỏ',
                                'converted' => 'Đã chuyển đổi',
                            ])
                            ->required()
                            ->default('active')
                            ->columnSpan(1),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),

                TextColumn::make('customer.name')
                    ->label('Khách hàng')
                    ->searchable()
                    ->sortable()
                    ->placeholder('Khách vãng lai'),

                TextColumn::make('session_id')
                    ->label('Session')
                    ->limit(20)
                    ->searchable(),

                TextColumn::make('items_count')
                    ->label('Số sản phẩm')
                    ->counts('items')
                    ->sortable(),

                TextColumn::make('total')
                    ->label('Tổng tiền')
                    ->money('VND')
                    ->sortable(),

                TextColumn::make('status')
                    ->label('Trạng thái')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'abandoned' => 'warning',
                        'converted' => 'primary',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'active' => 'Đang hoạt động',
                        'abandoned' => 'Đã bỏ',
                        'converted' => 'Đã chuyển đổi',
                        default => $state,
                    }),

                TextColumn::make('created_at')
                    ->label('Ngày tạo')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),

                TextColumn::make('updated_at')
                    ->label('Cập nhật cuối')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Trạng thái')
                    ->options([
                        'active' => 'Đang hoạt động',
                        'abandoned' => 'Đã bỏ',
                        'converted' => 'Đã chuyển đổi',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('Xem'),
                Tables\Actions\EditAction::make()
                    ->label('Sửa'),
                Tables\Actions\DeleteAction::make()
                    ->label('Xóa'),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->label('Xóa đã chọn'),
                ]),
            ])
            ->defaultSort('updated_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\CartItemsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCarts::route('/'),
            'create' => Pages\CreateCart::route('/create'),
            'view' => Pages\ViewCart::route('/{record}'),
            'edit' => Pages\EditCart::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('status', 'active')->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }
}
