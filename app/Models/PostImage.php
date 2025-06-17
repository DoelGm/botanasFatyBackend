<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PostImage extends Model
{
    protected $fillable = [
        'post_id',
        'cloudinary_url',
        'cloudinary_public_id',
    ];

    // Relación: la imagen pertenece a un post
    public function post()
    {
        return $this->belongsTo(Post::class);
    }
}
