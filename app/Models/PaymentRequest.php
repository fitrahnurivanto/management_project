<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentRequest extends Model
{
    protected $fillable = [
        'user_id',
        'project_id',
        'requested_amount',
        'approved_amount',
        'hours_worked',
        'status',
        'notes',
        'admin_notes',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'requested_amount' => 'decimal:2',
        'approved_amount' => 'decimal:2',
        'hours_worked' => 'decimal:2',
        'approved_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }
}
