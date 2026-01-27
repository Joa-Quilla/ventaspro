<?php

namespace App\Observers;

use App\Models\Product;
use App\Models\PurchaseItem;

class PurchaseItemObserver
{
    /**
     * Handle the PurchaseItem "created" event.
     * Actualizar stock cuando se crea un item de compra recibida
     */
    public function created(PurchaseItem $purchaseItem): void
    {
        // Solo actualizar si la compra está en estado "received"
        if ($purchaseItem->purchase->status === 'received') {
            $product = Product::find($purchaseItem->product_id);
            if ($product) {
                // Incrementar stock
                $product->increment('stock', $purchaseItem->quantity);

                // Actualizar precio de compra con el último costo
                $product->update([
                    'purchase_price' => $purchaseItem->unit_cost
                ]);
            }
        }
    }

    /**
     * Handle the PurchaseItem "deleted" event.
     * Restar stock cuando se elimina un item de compra recibida
     */
    public function deleted(PurchaseItem $purchaseItem): void
    {
        // Solo actualizar si la compra está en estado "received"
        if ($purchaseItem->purchase->status === 'received') {
            $product = Product::find($purchaseItem->product_id);
            if ($product) {
                $product->decrement('stock', $purchaseItem->quantity);
            }
        }
    }
}
