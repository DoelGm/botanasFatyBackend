<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Ticket extends Model
{
protected $fillable = [
    'subtotal',
];
    public function products()
{
    return $this->belongsToMany(Product::class, 'ticket_product')
                ->withPivot('quantity')
                ->withTimestamps();
                
}
}
