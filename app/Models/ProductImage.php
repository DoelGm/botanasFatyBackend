<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductImage extends Model
{
    // App\Models\ProductImage.php
    protected $fillable = ['product_id', 'cloudinary_url', 'cloudinary_public_id', "position"];
    protected $casts = [
        'position' => 'integer',
    ];

    public function product()
    {
    return $this->belongsTo(Product::class);
    }

}
