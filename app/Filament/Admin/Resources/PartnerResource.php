<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\PartnerResource\Pages;
use App\Models\Partner;
use App\Services\ImageService;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use App\Constants\NavigationGroups;
use App\Traits\HasRoleBasedAccess;

class PartnerResource extends Resource
{
    use HasRoleBasedAccess;
    protected static ?string $model = Partner::class;

    protected static ?string $modelLabel = 'đối tác';

    protected static ?string $pluralModelLabel = 'đối tác';

    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';

    protected static ?string $navigationGroup = NavigationGroups::CONTENT_MANAGEMENT;

    protected static ?string $navigationLabel = 'Đối tác';

    protected static ?int $navigationSort = 33;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Thông tin đối tác')
                    ->schema([
                        TextInput::make('name')
                            ->label('Tên đối tác')
                            ->required()
                            ->maxLength(255),

                        FileUpload::make('logo_link')
                            ->label('Logo')
                            ->helperText('Kích thước tối ưu: 300x200px (3:2). Logo đối tác nên rõ nét, nền trong suốt.')
                            ->image()
                            ->directory('partners/logos')
                            ->visibility('public')
                            ->imageResizeMode('contain')
                            ->imageResizeTargetWidth(300)
                            ->imageResizeTargetHeight(200)
                            ->saveUploadedFileUsing(function ($file, $get) {
                                $imageService = app(ImageService::class);
                                $partnerName = $get('name') ?? 'doi-tac';
                                return $imageService->saveImage(
                                    $file,
                                    'partners/logos',
                                    300,  // width
                                    200,  // height
                                    100,   // quality - cao hơn cho logo để giữ độ sắc nét
                                    "logo-{$partnerName}" // SEO-friendly name
                                );
                            }),

                        TextInput::make('website_link')
                            ->label('Website')
                            ->url()
                            ->maxLength(255),

                        Textarea::make('description')
                            ->label('Mô tả')
                            ->rows(3)
                            ->maxLength(1000),
                    ]),

                Section::make('Cấu hình hiển thị')
                    ->schema([
                        TextInput::make('order')
                            ->label('Thứ tự hiển thị')
                            ->integer()
                            ->default(0),

                        Toggle::make('status')
                            ->label('Hiển thị')
                            ->default(true)
                            ->onColor('success')
                            ->offColor('danger'),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('order')
                    ->label('Thứ tự')
                    ->sortable(),

                ImageColumn::make('logo_link')
                    ->label('Logo')
                    ->height(60),

                TextColumn::make('name')
                    ->label('Tên đối tác')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('website_link')
                    ->label('Website')
                    // ->url(fn (Partner $record): string => $record->website_link)
                    ->searchable(),

                TextColumn::make('description')
                    ->label('Mô tả')
                    ->limit(50)
                    ->searchable(),

                ToggleColumn::make('status')
                    ->label('Hiển thị')
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
            'index' => Pages\ListPartners::route('/'),
            'create' => Pages\CreatePartner::route('/create'),
            'edit' => Pages\EditPartner::route('/{record}/edit'),
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