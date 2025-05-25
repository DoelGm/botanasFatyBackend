<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    protected $fillable = [
        'product_id',
        'name',
        'quantity',
        'total_price',
        'type',
        'category_specific_id',
        'start_date',
        'end_date',
        'package_stock',
        'active',
        'imgs'
    ];

    protected $casts = [
        'imgs' => 'array',
        'start_date' => 'datetime',
        'end_date' => 'datetime'
        
    ];
}
