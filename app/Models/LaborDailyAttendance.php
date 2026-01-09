<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LaborDailyAttendance extends Model
{
    protected $table = 'labor_daily_attendance';

    protected $fillable = [
        'laborer_id',
        'project_id',
        'attendance_date',
        'time_in',
        'time_out',
        'regular_hours',
        'overtime_hours',
        'total_hours',
        'status',
        'work_area',
        'work_description',
        'recorded_by',
    ];

    protected $casts = [
        'attendance_date' => 'date',
        'regular_hours' => 'decimal:2',
        'overtime_hours' => 'decimal:2',
        'total_hours' => 'decimal:2',
    ];

    public function laborer()
    {
        return $this->belongsTo(Laborer::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function recordedByUser()
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }
}
