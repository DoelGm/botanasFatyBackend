<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $casts = [
        'price' => 'decimal:2',
        'discount' => 'decimal:2',
        'category_id' => 'integer',
    ];
    protected $fillable = [
        'name',
        'price',
        'description',
        'category_id',
        'discount',
    ];
    protected $hidden = [
        'created_at',
        'updated_at',
    ];   
    public function category(){
        return $this->belongsTo(Category::class);
    }
     protected $appends = ['image_urls']; 
 // Dentro del modelo Product.php
    public function getImageUrlsAttribute()
    {
    $urls = [];

    for ($i = 1; $i <= 3; $i++) {
        $filename = "product_{$this->id}_{$i}.webp";
        $path = storage_path("app/images/{$filename}");

        if (file_exists($path)) {
            $urls[] = url("/api/image/product/{$this->id}_{$i}");
        }
    }

    return $urls;
}

}
