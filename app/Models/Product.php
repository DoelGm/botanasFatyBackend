<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $casts = [
        'imgs' => 'array',
    ];
    protected $fillable = [
        'name',
        'price',
        'description',
    ];
    protected $hidden = [
        'created_at',
        'updated_at',
    ];   
    public function category(){
        return $this->belongsTo(Category::class);
    }
 
}
