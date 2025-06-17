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

    // Relación: un post tiene muchas imágenes
    public function image()
    {
        return $this->hasOne(PostImage::class);
    }
    
    public function productos()
    {
    return $this->belongsToMany(Producto::class, 'ticket_productos')
                ->withPivot('quantity')
                ->withTimestamps();
}
}
