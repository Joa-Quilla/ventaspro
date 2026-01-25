<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sale extends Model
{
    /**
     * Campos asignables masivamente
     */
    protected $fillable = [
        'user_id',
        'customer_id',
        'customer_name',
        'customer_phone',
        'subtotal',
        'tax',
        'total',
        'payment_method',
        'status',
        'notes',
    ];

    /**
     * Conversión automática de tipos
     */
    protected $casts = [
        'subtotal' => 'float',
        'tax' => 'float',
        'total' => 'float',
    ];

    /**
     * RELACIÓN: Una venta pertenece a un usuario (cajero)
     * 
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * RELACIÓN: Una venta pertenece a un cliente
     * 
     * @return BelongsTo
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * RELACIÓN: Una venta tiene muchos items (productos vendidos)
     * 
     * @return HasMany
     */
    public function items(): HasMany
    {
        return $this->hasMany(SaleItem::class);
    }

    /**
     * ACCESOR: Obtener el nombre del cajero
     */
    public function getCashierNameAttribute(): string
    {
        return $this->user->name ?? 'N/A';
    }

    /**
     * ACCESOR: Obtener método de pago en español
     */
    public function getPaymentMethodLabelAttribute(): string
    {
        return match ($this->payment_method) {
            'cash' => 'Efectivo',
            'card' => 'Tarjeta',
            'transfer' => 'Transferencia',
            default => $this->payment_method,
        };
    }

    /**
     * ACCESOR: Obtener estado en español
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'completed' => 'Completada',
            'cancelled' => 'Cancelada',
            default => $this->status,
        };
    }
}
