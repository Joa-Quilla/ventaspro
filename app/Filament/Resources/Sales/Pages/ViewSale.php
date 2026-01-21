<?php

namespace App\Filament\Resources\Sales\Pages;

use App\Filament\Resources\Sales\SaleResource;
use Filament\Actions\EditAction;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

class ViewSale extends ViewRecord
{
    protected static string $resource = SaleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('print')
                ->label('Imprimir Ticket')
                ->icon('heroicon-o-printer')
                ->color('success')
                ->url(fn() => route('tickets.sale', ['sale' => $this->record->id]))
                ->openUrlInNewTab(),

            Action::make('cancel')
                ->label('Anular Venta')
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->requiresConfirmation()
                ->modalHeading('¿Anular esta venta?')
                ->modalDescription('El stock se restaurará automáticamente y la venta quedará como cancelada.')
                ->visible(fn() => $this->record->status === 'completed')
                ->action(function () {
                    $this->record->update(['status' => 'cancelled']);

                    Notification::make()
                        ->success()
                        ->title('Venta anulada')
                        ->body('El stock ha sido restaurado automáticamente.')
                        ->send();

                    return redirect()->route('filament.admin.resources.sales.index');
                }),

            // Solo mostrar editar si NO está completada
            EditAction::make()
                ->visible(fn() => $this->record->status !== 'completed'),
        ];
    }
}
