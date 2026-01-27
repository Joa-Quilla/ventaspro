<?php

namespace App\Observers;

use App\Models\Product;
use App\Models\Purchase;

class PurchaseObserver
{
    /**
     * Handle the Purchase "saved" event.
     * Se ejecuta después de created y updated, cuando ya se guardaron los items
     */
    public function saved(Purchase $purchase): void
    {
        // Solo actualizar stock si es nueva compra recibida
        if ($purchase->wasRecentlyCreated && $purchase->status === 'received') {
            $this->updateStock($purchase, 'add');
        }
        // O si cambió a received
        elseif ($purchase->wasChanged('status')) {
            $oldStatus = $purchase->getOriginal('status');
            $newStatus = $purchase->status;

            if ($newStatus === 'received' && $oldStatus !== 'received') {
                $this->updateStock($purchase, 'add');
            } elseif ($oldStatus === 'received' && $newStatus !== 'received') {
                $this->updateStock($purchase, 'subtract');
            }
        }
    }

    /**
     * Handle the Purchase "deleted" event.
     * Si se elimina una compra recibida, restar del stock
     */
    public function deleted(Purchase $purchase): void
    {
        if ($purchase->status === 'received') {
            $this->updateStock($purchase, 'subtract');
        }
    }

    /**
     * Actualizar el stock de los productos de la compra
     */
    protected function updateStock(Purchase $purchase, string $operation): void
    {
        foreach ($purchase->items as $item) {
            $product = Product::find($item->product_id);

            if ($product) {
                if ($operation === 'add') {
                    // Sumar al stock
                    $product->increment('stock', $item->quantity);
                } elseif ($operation === 'subtract') {
                    // Restar del stock
                    $product->decrement('stock', $item->quantity);
                }
            }
        }
    }
}
