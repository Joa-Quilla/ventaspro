<?php

namespace App\Filament\Pages;

use BackedEnum;
use Filament\Pages\Page;

class Reports extends Page
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-chart-bar';

    protected static ?string $navigationLabel = 'Reportes';

    protected static ?string $title = 'Reportes de Ventas';

    protected static ?int $navigationSort = 3;

    protected string $view = 'filament.pages.reports';

    public function getWidgets(): array
    {
        return [
            \App\Livewire\SalesReportWidget::class,
            \App\Livewire\SalesChart::class,
            \App\Livewire\TopProductsWidget::class,
        ];
    }

    public function getHeaderWidgets(): array
    {
        return $this->getWidgets();
    }
}
