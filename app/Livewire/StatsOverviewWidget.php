<?php

namespace App\Livewire;

use App\Models\Sale;
use App\Models\Product;
use Filament\Widgets\StatsOverviewWidget as BaseStatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class StatsOverviewWidget extends BaseStatsOverviewWidget
{
    protected function getStats(): array
    {
        // Ventas de hoy
        $ventasHoy = Sale::whereDate('created_at', today())
            ->where('status', 'completed')
            ->sum('total');

        $cantidadVentasHoy = Sale::whereDate('created_at', today())
            ->where('status', 'completed')
            ->count();

        // Ventas del mes
        $ventasMes = Sale::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->where('status', 'completed')
            ->sum('total');

        // Productos vendidos hoy
        $productosVendidosHoy = DB::table('sale_items')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->whereDate('sales.created_at', today())
            ->where('sales.status', 'completed')
            ->sum('sale_items.quantity');

        // Productos con stock bajo
        $productosStockBajo = Product::whereColumn('stock', '<=', 'min_stock')
            ->where('is_active', true)
            ->count();

        return [
            Stat::make('Ventas de Hoy', 'Q ' . number_format($ventasHoy, 2))
                ->description("{$cantidadVentasHoy} ventas realizadas")
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->chart([7, 3, 4, 5, 6, 3, 5, 3])
                ->color('success'),

            Stat::make('Ventas del Mes', 'Q ' . number_format($ventasMes, 2))
                ->description('Total acumulado')
                ->descriptionIcon('heroicon-m-chart-bar')
                ->color('primary'),

            Stat::make('Productos Vendidos Hoy', $productosVendidosHoy)
                ->description('Unidades')
                ->descriptionIcon('heroicon-m-shopping-bag')
                ->color('warning'),

            Stat::make('Stock Bajo', $productosStockBajo)
                ->description('Productos requieren reposición')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color($productosStockBajo > 0 ? 'danger' : 'success'),
        ];
    }
}
