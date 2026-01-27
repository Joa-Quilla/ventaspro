<?php

namespace App\Filament\Resources\Customers\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class CustomersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('phone')
                    ->label('Teléfono')
                    ->searchable()
                    ->icon('heroicon-o-phone'),

                TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->icon('heroicon-o-envelope')
                    ->toggleable(),

                TextColumn::make('city')
                    ->label('Ciudad')
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('credit_limit')
                    ->label('Límite Crédito')
                    ->money('GTQ')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('current_balance')
                    ->label('Saldo Actual')
                    ->money('GTQ')
                    ->sortable()
                    ->color(fn($state) => $state > 0 ? 'warning' : 'success'),

                TextColumn::make('loyalty_points')
                    ->label('Puntos')
                    ->numeric()
                    ->sortable()
                    ->suffix(' pts')
                    ->toggleable(),

                TextColumn::make('sales_count')
                    ->label('Compras')
                    ->counts('sales')
                    ->badge()
                    ->color('success'),

                IconColumn::make('is_active')
                    ->label('Activo')
                    ->boolean()
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TernaryFilter::make('is_active')
                    ->label('Estado')
                    ->trueLabel('Solo activos')
                    ->falseLabel('Solo inactivos')
                    ->native(false),

                SelectFilter::make('has_credit')
                    ->label('Con Crédito')
                    ->options([
                        'with_limit' => 'Con límite de crédito',
                        'with_balance' => 'Con saldo pendiente',
                    ])
                    ->query(function ($query, $state) {
                        if ($state['value'] === 'with_limit') {
                            $query->where('credit_limit', '>', 0);
                        } elseif ($state['value'] === 'with_balance') {
                            $query->where('current_balance', '>', 0);
                        }
                    }),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('name');
    }
}
