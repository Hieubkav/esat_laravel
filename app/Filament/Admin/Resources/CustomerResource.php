<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\CustomerResource\Pages;
use App\Models\Customer;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Hash;
use App\Constants\NavigationGroups;
use App\Traits\HasRoleBasedAccess;

class CustomerResource extends Resource
{
    use HasRoleBasedAccess;
    protected static ?string $model = Customer::class;

    protected static ?string $modelLabel = 'khách hàng';

    protected static ?string $pluralModelLabel = 'khách hàng';

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationGroup = NavigationGroups::PRODUCT_MANAGEMENT;

    protected static ?string $navigationLabel = 'Quản lý khách hàng';

    protected static ?int $navigationSort = 24;

    /**
     * Ẩn khỏi navigation vì đã sử dụng eshop để quản lý khách hàng
     */
    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Thông tin khách hàng')
                    ->schema([
                        TextInput::make('name')
                            ->label('Tên khách hàng')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),

                        TextInput::make('phone')
                            ->label('Số điện thoại')
                            ->tel()
                            ->unique(ignoreRecord: true)
                            ->maxLength(20),

                        TextInput::make('plain_password')
                            ->label('Mật khẩu gốc (Admin có thể xem)')
                            ->maxLength(255)
                            ->helperText('Mật khẩu này sẽ được lưu không mã hóa để admin có thể xem')
                            ->columnSpanFull(),

                        TextInput::make('password')
                            ->label('Mật khẩu (Hash)')
                            ->password()
                            ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                            ->dehydrated(fn ($state) => filled($state))
                            ->required(fn (string $operation): bool => $operation === 'create')
                            ->helperText('Mật khẩu này sẽ được mã hóa tự động'),

                        Textarea::make('address')
                            ->label('Địa chỉ')
                            ->rows(3)
                            ->maxLength(500),
                    ])->columns(2),

                Section::make('Cấu hình hiển thị')
                    ->schema([
                        TextInput::make('order')
                            ->label('Thứ tự hiển thị')
                            ->integer()
                            ->default(0),

                        Toggle::make('status')
                            ->label('Hoạt động')
                            ->default(true)
                            ->onColor('success')
                            ->offColor('danger'),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->withCount('mshopkeeperCarts'))
            ->columns([
                TextColumn::make('order')
                    ->label('Thứ tự')
                    ->sortable(),

                TextColumn::make('name')
                    ->label('Tên khách hàng')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('email')
                    ->label('Email')
                    ->searchable(),

                TextColumn::make('phone')
                    ->label('Số điện thoại')
                    ->searchable(),

                TextColumn::make('address')
                    ->label('Địa chỉ')
                    ->limit(30)
                    ->searchable(),

                TextColumn::make('password')
                    ->label('Mật khẩu')
                    ->formatStateUsing(fn () => '••••••••')
                    ->tooltip('Click vào "Xem" để xem mật khẩu đầy đủ'),

                TextColumn::make('mshopkeeper_carts_count')
                    ->label('Giỏ hàng')
                    ->badge()
                    ->color('info')
                    ->formatStateUsing(fn ($state) => $state ?? 0),

                ToggleColumn::make('status')
                    ->label('Hoạt động')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Ngày tạo')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                //
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
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Xóa đã chọn'),
                ]),
            ])
            ->defaultSort('order', 'asc')
            ->reorderable('order');
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
            'index' => Pages\ListCustomers::route('/'),
            'create' => Pages\CreateCustomer::route('/create'),
            'view' => Pages\ViewCustomer::route('/{record}'),
            'edit' => Pages\EditCustomer::route('/{record}/edit'),
        ];
    }

    // Tắt navigation badge để tăng tốc độ load
    // public static function getNavigationBadge(): ?string
    // {
    //     return static::getModel()::count();
    // }

    // public static function getNavigationBadgeColor(): ?string
    // {
    //     return 'success';
    // }
}