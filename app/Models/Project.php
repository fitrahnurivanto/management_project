<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'client_id',
        'project_name',
        'project_code',
        'pks_number',
        'description',
        'status',
        'status_notes',
        'budget',
        'actual_cost',
        'start_date',
        'end_date',
        'completed_at',
    ];

    protected $casts = [
        'budget' => 'decimal:2',
        'actual_cost' => 'decimal:2',
        'start_date' => 'date',
        'end_date' => 'date',
        'completed_at' => 'datetime',
    ];

    /**
     * Get the order that owns the project.
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get the client that owns the project.
     */
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Get the services for the project.
     * NOTE: Disabled - project_services table removed
     */
    // public function services()
    // {
    //     return $this->belongsToMany(Service::class, 'project_services')
    //         ->withPivot('allocated_budget')
    //         ->withTimestamps();
    // }

    /**
     * Get the teams for the project.
     */
    public function teams()
    {
        return $this->hasMany(Team::class);
    }

    /**
     * Get the tasks for the project.
     */
    public function tasks()
    {
        return $this->hasMany(ProjectTask::class);
    }

    /**
     * Get the milestones for the project.
     */
    public function milestones()
    {
        return $this->hasMany(ProjectMilestone::class);
    }

    /**
     * Get the expenses for the project.
     */
    public function expenses()
    {
        return $this->hasMany(ProjectExpense::class);
    }

    /**
     * Get the time trackings for the project.
     */
    public function timeTrackings()
    {
        return $this->hasMany(TimeTracking::class);
    }

    /**
     * Calculate profit for the project.
     */
    public function getProfitAttribute()
    {
        return $this->budget - $this->actual_cost;
    }

    /**
     * Calculate profit margin percentage.
     */
    public function getProfitMarginAttribute()
    {
        if ($this->budget == 0) return 0;
        return ($this->profit / $this->budget) * 100;
    }

    /**
     * Update actual cost from expenses and time tracking.
     */
    public function updateActualCost()
    {
        $expensesCost = $this->expenses()->sum('amount');
        
        $laborCost = $this->timeTrackings()
            ->join('team_members', function($join) {
                $join->on('time_trackings.user_id', '=', 'team_members.user_id')
                     ->on('time_trackings.project_id', '=', 'team_members.team_id');
            })
            ->selectRaw('SUM(time_trackings.hours * team_members.hourly_rate) as total')
            ->value('total') ?? 0;

        $this->actual_cost = $expensesCost + $laborCost;
        $this->save();
    }

    /**
     * Generate unique project code.
     */
    public static function generateProjectCode()
    {
        $date = now()->format('Ymd');
        $lastProject = self::whereDate('created_at', today())->latest()->first();
        $number = $lastProject ? intval(substr($lastProject->project_code, -4)) + 1 : 1;
        
        return 'PRJ-' . $date . '-' . str_pad($number, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Scope a query to only include active projects.
     */
    public function scopeActive($query)
    {
        return $query->whereIn('status', ['in_progress', 'on_hold']);
    }

    /**
     * Scope a query to only include completed projects.
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }
}
