<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentRequest extends Model
{
    protected $fillable = [
        'user_id',
        'project_id',
        'class_id',
        'requested_amount',
        'approved_amount',
        'hours_worked',
        'status',
        'notes',
        'admin_notes',
        'approved_by',
        'approved_at',
        'paid_at',
        'paid_by',
        'payment_method',
        'payment_reference',
    ];

    protected $casts = [
        'requested_amount' => 'decimal:2',
        'approved_amount' => 'decimal:2',
        'hours_worked' => 'decimal:2',
        'approved_at' => 'datetime',
        'paid_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function clas()
    {
        return $this->belongsTo(Clas::class, 'class_id');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function payer()
    {
        return $this->belongsTo(User::class, 'paid_by');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeProcessing($query)
    {
        return $query->where('status', 'processing');
    }

    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    public function scopeApprovedOrProcessing($query)
    {
        return $query->whereIn('status', ['approved', 'processing']);
    }

    public function isPaid()
    {
        return $this->status === 'paid';
    }

    public function isApproved()
    {
        return $this->status === 'approved';
    }

    public function isPending()
    {
        return $this->status === 'pending';
    }
}

