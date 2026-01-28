<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'name',
        'email',
        'phone',
        'tax_id',
        'address',
        'city',
        'state',
        'zip_code',
        'birth_date',
        'credit_limit',
        'current_balance',
        'loyalty_points',
        'notes',
        'is_active',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'credit_limit' => 'decimal:2',
        'current_balance' => 'decimal:2',
        'loyalty_points' => 'integer',
        'is_active' => 'boolean',
    ];

    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function getAvailableCreditAttribute(): float
    {
        return $this->credit_limit - $this->current_balance;
    }

    public function getTotalPurchasesAttribute(): float
    {
        return $this->sales()->sum('total');
    }

    public function getTotalPaymentsAttribute(): float
    {
        return $this->payments()->sum('amount');
    }
}
