<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\OrderResource\Pages;
use App\Filament\Admin\Resources\OrderResource\RelationManagers;
use App\Models\Order;
use App\Constants\NavigationGroups;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationLabel = 'Đơn hàng Website';

    protected static ?string $modelLabel = 'Đơn hàng Website';

    protected static ?string $pluralModelLabel = 'Đơn hàng Website';

    protected static ?string $navigationGroup = NavigationGroups::ECOMMERCE;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('customer_id')
                    ->numeric()
                    ->default(null),
                Forms\Components\TextInput::make('order_number')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('total')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('status')
                    ->required(),
                Forms\Components\TextInput::make('payment_method'),
                Forms\Components\TextInput::make('payment_status')
                    ->required(),
                Forms\Components\Textarea::make('shipping_address')
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('shipping_name')
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\TextInput::make('shipping_phone')
                    ->tel()
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\TextInput::make('shipping_email')
                    ->email()
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\Textarea::make('note')
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('mshopkeeper_order_id')
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\TextInput::make('mshopkeeper_order_no')
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\TextInput::make('mshopkeeper_customer_id')
                    ->numeric()
                    ->default(null),
                Forms\Components\TextInput::make('mshopkeeper_invoice_id')
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\TextInput::make('mshopkeeper_invoice_number')
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\TextInput::make('mshopkeeper_sync_status')
                    ->required(),
                Forms\Components\Textarea::make('mshopkeeper_sync_error')
                    ->columnSpanFull(),
                Forms\Components\DateTimePicker::make('mshopkeeper_synced_at'),
                Forms\Components\TextInput::make('sale_channel')
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\TextInput::make('delivery_code')
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\TextInput::make('shipping_partner')
                    ->maxLength(255)
                    ->default(null),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('order_number')
                    ->label('Số đơn hàng (Local)')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('mshopkeeper_order_no')
                    ->label('Mã MShopKeeper')
                    ->searchable()
                    ->badge()
                    ->color('success'),

                Tables\Columns\TextColumn::make('total')
                    ->label('Tổng tiền')
                    ->money('VND')
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->label('Trạng thái')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'completed' => 'success',
                        'cancelled' => 'danger',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('shipping_name')
                    ->label('Khách hàng')
                    ->searchable()
                    ->limit(30),

                Tables\Columns\TextColumn::make('shipping_phone')
                    ->label('Điện thoại')
                    ->searchable(),

                Tables\Columns\TextColumn::make('payment_method')
                    ->label('Thanh toán')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'cod' => 'warning',
                        'bank_transfer' => 'success',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Ngày tạo')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Trạng thái')
                    ->options([
                        'pending' => 'Chờ xử lý',
                        'completed' => 'Hoàn thành',
                        'cancelled' => 'Đã hủy',
                    ]),

                Tables\Filters\SelectFilter::make('payment_method')
                    ->label('Phương thức thanh toán')
                    ->options([
                        'cod' => 'COD',
                        'bank_transfer' => 'Chuyển khoản',
                    ]),

                Tables\Filters\Filter::make('has_mshopkeeper_order')
                    ->label('Có mã MShopKeeper')
                    ->query(fn ($query) => $query->whereNotNull('mshopkeeper_order_no')),

                Tables\Filters\Filter::make('created_today')
                    ->label('Hôm nay')
                    ->query(fn ($query) => $query->whereDate('created_at', today())),
            ])
            ->defaultSort('created_at', 'desc')
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
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
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }
}
