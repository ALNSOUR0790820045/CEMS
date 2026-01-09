<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LaborTimesheetEntry extends Model
{
    protected $fillable = [
        'timesheet_id',
        'laborer_id',
        'daily_hours',
        'total_regular_hours',
        'total_overtime_hours',
        'daily_rate',
        'overtime_rate',
        'total_amount',
    ];

    protected $casts = [
        'daily_hours' => 'array',
        'total_regular_hours' => 'decimal:2',
        'total_overtime_hours' => 'decimal:2',
        'daily_rate' => 'decimal:2',
        'overtime_rate' => 'decimal:2',
        'total_amount' => 'decimal:2',
    ];

    public function timesheet()
    {
        return $this->belongsTo(LaborTimesheet::class, 'timesheet_id');
    }

    public function laborer()
    {
        return $this->belongsTo(Laborer::class);
    }
}
