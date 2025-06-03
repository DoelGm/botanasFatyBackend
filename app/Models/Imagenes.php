<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Imagenes extends Model
{
    protected $fillable = [
        'link',
        'deletehash',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];
    public function post()
    {
        return $this->belongsTo(Post::class, 'img_id');
    }
}
