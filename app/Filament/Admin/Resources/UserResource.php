<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Hash;
use App\Constants\NavigationGroups;
use App\Traits\HasRoleBasedAccess;

class UserResource extends Resource
{
    use HasRoleBasedAccess;
    protected static ?string $model = User::class;

    protected static ?string $modelLabel = 'người dùng';

    protected static ?string $pluralModelLabel = 'người dùng';

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationGroup = NavigationGroups::WEBSITE_SETTINGS;

    protected static ?string $navigationLabel = 'Người dùng';

    protected static ?int $navigationSort = 41;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Thông tin người dùng')
                    ->schema([
                        TextInput::make('name')
                            ->label('Tên người dùng')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->unique(ignoreRecord: true)
                            ->required()
                            ->maxLength(255),

                        TextInput::make('plain_password')
                            ->label('Mật khẩu')
                            ->helperText('Nhập mật khẩu mới (sẽ được mã hóa tự động)')
                            ->dehydrateStateUsing(function ($state, $record) {
                                // Chỉ cập nhật nếu có giá trị mới
                                if (filled($state)) {
                                    // Cập nhật password hash
                                    $record->password = Hash::make($state);
                                    return $state; // Lưu plain password
                                }
                                return $record->plain_password; // Giữ nguyên nếu không thay đổi
                            })
                            ->required(fn (string $operation): bool => $operation === 'create')
                            ->default(fn ($record) => $record?->plain_password),

                        Select::make('role')
                            ->label('Vai trò')
                            ->options([
                                'admin' => 'Quản trị viên',
                                'post_manager' => 'Quản lý bài viết',
                            ])
                            ->default('admin')
                            ->required(),
                    ])->columns(2),

                Section::make('Cấu hình hiển thị')
                    ->schema([
                        TextInput::make('order')
                            ->label('Thứ tự hiển thị')
                            ->integer()
                            ->default(0)
                            ->hidden(),

                        Toggle::make('status')
                            ->label('Hoạt động')
                            ->default(true)
                            ->onColor('success')
                            ->offColor('danger'),

                        DateTimePicker::make('last_login_at')
                            ->label('Lần đăng nhập cuối')
                            ->disabled(),
                    ])->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('order')
                    ->label('Thứ tự')
                    ->sortable(),

                TextColumn::make('name')
                    ->label('Tên người dùng')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('email')
                    ->label('Email')
                    ->searchable(),

                TextColumn::make('role')
                    ->label('Vai trò')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'admin' => 'success',
                        'post_manager' => 'warning',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'admin' => 'Quản trị viên',
                        'post_manager' => 'Quản lý bài viết',
                        default => $state,
                    })
                    ->sortable(),

                ToggleColumn::make('status')
                    ->label('Hoạt động')
                    ->sortable(),

                TextColumn::make('last_login_at')
                    ->label('Lần đăng nhập cuối')
                    ->dateTime('d/m/Y H:i')
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
            ->defaultSort('order', 'asc');
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}