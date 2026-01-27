<?php

namespace App\Filament\Resources\Suppliers\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class SuppliersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Contacto')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('company_name')
                    ->label('Empresa')
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('phone')
                    ->label('Teléfono')
                    ->icon('heroicon-o-phone')
                    ->toggleable(),

                TextColumn::make('email')
                    ->label('Email')
                    ->icon('heroicon-o-envelope')
                    ->toggleable(),

                TextColumn::make('city')
                    ->label('Ciudad')
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('credit_days')
                    ->label('Días Crédito')
                    ->suffix(' días')
                    ->alignEnd()
                    ->toggleable(),

                TextColumn::make('current_debt')
                    ->label('Deuda Actual')
                    ->money('GTQ')
                    ->color(fn($state) => $state > 0 ? 'warning' : 'success')
                    ->alignEnd()
                    ->toggleable(),

                TextColumn::make('purchases_count')
                    ->label('Compras')
                    ->counts('purchases')
                    ->badge()
                    ->alignCenter()
                    ->toggleable(),

                IconColumn::make('is_active')
                    ->label('Activo')
                    ->boolean()
                    ->alignCenter(),

                TextColumn::make('created_at')
                    ->label('Fecha Registro')
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->actions([
                EditAction::make(),
            ])
            ->filters([
                TernaryFilter::make('is_active')
                    ->label('Estado')
                    ->placeholder('Todos')
                    ->trueLabel('Solo activos')
                    ->falseLabel('Solo inactivos'),

                SelectFilter::make('has_debt')
                    ->label('Deuda')
                    ->options([
                        'with_debt' => 'Con deuda',
                        'no_debt' => 'Sin deuda',
                    ])
                    ->query(function ($query, $state) {
                        if ($state['value'] === 'with_debt') {
                            return $query->where('current_debt', '>', 0);
                        }
                        if ($state['value'] === 'no_debt') {
                            return $query->where('current_debt', '<=', 0);
                        }
                    }),
            ], layout: FiltersLayout::AboveContent)
            ->filtersFormColumns(2)
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('name');
    }
}
