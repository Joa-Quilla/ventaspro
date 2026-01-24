<?php

namespace App\Livewire;

use App\Models\Sale;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class SalesReportWidget extends BaseWidget
{
    protected function getStats(): array
    {
        // Ventas de hoy
        $today = Sale::where('status', 'completed')
            ->whereDate('created_at', today())
            ->sum('total');

        $todayCount = Sale::where('status', 'completed')
            ->whereDate('created_at', today())
            ->count();

        // Ventas de ayer
        $yesterday = Sale::where('status', 'completed')
            ->whereDate('created_at', today()->subDay())
            ->sum('total');

        // Calcular cambio porcentual
        $changePercent = $yesterday > 0
            ? (($today - $yesterday) / $yesterday) * 100
            : 0;

        // Ventas del mes actual
        $thisMonth = Sale::where('status', 'completed')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('total');

        $thisMonthCount = Sale::where('status', 'completed')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        // Ventas del mes pasado
        $lastMonth = Sale::where('status', 'completed')
            ->whereMonth('created_at', now()->subMonth()->month)
            ->whereYear('created_at', now()->subMonth()->year)
            ->sum('total');

        $monthChangePercent = $lastMonth > 0
            ? (($thisMonth - $lastMonth) / $lastMonth) * 100
            : 0;

        // Ticket promedio
        $avgTicket = $todayCount > 0 ? $today / $todayCount : 0;

        return [
            Stat::make('Ventas de Hoy', 'Q' . number_format($today, 2))
                ->description($todayCount . ' ventas completadas')
                ->descriptionIcon('heroicon-o-shopping-cart')
                ->color('success')
                ->chart([7, 5, 10, 5, 15, 20, $today])
                ->extraAttributes([
                    'class' => 'cursor-pointer',
                ]),

            Stat::make('Comparación con Ayer', number_format(abs($changePercent), 1) . '%')
                ->description($changePercent >= 0 ? 'Incremento' : 'Disminución')
                ->descriptionIcon($changePercent >= 0 ? 'heroicon-o-arrow-trending-up' : 'heroicon-o-arrow-trending-down')
                ->color($changePercent >= 0 ? 'success' : 'danger'),

            Stat::make('Ventas del Mes', 'Q' . number_format($thisMonth, 2))
                ->description($thisMonthCount . ' ventas este mes')
                ->descriptionIcon('heroicon-o-calendar')
                ->color('primary'),

            Stat::make('Ticket Promedio Hoy', 'Q' . number_format($avgTicket, 2))
                ->description('Promedio por venta')
                ->descriptionIcon('heroicon-o-receipt-percent')
                ->color('info'),
        ];
    }
}
