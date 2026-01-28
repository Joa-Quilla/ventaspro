<?php

namespace App\Observers;

use App\Models\Sale;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SaleObserver
{
    /**
     * Después de guardar una venta completamente (incluyendo items), reducir el stock
     * Nota: 'saved' se ejecuta después de que todo el modelo y sus relaciones se hayan guardado
     */
    public function saved(Sale $sale): void
    {
        // Solo reducir stock si la venta está completada
        if ($sale->status === 'completed' && !$sale->wasRecentlyCreated) {
            return; // Ya se procesó en created
        }

        if ($sale->status === 'completed' && $sale->wasRecentlyCreated) {
            // Esperar un momento para que los items se guarden
            $sale->loadMissing('items');

            if ($sale->items->count() > 0) {
                $this->reduceStock($sale);

                // Si es venta a crédito, actualizar balance del cliente
                if ($sale->payment_method === 'credit' && $sale->customer_id) {
                    $this->updateCustomerBalance($sale, 'add');
                }
            }
        }
    }

    /**
     * Después de crear una venta, reducir el stock de los productos
     * Este evento no funciona bien porque los items aún no están guardados
     */
    public function created(Sale $sale): void
    {
        // Comentado: los items aún no existen aquí
        // Usar 'saved' en su lugar
    }

    /**
     * Al actualizar una venta, manejar cambios de estado
     */
    public function updated(Sale $sale): void
    {
        // Si cambió de completed a cancelled, restaurar stock
        if ($sale->isDirty('status')) {
            $originalStatus = $sale->getOriginal('status');

            // Cargar items si no están cargados
            $sale->loadMissing('items');

            if ($originalStatus === 'completed' && $sale->status === 'cancelled') {
                $this->restoreStock($sale);
            }

            if ($originalStatus === 'cancelled' && $sale->status === 'completed') {
                $this->reduceStock($sale);
            }
        }
    }

    /**
     * Reducir stock de los productos vendidos
     */
    protected function reduceStock(Sale $sale): void
    {
        Log::info("🔻 Reduciendo stock para venta #{$sale->id}");

        DB::transaction(function () use ($sale) {
            foreach ($sale->items as $item) {
                $product = Product::find($item->product_id);

                if ($product) {
                    $stockAnterior = $product->stock;
                    $product->decrement('stock', $item->quantity);
                    Log::info("  - {$product->name}: {$stockAnterior} → {$product->fresh()->stock}");
                }
            }
        });
    }

    /**
     * Restaurar stock al cancelar una venta
     */
    protected function restoreStock(Sale $sale): void
    {
        DB::transaction(function () use ($sale) {
            foreach ($sale->items as $item) {
                $product = Product::find($item->product_id);

                if ($product) {
                    $product->increment('stock', $item->quantity);
                }
            }
        });

        // Si era venta a crédito, restar del balance del cliente
        if ($sale->payment_method === 'credit' && $sale->customer_id) {
            $this->updateCustomerBalance($sale, 'subtract');
        }
    }

    /**
     * Actualizar balance del cliente en ventas a crédito
     */
    protected function updateCustomerBalance(Sale $sale, string $operation): void
    {
        $customer = \App\Models\Customer::find($sale->customer_id);

        if ($customer) {
            if ($operation === 'add') {
                // Sumar al balance (aumentar deuda)
                $customer->increment('current_balance', $sale->total);
            } elseif ($operation === 'subtract') {
                // Restar del balance (disminuir deuda)
                $customer->decrement('current_balance', $sale->total);
            }
        }
    }
}
