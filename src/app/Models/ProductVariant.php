<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductVariant extends Model
{
    protected $fillable = [
        'product_id',
        'olsera_id',
        'sku',
        'name',
        'price',
        'sell_price',
        'buy_price',
        'weight',
        'stock',
        'barcode',
        'images',
    ];

    protected $casts = [
        'images' => 'array',
        'price' => 'decimal:2',
        'sell_price' => 'decimal:2',
        'buy_price' => 'decimal:2',
        'weight' => 'decimal:2',
        'stock' => 'integer',
    ];

    /**
     * Get the parent product.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
