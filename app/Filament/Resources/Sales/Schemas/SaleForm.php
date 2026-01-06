<?php

namespace App\Filament\Resources\Sales\Schemas;

use App\Models\Product;
use Filament\Facades\Filament;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Repeater;
use Filament\Schemas\Schema;

class SaleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                // Usuario (cajero) - automático
                Select::make('user_id')
                    ->label('Cajero')
                    ->relationship('user', 'name')
                    ->default(fn() => Filament::auth()->id())
                    ->required()
                    ->disabled(), // El cajero es el usuario logueado

                // Cliente
                TextInput::make('customer_name')
                    ->label('Nombre del Cliente')
                    ->placeholder('Ej: Juan Pérez'),

                TextInput::make('customer_phone')
                    ->label('Teléfono del Cliente')
                    ->tel()
                    ->placeholder('Ej: 5555-5555'),

                // PRODUCTOS (Lo más importante)
                Repeater::make('items')
                    ->label('Productos')
                    ->relationship('items')
                    ->schema([
                        Select::make('product_id')
                            ->label('Producto')
                            ->options(Product::where('is_active', true)->pluck('name', 'id'))
                            ->required()
                            ->searchable()
                            ->reactive()
                            ->afterStateUpdated(function ($set, $get, $state) {
                                if ($state) {
                                    $product = Product::find($state);
                                    if ($product) {
                                        $set('unit_price', $product->price);
                                        $quantity = $get('quantity') ?? 1;
                                        $set('subtotal', $product->price * $quantity);
                                    }
                                }
                            }),

                        TextInput::make('quantity')
                            ->label('Cantidad')
                            ->numeric()
                            ->default(1)
                            ->minValue(1)
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(function ($set, $get, $state) {
                                // Recalcular subtotal al cambiar cantidad
                                $price = $get('unit_price') ?? 0;
                                $set('subtotal', $price * $state);
                            }),

                        TextInput::make('unit_price')
                            ->label('Precio Unitario')
                            ->numeric()
                            ->prefix('Q')
                            ->disabled()
                            ->dehydrated(), // Enviar valor aunque esté disabled

                        TextInput::make('subtotal')
                            ->label('Subtotal')
                            ->numeric()
                            ->prefix('Q')
                            ->disabled()
                            ->dehydrated(),
                    ])
                    ->columns(4)
                    ->defaultItems(1)
                    ->addActionLabel('Agregar Producto')
                    ->columnSpanFull()
                    ->live()
                    ->afterStateUpdated(function ($set, $get) {
                        // Calcular totales automáticamente
                        self::updateTotals($set, $get);
                    }),

                // Totales (calculados automáticamente)
                TextInput::make('subtotal')
                    ->label('Subtotal')
                    ->numeric()
                    ->prefix('Q')
                    ->disabled()
                    ->dehydrated(),

                TextInput::make('tax')
                    ->label('IVA (12%)')
                    ->numeric()
                    ->prefix('Q')
                    ->default(0)
                    ->disabled()
                    ->dehydrated(),

                TextInput::make('total')
                    ->label('Total')
                    ->numeric()
                    ->prefix('Q')
                    ->disabled()
                    ->dehydrated()
                    ->extraAttributes(['class' => 'text-xl font-bold']),

                // Método de pago
                Select::make('payment_method')
                    ->label('Método de Pago')
                    ->options([
                        'cash' => 'Efectivo',
                        'card' => 'Tarjeta',
                        'transfer' => 'Transferencia',
                    ])
                    ->default('cash')
                    ->required(),

                // Estado
                Select::make('status')
                    ->label('Estado')
                    ->options([
                        'completed' => 'Completada',
                        'cancelled' => 'Cancelada',
                    ])
                    ->default('completed')
                    ->required(),

                // Notas
                Textarea::make('notes')
                    ->label('Notas')
                    ->rows(3)
                    ->columnSpanFull(),
            ]);
    }

    /**
     * Calcular totales automáticamente
     */
    protected static function updateTotals($set, $get): void
    {
        $items = $get('items') ?? [];
        $subtotal = collect($items)->sum('subtotal');
        $tax = $subtotal * 0.12; // 12% IVA Guatemala
        $total = $subtotal + $tax;

        $set('subtotal', number_format($subtotal, 2, '.', ''));
        $set('tax', number_format($tax, 2, '.', ''));
        $set('total', number_format($total, 2, '.', ''));
    }
}
