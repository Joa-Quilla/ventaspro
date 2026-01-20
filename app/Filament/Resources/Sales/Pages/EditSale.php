<?php

namespace App\Filament\Resources\Sales\Pages;

use App\Filament\Resources\Sales\SaleResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditSale extends EditRecord
{
    protected static string $resource = SaleResource::class;

    protected function getHeaderActions(): array
    {
        return [
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

            // No permitir eliminar (trazabilidad fiscal)
            // No permitir editar ventas completadas (integridad)
        ];
    }

    /**
     * Prevenir edición de ventas completadas - redirigir a vista
     */
    public function mount(int | string $record): void
    {
        parent::mount($record);

        // Si la venta está completada, redirigir a vista
        if ($this->record->status === 'completed') {
            Notification::make()
                ->warning()
                ->title('Venta completada')
                ->body('Las ventas completadas no se pueden editar. Solo puedes visualizarlas o anularlas.')
                ->send();

            redirect()->route('filament.admin.resources.sales.view', ['record' => $this->record->id]);
        }
    }

    /**
     * Prevenir guardado de ventas completadas (por si acaso)
     */
    protected function beforeSave(): void
    {
        if ($this->record->status === 'completed') {
            Notification::make()
                ->danger()
                ->title('No se puede guardar')
                ->body('Las ventas completadas no se pueden modificar.')
                ->send();

            $this->halt();
        }
    }
}
