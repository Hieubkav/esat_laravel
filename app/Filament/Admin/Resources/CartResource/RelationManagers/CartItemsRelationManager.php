<?php

namespace App\Filament\Admin\Resources\CartResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;

class CartItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'items';
    
    protected static ?string $title = 'Sản phẩm trong giỏ hàng';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('product_id')
                    ->label('Sản phẩm')
                    ->relationship('product', 'name')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->columnSpan(2),
                    
                TextInput::make('quantity')
                    ->label('Số lượng')
                    ->integer()
                    ->minValue(1)
                    ->required()
                    ->columnSpan(1),
                    
                TextInput::make('price')
                    ->label('Giá')
                    ->numeric()
                    ->prefix('₫')
                    ->required()
                    ->columnSpan(1),
                    
                TextInput::make('subtotal')
                    ->label('Thành tiền')
                    ->numeric()
                    ->prefix('₫')
                    ->disabled()
                    ->dehydrated(false)
                    ->columnSpan(2),
            ])->columns(2);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('product.name')
            ->columns([
                TextColumn::make('product.name')
                    ->label('Sản phẩm')
                    ->searchable()
                    ->sortable(),
                    
                TextColumn::make('quantity')
                    ->label('Số lượng')
                    ->sortable(),
                    
                TextColumn::make('price')
                    ->label('Giá')
                    ->money('VND')
                    ->sortable(),
                    
                TextColumn::make('subtotal')
                    ->label('Thành tiền')
                    ->money('VND')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Thêm sản phẩm'),
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
            ]);
    }
}
