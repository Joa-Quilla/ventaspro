<?php

namespace App\Filament\Resources\Products\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                // Información Básica
                TextInput::make('name')
                    ->label('Nombre del Producto')
                    ->required()
                    ->maxLength(255)
                    ->placeholder('Ej: Aspirina 500mg')
                    ->columnSpanFull(),

                TextInput::make('sku')
                    ->label('SKU')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255)
                    ->placeholder('Ej: MED-ASP-500')
                    ->helperText('Código único de producto'),

                TextInput::make('barcode')
                    ->label('Código de Barras')
                    ->maxLength(255)
                    ->unique(ignoreRecord: true)
                    ->placeholder('Ej: 7501234567890')
                    ->helperText('Opcional'),

                Select::make('category_id')
                    ->label('Categoría')
                    ->relationship('category', 'name')
                    ->required()
                    ->searchable()
                    ->preload()
                    ->columnSpanFull(),

                Textarea::make('description')
                    ->label('Descripción')
                    ->rows(3)
                    ->maxLength(1000)
                    ->placeholder('Descripción detallada del producto...')
                    ->columnSpanFull(),

                // Precios e Inventario
                TextInput::make('price')
                    ->label('Precio de Venta')
                    ->required()
                    ->numeric()
                    ->prefix('Q')
                    ->minValue(0)
                    ->step(0.01)
                    ->placeholder('0.00')
                    ->helperText('Precio al público'),

                TextInput::make('cost')
                    ->label('Costo de Compra')
                    ->required()
                    ->numeric()
                    ->prefix('Q')
                    ->minValue(0)
                    ->step(0.01)
                    ->default(0)
                    ->placeholder('0.00')
                    ->helperText('Costo de adquisición'),

                TextInput::make('stock')
                    ->label('Stock Actual')
                    ->required()
                    ->numeric()
                    ->minValue(0)
                    ->default(0)
                    ->placeholder('0')
                    ->helperText('Cantidad en inventario')
                    ->suffix('unidades'),

                TextInput::make('min_stock')
                    ->label('Stock Mínimo')
                    ->required()
                    ->numeric()
                    ->minValue(0)
                    ->default(0)
                    ->placeholder('0')
                    ->helperText('Alerta cuando baje de este nivel')
                    ->suffix('unidades'),

                // Configuración
                Toggle::make('is_active')
                    ->label('Producto Activo')
                    ->default(true)
                    ->helperText('Desactiva para ocultar sin eliminar')
                    ->inline(false),

                FileUpload::make('image')
                    ->label('Imagen del Producto')
                    ->image()
                    ->directory('products')
                    ->visibility('public')
                    ->maxSize(2048)
                    ->imageEditor()
                    ->imageEditorAspectRatios([
                        '1:1',
                        '4:3',
                    ])
                    ->helperText('Formatos: JPG, PNG. Máximo 2MB')
                    ->columnSpanFull(),
            ]);
    }
}
