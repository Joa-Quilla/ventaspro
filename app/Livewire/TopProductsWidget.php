<?php

namespace App\Livewire;

use App\Models\Product;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class TopProductsWidget extends TableWidget
{
    protected static ?string $heading = 'Top 5 Productos Más Vendidos';

    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Product::query()
                    ->select(
                        'products.id',
                        'products.name',
                        'products.category_id',
                        'products.stock',
                        'products.min_stock',
                        'products.price'
                    )
                    ->selectRaw('COALESCE(SUM(sale_items.quantity), 0) as total_vendido')
                    ->leftJoin('sale_items', 'products.id', '=', 'sale_items.product_id')
                    ->leftJoin('sales', function ($join) {
                        $join->on('sale_items.sale_id', '=', 'sales.id')
                            ->where('sales.status', '=', 'completed');
                    })
                    ->groupBy(
                        'products.id',
                        'products.name',
                        'products.category_id',
                        'products.stock',
                        'products.min_stock',
                        'products.price'
                    )
                    ->orderByDesc('total_vendido')
                    ->limit(5)
            )
            ->columns([
                TextColumn::make('name')
                    ->label('Producto')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('category.name')
                    ->label('Categoría')
                    ->badge()
                    ->color('info'),

                TextColumn::make('total_vendido')
                    ->label('Unidades Vendidas')
                    ->sortable()
                    ->badge()
                    ->color('success'),

                TextColumn::make('stock')
                    ->label('Stock Actual')
                    ->badge()
                    ->color(fn($record) => $record->stock <= $record->min_stock ? 'danger' : 'success'),

                TextColumn::make('price')
                    ->label('Precio')
                    ->money('GTQ')
                    ->sortable(),
            ])
            ->defaultSort('total_vendido', 'desc');
    }
}
