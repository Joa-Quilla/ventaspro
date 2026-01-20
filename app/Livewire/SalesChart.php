<?php

namespace App\Livewire;

use App\Models\Sale;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class SalesChart extends ChartWidget
{
    protected ?string $heading = 'Ventas de los Últimos 7 Días';

    protected function getData(): array
    {
        // Obtener ventas de los últimos 7 días
        $ventas = Sale::where('status', 'completed')
            ->where('created_at', '>=', now()->subDays(7))
            ->select(
                DB::raw('DATE(created_at) as fecha'),
                DB::raw('SUM(total) as total')
            )
            ->groupBy('fecha')
            ->orderBy('fecha')
            ->get();

        // Crear array con todos los últimos 7 días (incluso si no hay ventas)
        $labels = [];
        $data = [];

        for ($i = 6; $i >= 0; $i--) {
            $fecha = now()->subDays($i)->format('Y-m-d');
            $labels[] = now()->subDays($i)->format('d/m');

            $venta = $ventas->firstWhere('fecha', $fecha);
            $data[] = $venta ? round($venta->total, 2) : 0;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Ventas (Q)',
                    'data' => $data,
                    'borderColor' => '#10b981',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
                    'fill' => true,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
