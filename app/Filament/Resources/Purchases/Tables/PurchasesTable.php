<?php

namespace App\Filament\Resources\Purchases\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class PurchasesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),

                TextColumn::make('supplier.name')
                    ->label('Proveedor')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('invoice_number')
                    ->label('N° Factura')
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('purchase_date')
                    ->label('Fecha Compra')
                    ->date('d/m/Y')
                    ->sortable(),

                TextColumn::make('total')
                    ->label('Total')
                    ->money('GTQ')
                    ->sortable()
                    ->alignEnd(),

                TextColumn::make('status')
                    ->label('Estado')
                    ->badge()
                    ->color(fn($state) => match ($state) {
                        'pending' => 'warning',
                        'received' => 'success',
                        'cancelled' => 'danger',
                    })
                    ->formatStateUsing(fn($state) => match ($state) {
                        'pending' => 'Pendiente',
                        'received' => 'Recibida',
                        'cancelled' => 'Cancelada',
                    }),

                TextColumn::make('payment_status')
                    ->label('Pago')
                    ->badge()
                    ->color(fn($state) => match ($state) {
                        'pending' => 'danger',
                        'partial' => 'warning',
                        'paid' => 'success',
                    })
                    ->formatStateUsing(fn($state) => match ($state) {
                        'pending' => 'Pendiente',
                        'partial' => 'Parcial',
                        'paid' => 'Pagado',
                    }),

                TextColumn::make('paid_amount')
                    ->label('Pagado')
                    ->money('GTQ')
                    ->alignEnd()
                    ->toggleable(),

                TextColumn::make('user.name')
                    ->label('Usuario')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->label('Registrado')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->actions([
                EditAction::make(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Estado')
                    ->options([
                        'pending' => 'Pendiente',
                        'received' => 'Recibida',
                        'cancelled' => 'Cancelada',
                    ]),

                SelectFilter::make('payment_status')
                    ->label('Estado de Pago')
                    ->options([
                        'pending' => 'Pendiente',
                        'partial' => 'Parcial',
                        'paid' => 'Pagado',
                    ]),

                SelectFilter::make('supplier_id')
                    ->label('Proveedor')
                    ->relationship('supplier', 'name')
                    ->searchable()
                    ->preload(),
            ], layout: FiltersLayout::AboveContent)
            ->filtersFormColumns(3)
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('purchase_date', 'desc');
    }
}
