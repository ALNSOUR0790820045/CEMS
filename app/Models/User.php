<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'avatar',
        'job_title',
        'employee_id',
        'is_active',
        'last_login_at',
        'language',
        'company_id',
        'branch_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    // Relationships
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function employee()
    {
        return $this->hasOne(Employee:: class);
    }

    public function attendanceRecords()
    {
        return $this->hasManyThrough(AttendanceRecord::class, Employee:: class);
    }

    public function leaveRequests()
    {
        return $this->hasManyThrough(LeaveRequest::class, Employee::class);
    }

    // Accessors
    public function getInitialsAttribute()
    {
        $words = explode(' ', $this->name);
        if (count($words) >= 2) {
            return mb_substr($words[0], 0, 1).mb_substr($words[1], 0, 1);
        }

        return mb_substr($this->name, 0, 1);
    }

    // Methods
    public function updateLastLogin()
    {
        $this->update(['last_login_at' => now()]);
    }
}