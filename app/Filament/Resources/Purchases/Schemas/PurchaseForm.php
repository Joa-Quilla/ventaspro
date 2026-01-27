<?php

namespace App\Filament\Resources\Purchases\Schemas;

use App\Models\Product;
use App\Models\Supplier;
use Filament\Facades\Filament;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class PurchaseForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                // Usuario (automático)
                Select::make('user_id')
                    ->label('Usuario')
                    ->relationship('user', 'name')
                    ->default(fn() => Filament::auth()->id())
                    ->required()
                    ->disabled()
                    ->dehydrated(),

                // Proveedor
                Select::make('supplier_id')
                    ->label('Proveedor')
                    ->relationship('supplier', 'name')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->createOptionForm([
                        TextInput::make('name')
                            ->label('Nombre de Contacto')
                            ->required(),
                        TextInput::make('company_name')
                            ->label('Nombre de Empresa'),
                        TextInput::make('phone')
                            ->label('Teléfono'),
                        TextInput::make('email')
                            ->label('Email')
                            ->email(),
                    ])
                    ->helperText('Selecciona o crea un nuevo proveedor'),

                // Número de factura
                TextInput::make('invoice_number')
                    ->label('N° Factura del Proveedor')
                    ->maxLength(255)
                    ->helperText('Número de la factura que te dio el proveedor'),

                // Fecha de compra
                DatePicker::make('purchase_date')
                    ->label('Fecha de Compra')
                    ->default(now())
                    ->required()
                    ->native(false),

                // Fecha de entrega
                DatePicker::make('delivery_date')
                    ->label('Fecha de Entrega')
                    ->native(false)
                    ->helperText('Fecha en que se recibió la mercancía'),

                // Estado
                Select::make('status')
                    ->label('Estado')
                    ->options([
                        'pending' => 'Pendiente',
                        'received' => 'Recibida',
                        'cancelled' => 'Cancelada',
                    ])
                    ->default('received')
                    ->required(),

                // Estado de pago
                Select::make('payment_status')
                    ->label('Estado de Pago')
                    ->options([
                        'pending' => 'Pendiente',
                        'partial' => 'Parcial',
                        'paid' => 'Pagado',
                    ])
                    ->default('pending')
                    ->required(),

                // Monto pagado
                TextInput::make('paid_amount')
                    ->label('Monto Pagado')
                    ->numeric()
                    ->prefix('Q')
                    ->default(0)
                    ->helperText('Monto ya pagado al proveedor'),

                // PRODUCTOS
                Repeater::make('items')
                    ->label('Productos')
                    ->relationship('items')
                    ->schema([
                        Select::make('product_id')
                            ->label('Producto')
                            ->searchable()
                            ->getSearchResultsUsing(
                                fn(string $search): array =>
                                Product::where(function ($query) use ($search) {
                                    $query->where('name', 'like', "%{$search}%")
                                        ->orWhere('sku', 'like', "%{$search}%")
                                        ->orWhere('barcode', 'like', "%{$search}%");
                                })
                                    ->limit(50)
                                    ->get()
                                    ->mapWithKeys(fn($product) => [
                                        $product->id => "{$product->name} - {$product->sku}"
                                    ])
                                    ->toArray()
                            )
                            ->getOptionLabelUsing(
                                fn($value): ?string =>
                                Product::find($value)?->name
                            )
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(function ($set, $get, $state) {
                                if ($state) {
                                    $product = Product::find($state);
                                    if ($product) {
                                        // Sugerir el precio de compra si existe
                                        $set('unit_cost', $product->purchase_price ?? 0);
                                        $quantity = $get('quantity') ?? 1;
                                        $unitCost = $get('unit_cost') ?? 0;
                                        $set('subtotal', $quantity * $unitCost);
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
                                $unitCost = $get('unit_cost') ?? 0;
                                $set('subtotal', $unitCost * $state);
                            }),

                        TextInput::make('unit_cost')
                            ->label('Costo Unitario')
                            ->numeric()
                            ->prefix('Q')
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(function ($set, $get, $state) {
                                $quantity = $get('quantity') ?? 1;
                                $set('subtotal', $state * $quantity);
                            }),

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
                        self::updateTotals($set, $get);
                    }),

                // Totales
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
        $tax = $subtotal * 0.12; // 12% IVA
        $total = $subtotal + $tax;

        $set('subtotal', number_format($subtotal, 2, '.', ''));
        $set('tax', number_format($tax, 2, '.', ''));
        $set('total', number_format($total, 2, '.', ''));
    }
}
