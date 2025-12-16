<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\AssociationResource\Pages;
use App\Models\Association;
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
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use App\Constants\NavigationGroups;
use App\Traits\HasRoleBasedAccess;

class AssociationResource extends Resource
{
    use HasRoleBasedAccess;
    protected static ?string $model = Association::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';

    protected static ?string $navigationGroup = NavigationGroups::CONTENT_MANAGEMENT;

    protected static ?string $navigationLabel = 'Hiệp hội';

    protected static ?string $modelLabel = 'Hiệp hội';

    protected static ?string $pluralModelLabel = 'Hiệp hội';

    protected static ?int $navigationSort = 34;

    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Thông tin hiệp hội')
                    ->schema([
                        TextInput::make('name')
                            ->label('Tên hiệp hội')
                            ->required()
                            ->maxLength(255)
                            ->columnSpan(2),

                        FileUpload::make('image_link')
                            ->label('Logo/Hình ảnh')
                            ->image()
                            ->directory('associations/logos')
                            ->visibility('public')
                            ->imageResizeMode('cover')
                            ->imageResizeTargetWidth(400)
                            ->imageResizeTargetHeight(400)
                            ->maxSize(2048)
                            ->imageEditor()
                            ->saveUploadedFileUsing(function ($file, $get) {
                                $imageService = app(\App\Services\ImageService::class);
                                $associationName = $get('name') ?? 'hiep-hoi';
                                return $imageService->saveImage(
                                    $file,
                                    'associations/logos',
                                    400,   // width
                                    400,   // height
                                    95,    // quality
                                    "logo-{$associationName}" // SEO-friendly name
                                );
                            })
                            ->columnSpan(1),

                        TextInput::make('website_link')
                            ->label('Website')
                            ->url()
                            ->maxLength(500)
                            ->columnSpan(1),

                        Textarea::make('description')
                            ->label('Mô tả')
                            ->rows(4)
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
                    ->label('Logo')
                    ->height(60)
                    ->width(60)
                    ->circular(),

                TextColumn::make('name')
                    ->label('Tên hiệp hội')
                    ->searchable()
                    ->sortable()
                    ->weight('medium'),

                TextColumn::make('website_link')
                    ->label('Website')
                    ->limit(30)
                    ->url(fn ($record) => $record->website_link)
                    ->openUrlInNewTab(),

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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAssociations::route('/'),
            'create' => Pages\CreateAssociation::route('/create'),
            'edit' => Pages\EditAssociation::route('/{record}/edit'),
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
