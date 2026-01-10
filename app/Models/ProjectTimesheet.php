<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectTimesheet extends Model
{
    protected $fillable = [
        'project_id',
        'activity_id',
        'employee_id',
        'work_date',
        'regular_hours',
        'overtime_hours',
        'total_hours',
        'work_description',
        'progress_achieved',
        'cost',
        'approved_by',
        'approved_at',
        'status',
        'notes',
    ];

    protected $casts = [
        'work_date' => 'date',
        'regular_hours' => 'decimal:2',
        'overtime_hours' => 'decimal:2',
        'total_hours' => 'decimal:2',
        'progress_achieved' => 'decimal:2',
        'cost' => 'decimal:2',
        'approved_at' => 'datetime',
    ];

    // Relationships
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function activity(): BelongsTo
    {
        return $this->belongsTo(ProjectActivity::class, 'activity_id');
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // Scopes
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'submitted');
    }

    public function scopeForPeriod($query, $startDate, $endDate)
    {
        return $query->whereBetween('work_date', [$startDate, $endDate]);
    }

    // Mutators
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($timesheet) {
            // Auto-calculate total hours
            $timesheet->total_hours = $timesheet->regular_hours + $timesheet->overtime_hours;
            
            // Auto-calculate cost if employee is loaded
            if ($timesheet->employee) {
                $regularCost = $timesheet->regular_hours * $timesheet->employee->hourly_rate;
                $overtimeCost = $timesheet->overtime_hours * $timesheet->employee->overtime_rate;
                $timesheet->cost = $regularCost + $overtimeCost;
            }
        });
    }

    // Methods
    public function approve($userId)
    {
        $this->status = 'approved';
        $this->approved_by = $userId;
        $this->approved_at = now();
        $this->save();
    }

    public function reject()
    {
        $this->status = 'rejected';
        $this->approved_by = null;
        $this->approved_at = null;
        $this->save();
    }
}
