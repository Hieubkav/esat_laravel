<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\EmployeeResource\Pages;
use App\Filament\Admin\Resources\EmployeeResource\RelationManagers;
use App\Models\Employee;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\Action;
use Filament\Notifications\Notification;
use App\Constants\NavigationGroups;
use App\Traits\HasRoleBasedAccess;

class EmployeeResource extends Resource
{
    use HasRoleBasedAccess;
    protected static ?string $model = Employee::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationGroup = NavigationGroups::WEBSITE_SETTINGS;

    protected static ?string $navigationLabel = 'Nhân sự';

    protected static ?string $modelLabel = 'Nhân viên';

    protected static ?string $pluralModelLabel = 'Nhân viên';

    protected static ?int $navigationSort = 44;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Thông tin cá nhân')
                    ->schema([
                        TextInput::make('name')
                            ->label('Họ và tên')
                            ->required()
                            ->maxLength(255)
                            ->columnSpan(1)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (string $operation, $state, Set $set) {
                                if ($operation !== 'create') {
                                    return;
                                }
                                $set('slug', \Illuminate\Support\Str::slug($state));
                            }),

                        TextInput::make('slug')
                            ->label('Đường dẫn (Slug)')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->columnSpan(1)
                            ->helperText('Đường dẫn SEO-friendly cho trang thông tin nhân viên'),

                        TextInput::make('position')
                            ->label('Chức vụ')
                            ->required()
                            ->maxLength(255)
                            ->columnSpan(1),

                        RichEditor::make('description')
                            ->label('Mô tả')
                            ->fileAttachmentsDisk('public')
                            ->fileAttachmentsDirectory('employees')
                            ->columnSpanFull()
                            ->helperText('Mô tả chi tiết về nhân viên, kinh nghiệm, kỹ năng...')
                            ->toolbarButtons([
                                'bold',
                                'italic',
                                'underline',
                                'bulletList',
                                'orderedList',
                                'link',
                                'blockquote',
                                'h2',
                                'h3',
                                'undo',
                                'redo',
                            ]),

                        FileUpload::make('image_link')
                            ->label('Ảnh đại diện')
                            ->image()
                            ->directory('employees/avatars')
                            ->visibility('public')
                            ->imageResizeMode('cover')
                            ->imageResizeTargetWidth(400)
                            ->imageResizeTargetHeight(400)
                            ->maxSize(2048)
                            ->imageEditor()
                            ->saveUploadedFileUsing(function ($file, $get) {
                                $imageService = app(\App\Services\ImageService::class);
                                $employeeName = $get('name') ?? 'nhan-vien';
                                return $imageService->saveImage(
                                    $file,
                                    'employees/avatars',
                                    400,   // width
                                    400,   // height
                                    95,    // quality
                                    "avatar-{$employeeName}" // SEO-friendly name
                                );
                            })
                            ->columnSpanFull(),
                    ])->columns(2),

                Section::make('Thông tin liên hệ')
                    ->schema([
                        TextInput::make('phone')
                            ->label('Số điện thoại')
                            ->tel()
                            ->maxLength(20)
                            ->columnSpan(1),

                        TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->maxLength(255)
                            ->columnSpan(1),

                        FileUpload::make('qr_code')
                            ->label('Mã QR')
                            ->image()
                            ->directory('employees/qr-codes')
                            ->visibility('public')
                            ->maxSize(1024)
                            ->saveUploadedFileUsing(function ($file, $get) {
                                $imageService = app(\App\Services\ImageService::class);
                                $employeeName = $get('name') ?? 'nhan-vien';
                                return $imageService->saveImage(
                                    $file,
                                    'employees/qr-codes',
                                    300,   // width
                                    300,   // height
                                    100,   // quality (cao cho QR code)
                                    "qr-{$employeeName}" // SEO-friendly name
                                );
                            })
                            ->helperText('Tải lên QR code hoặc để trống để tự động tạo khi lưu')
                            ->columnSpanFull(),
                    ])->columns(2),

                Section::make('Cấu hình hiển thị')
                    ->schema([
                        TextInput::make('order')
                            ->label('Thứ tự hiển thị')
                            ->integer()
                            ->default(0)
                            ->minValue(0)
                            ->columnSpan(1),

                        Toggle::make('status')
                            ->label('Hiển thị')
                            ->default(true)
                            ->onColor('success')
                            ->offColor('danger')
                            ->columnSpan(1),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('order')
                    ->label('Thứ tự')
                    ->sortable()
                    ->width(80),

                ImageColumn::make('image_link')
                    ->label('Ảnh')
                    ->height(60)
                    ->width(60)
                    ->circular(),

                TextColumn::make('name')
                    ->label('Họ và tên')
                    ->searchable()
                    ->sortable()
                    ->weight('medium'),

                TextColumn::make('slug')
                    ->label('Slug')
                    ->searchable()
                    ->copyable()
                    ->copyMessage('Đã sao chép slug!')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('position')
                    ->label('Chức vụ')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('description')
                    ->label('Mô tả')
                    ->html()
                    ->limit(50)
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('phone')
                    ->label('Điện thoại')
                    ->searchable(),

                TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->limit(30),

                TextColumn::make('qr_code')
                    ->label('QR Code')
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state ? 'Có' : 'Chưa có')
                    ->color(fn ($state) => $state ? 'success' : 'warning')
                    ->toggleable(),

                ToggleColumn::make('status')
                    ->label('Hiển thị')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Ngày tạo')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('status')
                    ->label('Trạng thái hiển thị')
                    ->boolean()
                    ->trueLabel('Đang hiển thị')
                    ->falseLabel('Đã ẩn')
                    ->native(false),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Action::make('viewProfile')
                        ->label('Xem trang')
                        ->icon('heroicon-o-eye')
                        ->color('primary')
                        ->url(fn (Employee $record) => route('employee.profile', $record->slug))
                        ->openUrlInNewTab()
                        ->visible(fn (Employee $record) => !empty($record->slug)),

                    Action::make('generateQrCode')
                        ->label('Tạo QR Code')
                        ->icon('heroicon-o-qr-code')
                        ->color('info')
                        ->action(function (Employee $record) {
                            $qrCodeService = app(\App\Services\QrCodeService::class);
                            $profileUrl = route('employee.profile', $record->slug);
                            $qrCodePath = $qrCodeService->generateEmployeeQrCode($profileUrl, $record->name);

                            if ($qrCodePath) {
                                // Xóa QR code cũ nếu có
                                if ($record->qr_code) {
                                    $qrCodeService->deleteQrCode($record->qr_code);
                                }

                                // Cập nhật đường dẫn QR code mới
                                $record->update(['qr_code' => $qrCodePath]);

                                Notification::make()
                                    ->title('Tạo QR Code thành công!')
                                    ->success()
                                    ->send();
                            } else {
                                Notification::make()
                                    ->title('Lỗi tạo QR Code!')
                                    ->danger()
                                    ->send();
                            }
                        })
                        ->requiresConfirmation()
                        ->modalHeading('Tạo QR Code mới')
                        ->modalDescription('Bạn có chắc chắn muốn tạo QR Code mới? QR Code cũ sẽ bị thay thế.')
                        ->visible(fn (Employee $record) => !empty($record->slug)),

                    Action::make('downloadQrCode')
                        ->label('Tải QR Code')
                        ->icon('heroicon-o-arrow-down-tray')
                        ->color('success')
                        ->url(fn (Employee $record) => route('employee.qr-download', $record->slug))
                        ->openUrlInNewTab()
                        ->visible(fn (Employee $record) => !empty($record->slug)),
                ])
                ->label('Thao tác')
                ->icon('heroicon-m-ellipsis-vertical')
                ->size('sm')
                ->color('gray')
                ->button(),

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
            ->defaultSort('order', 'asc')
            ->reorderable('order');
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\EmployeeImagesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEmployees::route('/'),
            'create' => Pages\CreateEmployee::route('/create'),
            'edit' => Pages\EditEmployee::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'success';
    }
}
