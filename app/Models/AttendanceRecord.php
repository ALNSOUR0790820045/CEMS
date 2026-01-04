<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AttendanceRecord extends Model
{
    use HasFactory;
    protected $fillable = [
        'employee_id',
        'attendance_date',
        'check_in_time',
        'check_out_time',
        'work_hours',
        'overtime_hours',
        'late_minutes',
        'early_leave_minutes',
        'status',
        'location',
        'device_id',
        'notes',
        'approved_by_id',
        'company_id',
    ];

    protected $casts = [
        'attendance_date' => 'date',
        'check_in_time' => 'datetime',
        'check_out_time' => 'datetime',
        'work_hours' => 'decimal:2',
        'overtime_hours' => 'decimal:2',
        'late_minutes' => 'integer',
        'early_leave_minutes' => 'integer',
    ];

    // Relationships
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by_id');
    }

    // Scopes
    public function scopeForCompany($query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    public function scopeForDate($query, $date)
    {
        return $query->whereDate('attendance_date', $date);
    }

    public function scopeForEmployee($query, $employeeId)
    {
        return $query->where('employee_id', $employeeId);
    }

    public function scopeForDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('attendance_date', [$startDate, $endDate]);
    }

    // Methods
    public function calculateWorkHours()
    {
        if ($this->check_in_time && $this->check_out_time) {
            $checkIn = Carbon::parse($this->check_in_time);
            $checkOut = Carbon::parse($this->check_out_time);
            $hours = $checkIn->diffInMinutes($checkOut) / 60;
            $this->work_hours = round($hours, 2);
            $this->save();
        }
    }

    public function calculateLateMinutes($shiftStartTime)
    {
        if ($this->check_in_time) {
            $checkIn = Carbon::parse($this->check_in_time);
            $shiftStart = Carbon::parse($this->attendance_date->format('Y-m-d') . ' ' . $shiftStartTime);
            
            if ($checkIn->gt($shiftStart)) {
                $this->late_minutes = $shiftStart->diffInMinutes($checkIn);
            } else {
                $this->late_minutes = 0;
            }
            $this->save();
        }
    }

    public function calculateOvertimeHours($standardHours)
    {
        if ($this->work_hours > $standardHours) {
            $this->overtime_hours = $this->work_hours - $standardHours;
        } else {
            $this->overtime_hours = 0;
        }
        $this->save();
    }
}
