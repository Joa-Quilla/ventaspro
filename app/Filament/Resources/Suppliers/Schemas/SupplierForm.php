<?php

namespace App\Filament\Resources\Suppliers\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class SupplierForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                TextInput::make('name')
                    ->label('Nombre de Contacto')
                    ->required()
                    ->maxLength(255),

                TextInput::make('company_name')
                    ->label('Nombre de Empresa')
                    ->maxLength(255),

                TextInput::make('tax_id')
                    ->label('RFC/RUC/NIT')
                    ->maxLength(255)
                    ->helperText('Identificación fiscal'),

                TextInput::make('email')
                    ->label('Email')
                    ->email()
                    ->maxLength(255),

                TextInput::make('phone')
                    ->label('Teléfono')
                    ->tel()
                    ->maxLength(255),

                TextInput::make('mobile')
                    ->label('Celular')
                    ->tel()
                    ->maxLength(255),

                TextInput::make('city')
                    ->label('Ciudad')
                    ->maxLength(255),

                TextInput::make('state')
                    ->label('Departamento/Estado')
                    ->maxLength(255),

                TextInput::make('zip_code')
                    ->label('Código Postal')
                    ->maxLength(255),

                TextInput::make('country')
                    ->label('País')
                    ->default('Guatemala')
                    ->maxLength(255),

                TextInput::make('credit_days')
                    ->label('Días de Crédito')
                    ->numeric()
                    ->default(0)
                    ->minValue(0)
                    ->helperText('Días que da el proveedor para pagar'),

                TextInput::make('current_debt')
                    ->label('Deuda Actual')
                    ->numeric()
                    ->prefix('Q')
                    ->default(0)
                    ->disabled()
                    ->dehydrated(false)
                    ->helperText('Se actualiza automáticamente con las compras'),

                Textarea::make('address')
                    ->label('Dirección')
                    ->rows(2)
                    ->columnSpanFull(),

                Textarea::make('notes')
                    ->label('Notas')
                    ->rows(3)
                    ->columnSpanFull(),

                Toggle::make('is_active')
                    ->label('Activo')
                    ->default(true)
                    ->columnSpanFull(),
            ]);
    }
}
