<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $fillable = [
        'title',
        'content',
        'imgs',
    ];

    protected $casts = [
        'imgs' => 'array',
    ];
    protected $hidden = [
        'created_at',
        'updated_at',
    ];
    protected $appends = [
        'imgs',
    ];
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
