<?php

namespace App\Filament\Resources\Sales\Tables;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class SalesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('#')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('customer_name')
                    ->label('Cliente')
                    ->searchable()
                    ->default('Cliente General')
                    ->description(fn($record) => $record->customer_phone),

                TextColumn::make('items_count')
                    ->label('Productos')
                    ->counts('items')
                    ->badge()
                    ->color('info'),

                TextColumn::make('items')
                    ->label('Detalle')
                    ->formatStateUsing(
                        fn($record) =>
                        $record->items->take(2)->map(
                            fn($item) =>
                            "{$item->quantity}x {$item->product->name}"
                        )->join(', ') . ($record->items->count() > 2 ? '...' : '')
                    )
                    ->wrap()
                    ->limit(50),

                TextColumn::make('total')
                    ->label('Total')
                    ->money('GTQ')
                    ->sortable()
                    ->weight('bold')
                    ->color('success'),

                TextColumn::make('payment_method')
                    ->label('Pago')
                    ->badge()
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'cash' => 'Efectivo',
                        'card' => 'Tarjeta',
                        'transfer' => 'Transferencia',
                        default => $state,
                    })
                    ->color(fn(string $state): string => match ($state) {
                        'cash' => 'success',
                        'card' => 'warning',
                        'transfer' => 'info',
                        default => 'gray',
                    }),

                TextColumn::make('status')
                    ->label('Estado')
                    ->badge()
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'completed' => 'Completada',
                        'cancelled' => 'Cancelada',
                        default => $state,
                    })
                    ->color(fn(string $state): string => match ($state) {
                        'completed' => 'success',
                        'cancelled' => 'danger',
                        default => 'gray',
                    }),

                TextColumn::make('user.name')
                    ->label('Cajero')
                    ->toggleable()
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Fecha')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Estado')
                    ->options([
                        'completed' => 'Completada',
                        'cancelled' => 'Cancelada',
                    ]),

                SelectFilter::make('payment_method')
                    ->label('Método de Pago')
                    ->options([
                        'cash' => 'Efectivo',
                        'card' => 'Tarjeta',
                        'transfer' => 'Transferencia',
                    ]),

                Filter::make('created_at')
                    ->form([
                        \Filament\Forms\Components\DatePicker::make('from')
                            ->label('Desde'),
                        \Filament\Forms\Components\DatePicker::make('until')
                            ->label('Hasta'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'],
                                fn(Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['until'],
                                fn(Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    }),
            ]);
    }
}
