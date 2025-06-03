<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $fillable = [
        'title',
        'content',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];
    public function imagenes()
    {
        return $this->belongsto(Imagenes::class);
    }

    
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
