<?php

namespace App\Filament\Resources\Products\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ProductsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('sku')
                    ->label('SKU')
                    ->searchable(),
                TextColumn::make('barcode')
                    ->searchable(),
                TextColumn::make('category.name')
                    ->searchable(),
                TextColumn::make('price')
                    ->label('Precio Venta')
                    ->money('GTQ')
                    ->sortable(),

                TextColumn::make('purchase_price')
                    ->label('Precio Compra')
                    ->money('GTQ')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('profit_margin')
                    ->label('Margen')
                    ->state(function ($record) {
                        if ($record->purchase_price > 0) {
                            $margin = (($record->price - $record->purchase_price) / $record->purchase_price) * 100;
                            return number_format($margin, 1) . '%';
                        }
                        return 'N/A';
                    })
                    ->color(
                        fn($record) =>
                        $record->purchase_price > 0 && $record->price > $record->purchase_price
                            ? 'success'
                            : 'danger'
                    )
                    ->alignCenter()
                    ->toggleable(),

                TextColumn::make('cost')
                    ->money('GTQ')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('stock')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('min_stock')
                    ->numeric()
                    ->sortable(),
                IconColumn::make('is_active')
                    ->boolean(),
                ImageColumn::make('image'),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
