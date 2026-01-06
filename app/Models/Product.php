<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Product extends Model
{
    protected $fillable = [
        'name',
        'sku',
        'barcode',
        'description',
        'category_id',
        'price',
        'cost',
        'stock',
        'min_stock',
        'is_active',
        'image',
    ];

    protected $casts = [

        'price' => 'float',
        'cost' => 'float',
        'stock' => 'integer',
        'min_stock' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * @return BelongsTo
     */
    public function category(): BelongsTo

    {
        return $this->belongsTo(Category::class);
    }
}
