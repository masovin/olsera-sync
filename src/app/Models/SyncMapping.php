<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SyncMapping extends Model
{
    protected $fillable = [
        'olsera_id',
        'woocommerce_id',
    ];
}
