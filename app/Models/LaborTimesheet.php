<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LaborTimesheet extends Model
{
    protected $fillable = [
        'timesheet_number',
        'project_id',
        'week_start_date',
        'week_end_date',
        'status',
        'total_regular_hours',
        'total_overtime_hours',
        'total_amount',
        'prepared_by',
        'approved_by',
        'approved_at',
        'notes',
    ];

    protected $casts = [
        'week_start_date' => 'date',
        'week_end_date' => 'date',
        'total_regular_hours' => 'decimal:2',
        'total_overtime_hours' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'approved_at' => 'datetime',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function entries()
    {
        return $this->hasMany(LaborTimesheetEntry::class, 'timesheet_id');
    }

    public function preparedByUser()
    {
        return $this->belongsTo(User::class, 'prepared_by');
    }

    public function approvedByUser()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
