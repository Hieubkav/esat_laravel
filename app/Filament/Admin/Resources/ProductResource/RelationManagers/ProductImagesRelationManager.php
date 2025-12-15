<?php

namespace App\Filament\Admin\Resources\ProductResource\RelationManagers;

use App\Services\ImageService;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProductImagesRelationManager extends RelationManager
{
    protected static string $relationship = 'productImages';

    protected static ?string $title = 'Hình ảnh sản phẩm';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                FileUpload::make('image_link')
                    ->label('Hình ảnh')
                    ->required()
                    ->image()
                    ->directory('products/gallery')
                    ->visibility('public')
                    ->maxSize(4048)
                    ->imageEditor()
                    ->imageResizeMode('cover')
                    ->imageResizeTargetWidth(1200)
                    ->imageResizeTargetHeight(800)
                    ->saveUploadedFileUsing(function ($file, $get, $livewire) {
                        $imageService = app(ImageService::class);
                        $productName = $livewire->ownerRecord->name ?? 'san-pham';
                        return $imageService->saveImage(
                            $file,
                            'products/gallery',
                            1200,  // width
                            800,   // height
                            100,   // quality
                            "gallery-{$productName}" // SEO-friendly name
                        );
                    }),

                TextInput::make('order')
                    ->label('Thứ tự hiển thị')
                    ->integer()
                    ->default(0),

                Toggle::make('status')
                    ->label('Hiển thị')
                    ->default(true)
                    ->onColor('success')
                    ->offColor('danger'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('order')
                    ->label('Thứ tự')
                    ->sortable(),

                ImageColumn::make('image_link')
                    ->label('Hình ảnh')
                    ->height(100),

                ToggleColumn::make('status')
                    ->label('Hiển thị')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Thêm hình ảnh'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('order', 'asc');
    }
}