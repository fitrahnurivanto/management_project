<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'division',
        'phone',
        'address',
        'avatar',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the client profile for the user.
     */
    public function client()
    {
        return $this->hasOne(Client::class);
    }

    /**
     * Get the team memberships for the user.
     */
    public function teamMemberships()
    {
        return $this->hasMany(TeamMember::class);
    }

    /**
     * Get the teams the user belongs to.
     */
    public function teams()
    {
        return $this->belongsToMany(Team::class, 'team_members')
            ->withPivot('role', 'hourly_rate', 'assigned_at')
            ->withTimestamps();
    }

    /**
     * Get the tasks assigned to the user.
     */
    public function tasks()
    {
        return $this->hasMany(ProjectTask::class, 'assigned_to');
    }

    /**
     * Get the time trackings for the user.
     */
    public function timeTrackings()
    {
        return $this->hasMany(TimeTracking::class);
    }

    /**
     * Get the activity logs for the user.
     */
    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class);
    }

    /**
     * Check if user is admin (any type of admin).
     */
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    /**
     * Check if user is super admin.
     */
    public function isSuperAdmin()
    {
        return $this->role === 'admin' && $this->division === null;
    }

    /**
     * Check if user is agency admin.
     */
    public function isAgencyAdmin()
    {
        return $this->role === 'admin' && $this->division === 'agency';
    }

    /**
     * Check if user is academy admin.
     */
    public function isAcademyAdmin()
    {
        return $this->role === 'admin' && $this->division === 'academy';
    }

    /**
     * Check if user can access agency data.
     */
    public function canAccessAgency()
    {
        return $this->isSuperAdmin() || $this->isAgencyAdmin();
    }

    /**
     * Check if user can access academy data.
     */
    public function canAccessAcademy()
    {
        return $this->isSuperAdmin() || $this->isAcademyAdmin();
    }

    /**
     * Get user's division or return 'both' for super admin.
     */
    public function getDivision()
    {
        return $this->division ?? 'both';
    }

    /**
     * Check if user is client.
     */
    public function isClient()
    {
        return $this->role === 'client';
    }

    /**
     * Check if user is employee.
     */
    public function isEmployee()
    {
        return $this->role === 'employee';
    }

    /**
     * Check if user is finance.
     */
    public function isFinance()
    {
        return $this->role === 'finance';
    }

    /**
     * Get user notifications.
     */
    public function notifications()
    {
        return $this->hasMany(\App\Models\Notification::class);
    }

    /**
     * Get notification settings.
     */
    public function notificationSettings()
    {
        return $this->hasMany(\App\Models\NotificationSetting::class);
    }

    /**
     * Get unread notifications count.
     */
    public function unreadNotificationsCount()
    {
        return $this->notifications()->unread()->count();
    }
}

