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
        'is_synced',
    ];

    protected $casts = [
        'images' => 'array',
        'is_synced' => 'boolean',
        'price' => 'decimal:2',
    ];
}
