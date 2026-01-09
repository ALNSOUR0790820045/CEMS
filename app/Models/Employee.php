<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Employee extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'company_id',
        'user_id',
        'employee_code',
        'name',
        'name_en',
        'email',
        'phone',
        'job_title',
        'department',
        'hire_date',
        'hourly_rate',
        'overtime_rate',
        'is_active',
    ];

    protected $casts = [
        'hire_date' => 'date',
        'hourly_rate' => 'decimal:2',
        'overtime_rate' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    // Relationships
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function timesheets(): HasMany
    {
        return $this->hasMany(ProjectTimesheet::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByDepartment($query, $department)
    {
        return $query->where('department', $department);
    }

    // Methods
    public function calculateTotalHoursForPeriod($startDate, $endDate)
    {
        return $this->timesheets()
            ->whereBetween('work_date', [$startDate, $endDate])
            ->where('status', 'approved')
            ->sum('total_hours');
    }

    public function calculateTotalCostForPeriod($startDate, $endDate)
    {
        return $this->timesheets()
            ->whereBetween('work_date', [$startDate, $endDate])
            ->where('status', 'approved')
            ->sum('cost');
    }
}
