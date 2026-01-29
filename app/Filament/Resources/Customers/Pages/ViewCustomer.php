<?php

namespace App\Filament\Resources\Customers\Pages;

use App\Filament\Resources\Customers\CustomerResource;
use Filament\Actions;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Text;
use Filament\Schemas\Schema;
use Filament\Resources\Pages\ViewRecord;

class ViewCustomer extends ViewRecord
{
    protected static string $resource = CustomerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\Action::make('newSale')
                ->label('Nueva Venta')
                ->icon('heroicon-o-shopping-cart')
                ->url(fn() => route('filament.admin.resources.sales.create'))
                ->color('success'),
            Actions\Action::make('newPayment')
                ->label('Registrar Pago')
                ->icon('heroicon-o-banknotes')
                ->url(fn() => route('filament.admin.resources.payments.create'))
                ->color('warning'),
        ];
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make('INFORMACIÓN DEL CLIENTE')
                    ->columns(3)
                    ->schema([
                        Grid::make(1)->schema([
                            Text::make(fn($record) => "Nombre Completo: {$record->name}"),
                        ]),
                        Grid::make(1)->schema([
                            Text::make(fn($record) => "Email: " . ($record->email ?? '—')),
                        ]),
                        Grid::make(1)->schema([
                            Text::make(fn($record) => "Teléfono: " . ($record->phone ?? '—')),
                        ]),
                        Grid::make(1)->schema([
                            Text::make(fn($record) => "Ciudad: " . ($record->city ?? '—')),
                        ]),
                        Grid::make(1)->schema([
                            Text::make(fn($record) => "Estado: " . ($record->state ?? '—')),
                        ]),
                        Grid::make(1)->schema([
                            Text::make(fn($record) => "Código Postal: " . ($record->zip_code ?? '—')),
                        ]),
                        Grid::make(3)->columnSpanFull()->schema([
                            Text::make(fn($record) => "Dirección: " . ($record->address ?? '—')),
                        ]),
                    ]),

                Section::make('ESTADO DE CRÉDITO')
                    ->columns(4)
                    ->schema([
                        Grid::make(1)->schema([
                            Text::make(fn($record) => "Límite de Crédito\nQ " . number_format($record->credit_limit, 2)),
                        ]),
                        Grid::make(1)->schema([
                            Text::make(fn($record) => "Saldo Actual\nQ " . number_format($record->current_balance, 2)),
                        ]),
                        Grid::make(1)->schema([
                            Text::make(fn($record) => "Crédito Disponible\nQ " . number_format($record->available_credit, 2)),
                        ]),
                        Grid::make(1)->schema([
                            Text::make(fn($record) => "Puntos\n" . number_format($record->loyalty_points) . ' pts'),
                        ]),
                    ]),

                Section::make('ESTADÍSTICAS')
                    ->columns(3)
                    ->schema([
                        Grid::make(1)->schema([
                            Text::make(fn($record) => "Total Compras\nQ " . number_format($record->total_purchases ?? 0, 2)),
                        ]),
                        Grid::make(1)->schema([
                            Text::make(fn($record) => "Total Pagos\nQ " . number_format($record->total_payments ?? 0, 2)),
                        ]),
                        Grid::make(1)->schema([
                            Text::make(fn($record) => "# Compras\n" . $record->sales()->count() . ' compras'),
                        ]),
                    ]),

                Section::make('INFORMACIÓN ADICIONAL')
                    ->columns(3)
                    ->schema([
                        Grid::make(1)->schema([
                            Text::make(fn($record) => "RFC/DNI/RUC: " . ($record->tax_id ?? '—')),
                        ]),
                        Grid::make(1)->schema([
                            Text::make(fn($record) => "Fecha de Nacimiento: " . ($record->birth_date ? $record->birth_date->format('d/m/Y') : '—')),
                        ]),
                        Grid::make(1)->schema([
                            Text::make(fn($record) => "Estado: " . ($record->is_active ? '✅ Activo' : '❌ Inactivo')),
                        ]),
                        Grid::make(3)->columnSpanFull()->schema([
                            Text::make(fn($record) => "Notas: " . ($record->notes ?? 'Sin notas')),
                        ]),
                        Grid::make(1)->schema([
                            Text::make(fn($record) => "Registrado: " . $record->created_at->format('d/m/Y H:i')),
                        ]),
                        Grid::make(1)->schema([
                            Text::make(fn($record) => "Actualizado: " . $record->updated_at->format('d/m/Y H:i')),
                        ]),
                    ]),
            ]);
    }
}
