<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CalendarException extends Model
{
    protected $fillable = [
        'schedule_calendar_id',
        'exception_date',
        'exception_type',
        'name',
        'working_hours',
        'is_recurring',
        'recurrence_pattern',
    ];

    protected $casts = [
        'exception_date' => 'date',
        'working_hours' => 'array',
        'is_recurring' => 'boolean',
    ];

    // Relationships
    public function calendar(): BelongsTo
    {
        return $this->belongsTo(ScheduleCalendar::class, 'schedule_calendar_id');
    }
}
