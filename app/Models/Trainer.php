<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Trainer extends Model
{
    protected $fillable = [
        'name',
        'email',
        'phone',
        'bio',
        'expertise',
        'status',
    ];

    protected $casts = [
        'status' => 'string',
    ];
}
