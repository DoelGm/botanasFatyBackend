<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = ['name'];

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    // Si también se relaciona con posts
    public function posts()
    {
        return $this->hasMany(Post::class);
    }
}
