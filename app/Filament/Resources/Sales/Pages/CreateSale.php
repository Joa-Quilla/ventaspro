<?php

namespace App\Filament\Resources\Sales\Pages;

use App\Filament\Resources\Sales\SaleResource;
use App\Models\Product;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CreateSale extends CreateRecord
{
    protected static string $resource = SaleResource::class;

    /**
     * Validar stock antes de guardar la venta
     */
    protected function beforeValidate(): void
    {
        $items = $this->data['items'] ?? [];

        foreach ($items as $item) {
            if (isset($item['product_id']) && isset($item['quantity'])) {
                $product = Product::find($item['product_id']);

                if (!$product) {
                    Notification::make()
                        ->danger()
                        ->title('Producto no encontrado')
                        ->body("El producto seleccionado no existe.")
                        ->send();

                    $this->halt();
                }

                if ($item['quantity'] > $product->stock) {
                    Notification::make()
                        ->danger()
                        ->title('Stock insuficiente')
                        ->body("El producto '{$product->name}' solo tiene {$product->stock} unidades disponibles. Solicitaste {$item['quantity']}.")
                        ->send();

                    $this->halt();
                }
            }
        }
    }

    /**
     * Después de crear la venta y sus items, reducir el stock
     */
    protected function afterCreate(): void
    {
        $sale = $this->record;

        // Solo reducir stock si está completada
        if ($sale->status === 'completed') {
            Log::info("🔻 Reduciendo stock para venta #{$sale->id}");

            DB::transaction(function () use ($sale) {
                // Cargar los items
                $sale->loadMissing('items');

                foreach ($sale->items as $item) {
                    $product = Product::find($item->product_id);

                    if ($product) {
                        $stockAnterior = $product->stock;
                        $product->decrement('stock', $item->quantity);
                        $product->refresh();

                        Log::info("  - {$product->name}: {$stockAnterior} → {$product->stock}");
                    }
                }
            });
        }
    }

    /**
     * Mensaje de éxito personalizado
     */
    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Venta registrada exitosamente. El stock se ha actualizado automáticamente.';
    }
}
