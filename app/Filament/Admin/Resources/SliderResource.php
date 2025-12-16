<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\SliderResource\Pages;
use App\Models\Slider;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;


use Filament\Tables\Table;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Notifications\Notification;
use App\Constants\NavigationGroups;
use App\Traits\HasRoleBasedAccess;

class SliderResource extends Resource
{
    use HasRoleBasedAccess;
    protected static ?string $model = Slider::class;

    protected static ?string $navigationIcon = 'heroicon-o-photo';

    protected static ?string $navigationGroup = NavigationGroups::CONTENT_MANAGEMENT;

    protected static ?string $navigationLabel = 'Ảnh cuộn';

    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }

    protected static ?string $modelLabel = 'slider banner';

    protected static ?string $pluralModelLabel = 'slider banner';

    protected static ?int $navigationSort = 32;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Thông tin slider')
                    ->schema([
                        FileUpload::make('image_link')
                            ->label('Hình ảnh Hero Banner')
                            ->helperText('Ảnh sẽ được tối ưu hóa tự động với chiều rộng tối đa 1920px, giữ nguyên tỷ lệ gốc. Sử dụng nút "Chỉnh ảnh thành 16:9" nếu muốn crop thành tỷ lệ 16:9.')
                            ->required()
                            ->image()
                            ->directory('sliders/banners')
                            ->visibility('public')
                            ->maxSize(8192) // Tăng lên 8MB để cho phép ảnh chất lượng cao
                            ->imageEditor()
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                            ->saveUploadedFileUsing(function ($file, $get) {
                                $imageService = app(\App\Services\ImageService::class);
                                $title = $get('title') ?? 'hero-banner';
                                return $imageService->saveImageWithAspectRatio(
                                    $file,
                                    'sliders/banners',
                                    1920,  // maxWidth - tối ưu cho desktop
                                    1080,  // maxHeight - giới hạn chiều cao
                                    85,    // quality - cân bằng giữa chất lượng và dung lượng
                                    "hero-banner-{$title}" // SEO-friendly name
                                );
                            })
                            ->columnSpanFull(),

                        TextInput::make('title')
                            ->label('Tiêu đề')
                            ->maxLength(255)
                            ->columnSpan(1),

                        TextInput::make('link')
                            ->label('Liên kết')
                            ->url()
                            ->maxLength(500)
                            ->columnSpan(1),

                        Textarea::make('description')
                            ->label('Mô tả')
                            ->rows(3)
                            ->maxLength(1000)
                            ->columnSpanFull(),
                    ])->columns(2),

                Section::make('Cấu hình hiển thị')
                    ->schema([
                        TextInput::make('order')
                            ->label('Thứ tự hiển thị')
                            ->integer()
                            ->default(0)
                            ->minValue(0)
                            ->columnSpan(1)
                            ->hidden(),

                        Select::make('status')
                            ->label('Trạng thái')
                            ->options([
                                'active' => 'Hiển thị',
                                'inactive' => 'Ẩn',
                            ])
                            ->default('active')
                            ->required()
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
                    ->label('Hình ảnh')
                    ->height(60)
                    ->width(100),

                TextColumn::make('title')
                    ->label('Tiêu đề')
                    ->searchable()
                    ->sortable()
                    ->limit(30),

                TextColumn::make('description')
                    ->label('Mô tả')
                    ->limit(50)
                    ->searchable(),

                TextColumn::make('link')
                    ->label('Liên kết')
                    ->limit(30)
                    ->url(fn ($record) => $record->link)
                    ->openUrlInNewTab(),

                TextColumn::make('alt_text')
                    ->label('Alt Text (SEO)')
                    ->limit(40)
                    ->tooltip(fn ($record) => $record->alt_text)
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('status')
                    ->label('Trạng thái')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'inactive' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'active' => 'Hiển thị',
                        'inactive' => 'Ẩn',
                        default => 'Không xác định',
                    })
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Ngày tạo')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Trạng thái hiển thị')
                    ->options([
                        'active' => 'Đang hiển thị',
                        'inactive' => 'Đã ẩn',
                    ])
                    ->native(false),
            ])
            ->actions([
                Tables\Actions\Action::make('crop_16_9')
                    ->label('Chỉnh ảnh thành 16:9')
                    ->icon('heroicon-o-scissors')
                    ->color('warning')
                    ->visible(fn ($record) => $record->image_link)
                    ->action(function ($record) {
                        $imageService = app(\App\Services\ImageService::class);
                        $newPath = $imageService->resizeToSixteenNine($record->image_link, "hero-banner-{$record->title}");

                        if ($newPath) {
                            $record->update(['image_link' => $newPath]);
                            Notification::make()
                                ->title('Đã chỉnh ảnh thành tỷ lệ 16:9')
                                ->success()
                                ->send();
                        } else {
                            Notification::make()
                                ->title('Lỗi khi chỉnh ảnh')
                                ->danger()
                                ->send();
                        }
                    })
                    ->requiresConfirmation()
                    ->modalHeading('Chỉnh ảnh thành tỷ lệ 16:9')
                    ->modalDescription('Ảnh sẽ được crop và resize thành tỷ lệ 16:9 (1920x1080px). Thao tác này không thể hoàn tác.')
                    ->modalSubmitActionLabel('Xác nhận'),
                Tables\Actions\Action::make('toggle_status')
                    ->label(fn ($record) => $record->status === 'active' ? 'Ẩn' : 'Hiển thị')
                    ->icon(fn ($record) => $record->status === 'active' ? 'heroicon-o-eye-slash' : 'heroicon-o-eye')
                    ->color(fn ($record) => $record->status === 'active' ? 'danger' : 'success')
                    ->action(function ($record) {
                        $record->update([
                            'status' => $record->status === 'active' ? 'inactive' : 'active'
                        ]);
                    })
                    ->requiresConfirmation(false),
                Tables\Actions\EditAction::make()
                    ->label('Sửa'),
                Tables\Actions\DeleteAction::make()
                    ->label('Xóa'),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('show_selected')
                        ->label('Hiển thị đã chọn')
                        ->icon('heroicon-o-eye')
                        ->color('success')
                        ->action(function ($records) {
                            $records->each(function ($record) {
                                $record->update(['status' => 'active']);
                            });
                        })
                        ->deselectRecordsAfterCompletion()
                        ->successNotificationTitle('Đã hiển thị các slider đã chọn'),

                    Tables\Actions\BulkAction::make('hide_selected')
                        ->label('Ẩn đã chọn')
                        ->icon('heroicon-o-eye-slash')
                        ->color('danger')
                        ->action(function ($records) {
                            $records->each(function ($record) {
                                $record->update(['status' => 'inactive']);
                            });
                        })
                        ->deselectRecordsAfterCompletion()
                        ->successNotificationTitle('Đã ẩn các slider đã chọn'),

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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSliders::route('/'),
            'create' => Pages\CreateSlider::route('/create'),
            'edit' => Pages\EditSlider::route('/{record}/edit'),
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

