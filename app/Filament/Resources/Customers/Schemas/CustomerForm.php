<?php

namespace App\Filament\Resources\Customers\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class CustomerForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                TextInput::make('name')
                    ->label('Nombre Completo')
                    ->required()
                    ->maxLength(255),

                TextInput::make('email')
                    ->label('Correo Electrónico')
                    ->email()
                    ->maxLength(255),

                TextInput::make('phone')
                    ->label('Teléfono')
                    ->tel()
                    ->maxLength(255),

                TextInput::make('tax_id')
                    ->label('RFC/DNI/RUC')
                    ->maxLength(255),

                DatePicker::make('birth_date')
                    ->label('Fecha de Nacimiento')
                    ->native(false)
                    ->maxDate(now()),

                Textarea::make('address')
                    ->label('Dirección')
                    ->rows(2)
                    ->columnSpanFull(),

                TextInput::make('city')
                    ->label('Ciudad')
                    ->maxLength(255),

                TextInput::make('state')
                    ->label('Estado/Provincia')
                    ->maxLength(255),

                TextInput::make('zip_code')
                    ->label('Código Postal')
                    ->maxLength(255),

                TextInput::make('credit_limit')
                    ->label('Límite de Crédito')
                    ->numeric()
                    ->prefix('Q')
                    ->default(0)
                    ->minValue(0)
                    ->step(0.01),

                TextInput::make('current_balance')
                    ->label('Saldo Actual')
                    ->numeric()
                    ->prefix('Q')
                    ->default(0)
                    ->disabled()
                    ->dehydrated(false)
                    ->helperText('Se actualiza automáticamente'),

                TextInput::make('loyalty_points')
                    ->label('Puntos de Fidelidad')
                    ->numeric()
                    ->default(0)
                    ->minValue(0)
                    ->suffix('pts'),

                Textarea::make('notes')
                    ->label('Notas')
                    ->rows(3)
                    ->columnSpanFull(),

                Toggle::make('is_active')
                    ->label('Cliente Activo')
                    ->default(true)
                    ->columnSpanFull()
                    ->helperText('Desactiva para ocultar este cliente'),
            ]);
    }
}
