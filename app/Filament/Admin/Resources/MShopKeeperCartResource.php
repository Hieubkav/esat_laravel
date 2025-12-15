<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\MShopKeeperCartResource\Pages;
use App\Models\MShopKeeperCart;
use App\Traits\HasRoleBasedAccess;
use App\Constants\NavigationGroups;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Actions\Action;
use Filament\Notifications\Notification;
use App\Services\OrderService;

class MShopKeeperCartResource extends Resource
{
    use HasRoleBasedAccess;

    protected static ?string $model = MShopKeeperCart::class;

    protected static ?string $modelLabel = 'giỏ hàng MShopKeeper';

    protected static ?string $pluralModelLabel = 'giỏ hàng MShopKeeper';

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';

    protected static ?string $navigationGroup = NavigationGroups::ECOMMERCE;

    protected static ?string $navigationLabel = 'Giỏ hàng MShopKeeper';

    protected static ?int $navigationSort = 25;

    public static function form(Form $form): Form
    {
        // Không cho phép tạo/chỉnh sửa giỏ hàng từ admin
        return $form
            ->schema([
                Forms\Components\Placeholder::make('readonly_notice')
                    ->label('Thông báo')
                    ->content('Giỏ hàng chỉ có thể xem, không thể chỉnh sửa từ admin panel.'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->with(['customer', 'items.product']))
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),

                Tables\Columns\TextColumn::make('customer.name')
                    ->label('Khách hàng')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('customer.email')
                    ->label('Email')
                    ->searchable(),

                Tables\Columns\TextColumn::make('customer.phone')
                    ->label('Điện thoại')
                    ->searchable(),

                Tables\Columns\TextColumn::make('total_quantity')
                    ->label('Số sản phẩm')
                    ->badge()
                    ->color('info'),

                Tables\Columns\TextColumn::make('total_price')
                    ->label('Tổng giá trị')
                    ->formatStateUsing(fn ($state) => number_format($state) . 'đ')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Ngày tạo')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Cập nhật cuối')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('customer')
                    ->relationship('customer', 'name')
                    ->label('Khách hàng'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('Xem chi tiết'),

                Action::make('create_order')
                    ->label('Tạo đơn hàng')
                    ->icon('heroicon-o-shopping-bag')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Tạo đơn hàng từ giỏ hàng')
                    ->modalDescription(fn (MShopKeeperCart $record) =>
                        "Tạo đơn hàng cho khách hàng: {$record->customer->name}?\n" .
                        "Tổng giá trị: " . number_format($record->total_price) . "đ"
                    )
                    ->action(function (MShopKeeperCart $record) {
                        $orderService = new OrderService();

                        // Tạo đơn hàng từ giỏ hàng
                        $result = $orderService->createOrderFromCart($record->customer_id, [
                            'shipping_name' => $record->customer->name,
                            'shipping_phone' => $record->customer->phone,
                            'shipping_email' => $record->customer->email,
                            'shipping_address' => $record->customer->address,
                            'payment_method' => 'cod',
                            'note' => 'Đơn hàng được tạo bởi admin từ giỏ hàng'
                        ]);

                        if ($result['success']) {
                            // Xóa giỏ hàng sau khi tạo đơn thành công
                            $record->items()->delete();
                            $record->delete();

                            Notification::make()
                                ->title('Tạo đơn hàng thành công')
                                ->body("Đơn hàng {$result['order']->order_number} đã được tạo và giỏ hàng đã được xóa.")
                                ->success()
                                ->actions([
                                    \Filament\Notifications\Actions\Action::make('view_order')
                                        ->label('Xem đơn hàng')
                                        ->url(route('filament.admin.resources.orders.view', $result['order']->id))
                                        ->button()
                                ])
                                ->send();
                        } else {
                            Notification::make()
                                ->title('Lỗi tạo đơn hàng')
                                ->body($result['message'])
                                ->danger()
                                ->send();
                        }
                    }),
            ])
            ->bulkActions([
                // Không cho phép xóa hàng loạt
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMShopKeeperCarts::route('/'),
            'view' => Pages\ViewMShopKeeperCart::route('/{record}'),
        ];
    }

    // Không cho phép tạo mới
    public static function canCreate(): bool
    {
        return false;
    }

    // Không cho phép chỉnh sửa
    public static function canEdit($record): bool
    {
        return false;
    }

    // Không cho phép xóa
    public static function canDelete($record): bool
    {
        return false;
    }
}
