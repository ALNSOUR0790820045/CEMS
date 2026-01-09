<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ScheduleCalendar extends Model
{
    protected $fillable = [
        'name',
        'name_en',
        'description',
        'is_default',
        'working_days',
        'working_hours',
        'hours_per_day',
        'company_id',
    ];

    protected $casts = [
        'is_default' => 'boolean',
        'working_days' => 'array',
        'working_hours' => 'array',
        'hours_per_day' => 'decimal:2',
    ];

    protected $attributes = [
        'working_days' => '[1,2,3,4,5]',
        'working_hours' => '{"start":"08:00","end":"17:00","break_start":"12:00","break_end":"13:00"}',
    ];

    // Relationships
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function exceptions(): HasMany
    {
        return $this->hasMany(CalendarException::class);
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(ProjectSchedule::class, 'calendar_id');
    }

    public function activities(): HasMany
    {
        return $this->hasMany(ScheduleActivity::class, 'calendar_id');
    }

    // Helper methods
    public function isWorkingDay(int $dayOfWeek): bool
    {
        return in_array($dayOfWeek, $this->working_days ?? []);
    }

    public function hasException($date): bool
    {
        return $this->exceptions()
            ->where('exception_date', $date)
            ->exists();
    }
}
