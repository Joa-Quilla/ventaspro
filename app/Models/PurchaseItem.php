<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PurchaseItem extends Model
{
    protected $fillable = [
        'purchase_id',
        'product_id',
        'quantity',
        'unit_cost',
        'subtotal',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'unit_cost' => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];

    /**
     * RELACIÓN: Un item pertenece a una compra
     */
    public function purchase(): BelongsTo
    {
        return $this->belongsTo(Purchase::class);
    }

    /**
     * RELACIÓN: Un item pertenece a un producto
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
