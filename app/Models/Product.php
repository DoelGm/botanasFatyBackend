<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $casts = [
        'price' => 'decimal:2',
        'discount' => 'decimal:2',
        'category_id' => 'integer',
        'stock' => 'integer',
    ];
    protected $fillable = [
        'name',
        'price',
        'description',
        'category_id',
        'stock',
        'discount',
    ];
    protected $hidden = [
        'created_at',
        'updated_at',
    ];
    public function category(){
        return $this->belongsTo(Category::class);
    }

    public function images()
{
    return $this->hasMany(ProductImage::class);
}

}
