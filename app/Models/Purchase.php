<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Purchase extends Model
{
    protected $fillable = [
        'supplier_id',
        'user_id',
        'invoice_number',
        'purchase_date',
        'delivery_date',
        'subtotal',
        'tax',
        'total',
        'status',
        'payment_status',
        'paid_amount',
        'notes',
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'delivery_date' => 'date',
        'subtotal' => 'decimal:2',
        'tax' => 'decimal:2',
        'total' => 'decimal:2',
        'paid_amount' => 'decimal:2',
    ];

    /**
     * RELACIÓN: Una compra pertenece a un proveedor
     */
    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    /**
     * RELACIÓN: Una compra pertenece a un usuario
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * RELACIÓN: Una compra tiene muchos items
     */
    public function items(): HasMany
    {
        return $this->hasMany(PurchaseItem::class);
    }

    /**
     * Saldo pendiente de pago
     */
    public function getPendingAmountAttribute(): float
    {
        return $this->total - $this->paid_amount;
    }
}
