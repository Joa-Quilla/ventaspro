<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Supplier extends Model
{
    protected $fillable = [
        'name',
        'company_name',
        'tax_id',
        'email',
        'phone',
        'mobile',
        'address',
        'city',
        'state',
        'zip_code',
        'country',
        'credit_days',
        'current_debt',
        'notes',
        'is_active',
    ];

    protected $casts = [
        'credit_days' => 'integer',
        'current_debt' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    /**
     * RELACIÓN: Un proveedor tiene muchas compras
     */
    public function purchases(): HasMany
    {
        return $this->hasMany(Purchase::class);
    }

    /**
     * Total de compras realizadas al proveedor
     */
    public function getTotalPurchasesAttribute(): float
    {
        return $this->purchases()->sum('total');
    }
}
