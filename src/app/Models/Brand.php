<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    protected $fillable = [
        'woocommerce_id',
        'name',
        'slug',
    ];

    /**
     * Disable timestamps if not needed, or keep for auditing.
     */
    public $timestamps = true;
}
