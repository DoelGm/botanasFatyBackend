<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable = [
        'name',
        'email',
        'phone',
    ];
    protected $hidden = [
        'created_at',
        'updated_at',
    ];
}
