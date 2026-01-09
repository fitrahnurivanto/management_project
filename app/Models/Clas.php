<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Clas extends Model
{
    protected $table = 'clas';

    protected $fillable = [
        'user_id',
        'name',
        'slug',
        'price',
        'amount',
        'cost',
        'meet',
        'duration',
        'method',
        'start_date',
        'end_date',
        'trainer',
        'income',
        'description',
        'status',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'cost' => 'decimal:2',
        'income' => 'decimal:2',
        'amount' => 'integer',
        'meet' => 'integer',
        'duration' => 'integer',
        'start_date' => 'date',
        'end_date' => 'date',
    ];
}

