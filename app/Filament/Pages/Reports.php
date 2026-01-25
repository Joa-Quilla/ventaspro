<?php

namespace App\Filament\Pages;

use App\Models\Product;
use App\Models\Sale;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class Reports extends Page
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-chart-bar';

    protected static ?string $navigationLabel = 'Reportes';

    protected static ?string $title = 'Reportes y Exportaciones';

    protected static ?int $navigationSort = 3;

    protected string $view = 'filament.pages.reports';

    public string $reportType = 'sales';
    public string $startDate = '';
    public string $endDate = '';
    public array $data = [];

    public function mount(): void
    {
        $this->startDate = now()->startOfMonth()->format('Y-m-d');
        $this->endDate = now()->format('Y-m-d');
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('generate')
                ->label('Generar Reporte')
                ->icon('heroicon-o-document-chart-bar')
                ->color('primary')
                ->action(function () {
                    $this->generateReport();
                }),

            Action::make('exportExcel')
                ->label('Exportar Excel')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->visible(fn() => !empty($this->data))
                ->action(function () {
                    return $this->exportToExcel();
                }),
        ];
    }

    public function generateReport(): void
    {
        $this->data = match ($this->reportType) {
            'sales' => $this->getSalesReport(),
            'products' => $this->getProductsReport(),
            'low_stock' => $this->getLowStockReport(),
            'inventory' => $this->getInventoryReport(),
            default => [],
        };

        Notification::make()
            ->success()
            ->title('Reporte generado')
            ->body('Se generaron ' . count($this->data) . ' registros.')
            ->send();
    }

    private function getSalesReport(): array
    {
        return Sale::where('status', 'completed')
            ->whereBetween('created_at', [$this->startDate, $this->endDate . ' 23:59:59'])
            ->with(['user', 'items.product'])
            ->get()
            ->map(fn($sale) => [
                'ID' => $sale->id,
                'Fecha' => $sale->created_at->format('d/m/Y H:i'),
                'Cliente' => $sale->customer_name ?: 'Cliente General',
                'Total' => 'Q' . number_format($sale->total, 2),
                'Método Pago' => match ($sale->payment_method) {
                    'cash' => 'Efectivo',
                    'card' => 'Tarjeta',
                    'transfer' => 'Transferencia',
                    default => $sale->payment_method
                },
                'Cajero' => $sale->user->name,
                'Productos' => $sale->items->count(),
            ])
            ->toArray();
    }

    private function getProductsReport(): array
    {
        return DB::table('products')
            ->join('sale_items', 'products.id', '=', 'sale_items.product_id')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->where('sales.status', 'completed')
            ->whereBetween('sales.created_at', [$this->startDate, $this->endDate . ' 23:59:59'])
            ->select(
                'products.id',
                'products.name',
                'products.sku',
                DB::raw('SUM(sale_items.quantity) as total_vendido'),
                DB::raw('SUM(sale_items.subtotal) as total_ingresos')
            )
            ->groupBy('products.id', 'products.name', 'products.sku')
            ->orderByDesc('total_vendido')
            ->get()
            ->map(fn($item) => [
                'Producto' => $item->name,
                'SKU' => $item->sku,
                'Cantidad Vendida' => $item->total_vendido,
                'Ingresos' => 'Q' . number_format($item->total_ingresos, 2),
            ])
            ->toArray();
    }

    private function getLowStockReport(): array
    {
        return Product::whereColumn('stock', '<=', 'min_stock')
            ->where('is_active', true)
            ->with('category')
            ->get()
            ->map(fn($product) => [
                'Producto' => $product->name,
                'SKU' => $product->sku,
                'Categoría' => $product->category->name,
                'Stock Actual' => $product->stock,
                'Stock Mínimo' => $product->min_stock,
                'Diferencia' => $product->min_stock - $product->stock,
                'Precio' => 'Q' . number_format($product->price, 2),
            ])
            ->toArray();
    }

    private function getInventoryReport(): array
    {
        return Product::where('is_active', true)
            ->with('category')
            ->get()
            ->map(fn($product) => [
                'ID' => $product->id,
                'Producto' => $product->name,
                'SKU' => $product->sku,
                'Código Barras' => $product->barcode ?: 'N/A',
                'Categoría' => $product->category->name,
                'Stock' => $product->stock,
                'Stock Mínimo' => $product->min_stock,
                'Precio Venta' => 'Q' . number_format($product->price, 2),
                'Costo' => 'Q' . number_format($product->cost, 2),
                'Valor Inventario' => 'Q' . number_format($product->stock * $product->cost, 2),
            ])
            ->toArray();
    }

    public function exportToExcel()
    {
        if (empty($this->data)) {
            Notification::make()
                ->warning()
                ->title('Sin datos')
                ->body('Primero genera el reporte antes de exportar.')
                ->send();
            return;
        }

        $filename = match ($this->reportType) {
            'sales' => 'reporte_ventas',
            'products' => 'productos_mas_vendidos',
            'low_stock' => 'productos_stock_bajo',
            'inventory' => 'inventario_completo',
            default => 'reporte',
        } . '_' . now()->format('Y-m-d_H-i-s') . '.xlsx';

        return Excel::download(
            new class($this->data) implements \Maatwebsite\Excel\Concerns\FromArray {
                public function __construct(private array $data) {}
                public function array(): array
                {
                    return $this->data;
                }
            },
            $filename
        );
    }
}
