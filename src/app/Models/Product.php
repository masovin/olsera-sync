<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'olsera_id',
        'sku',
        'name',
        'description',
        'price',
        'stock',
        'images',
        'has_variants',
        'is_synced',
        'brand'
    ];

    protected $casts = [
        'images' => 'array',
        'has_variants' => 'boolean',
        'is_synced' => 'boolean',
        'price' => 'decimal:2',
    ];

    /**
     * Get the variations for the product.
     */
    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }
}
