<?php

namespace App\Filament\Resources\Sales\Schemas;

use App\Models\Product;
use App\Models\Customer;
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
                    ->disabled()
                    ->dehydrated(), // Enviar valor aunque esté disabled

                // Cliente registrado
                Select::make('customer_id')
                    ->label('Cliente Registrado')
                    ->relationship('customer', 'name')
                    ->searchable()
                    ->preload()
                    ->createOptionForm([
                        TextInput::make('name')
                            ->label('Nombre')
                            ->required(),
                        TextInput::make('phone')
                            ->label('Teléfono'),
                        TextInput::make('email')
                            ->label('Email')
                            ->email(),
                    ])
                    ->helperText('Opcional: selecciona un cliente registrado'),

                // Cliente manual (si no está registrado)
                TextInput::make('customer_name')
                    ->label('Nombre del Cliente (Manual)')
                    ->placeholder('Ej: Juan Pérez')
                    ->helperText('Solo si el cliente no está registrado'),

                TextInput::make('customer_phone')
                    ->label('Teléfono del Cliente (Manual)')
                    ->tel()
                    ->placeholder('Ej: 5555-5555'),

                // PRODUCTOS (Lo más importante)
                Repeater::make('items')
                    ->label('Productos')
                    ->relationship('items')
                    ->schema([
                        Select::make('product_id')
                            ->label('Producto (Nombre, SKU o Código de Barras)')
                            ->searchable()
                            ->getSearchResultsUsing(
                                fn(string $search): array =>
                                Product::where('is_active', true)
                                    ->where(function ($query) use ($search) {
                                        $query->where('name', 'like', "%{$search}%")
                                            ->orWhere('sku', 'like', "%{$search}%")
                                            ->orWhere('barcode', 'like', "%{$search}%");
                                    })
                                    ->limit(50)
                                    ->get()
                                    ->mapWithKeys(fn($product) => [
                                        $product->id => "{$product->name} - {$product->sku} (Stock: {$product->stock})"
                                    ])
                                    ->toArray()
                            )
                            ->getOptionLabelUsing(
                                fn($value): ?string =>
                                Product::find($value)?->name
                            )
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(function ($set, $get, $state, $livewire) {
                                if ($state) {
                                    $product = Product::find($state);
                                    if ($product) {
                                        $set('unit_price', $product->price);
                                        $quantity = $get('quantity') ?? 1;
                                        $set('subtotal', $product->price * $quantity);

                                        // Mostrar stock disponible
                                        $set('../../stock_info', "Stock disponible: {$product->stock}");

                                        // Auto-agregar nueva línea para seguir escaneando
                                        // Esto permite flujo continuo: escanear -> siguiente producto
                                        $currentKey = $get('../../currentItemKey');
                                        if ($currentKey !== null) {
                                            // Solo agregar si esta línea está completa
                                            $allItems = $get('../../items') ?? [];
                                            $lastItem = end($allItems);
                                            if ($lastItem && isset($lastItem['product_id'])) {
                                                // Trigger para agregar nueva línea
                                                $livewire->dispatch('repeater-add-item', component: 'items');
                                            }
                                        }
                                    }
                                }
                            })
                            ->placeholder('Escribe o escanea aquí'),

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

                                // Validar stock disponible
                                $productId = $get('product_id');
                                if ($productId && $state) {
                                    $product = Product::find($productId);
                                    if ($product && $state > $product->stock) {
                                        $set('../../stock_warning', "⚠️ Solo hay {$product->stock} unidades disponibles");
                                    } else {
                                        $set('../../stock_warning', null);
                                    }
                                }
                            })
                            ->helperText(fn($get) => $get('product_id') ? 'Verifica el stock antes de continuar' : ''),

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
