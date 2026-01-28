<?php

namespace App\Filament\Resources\Payments\Schemas;

use App\Models\Customer;
use Filament\Facades\Filament;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class PaymentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                // Usuario (automático)
                Select::make('user_id')
                    ->label('Usuario')
                    ->relationship('user', 'name')
                    ->default(fn() => Filament::auth()->id())
                    ->required()
                    ->disabled()
                    ->dehydrated(),

                // Cliente
                Select::make('customer_id')
                    ->label('Cliente')
                    ->relationship('customer', 'name')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->reactive()
                    ->helperText(
                        fn($get) =>
                        $get('customer_id')
                            ? 'Saldo pendiente: Q' . number_format(Customer::find($get('customer_id'))?->current_balance ?? 0, 2)
                            : 'Selecciona un cliente'
                    ),

                // Monto
                TextInput::make('amount')
                    ->label('Monto del Pago')
                    ->numeric()
                    ->prefix('Q')
                    ->required()
                    ->reactive()
                    ->afterStateUpdated(function ($set, $get, $state) {
                        $customerId = $get('customer_id');
                        if ($customerId && $state) {
                            $customer = Customer::find($customerId);
                            if ($customer && $state > $customer->current_balance) {
                                // Advertencia si el pago es mayor que la deuda
                                $set('amount', $customer->current_balance);
                            }
                        }
                    })
                    ->helperText('Monto que el cliente está pagando'),

                // Fecha de pago
                DatePicker::make('payment_date')
                    ->label('Fecha de Pago')
                    ->default(now())
                    ->required()
                    ->native(false),

                // Método de pago
                Select::make('payment_method')
                    ->label('Método de Pago')
                    ->options([
                        'cash' => 'Efectivo',
                        'card' => 'Tarjeta',
                        'transfer' => 'Transferencia',
                        'check' => 'Cheque',
                    ])
                    ->default('cash')
                    ->required(),

                // Referencia
                TextInput::make('reference')
                    ->label('Referencia')
                    ->maxLength(255)
                    ->helperText('Número de referencia, cheque, transacción, etc.'),

                // Notas
                Textarea::make('notes')
                    ->label('Notas')
                    ->rows(3)
                    ->columnSpanFull(),
            ]);
    }
}
