<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProjectSchedule extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'schedule_number',
        'project_id',
        'name',
        'description',
        'schedule_type',
        'version',
        'start_date',
        'end_date',
        'duration_days',
        'working_days_per_week',
        'hours_per_day',
        'calendar_id',
        'data_date',
        'status',
        'prepared_by_id',
        'approved_by_id',
        'approved_at',
        'company_id',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'data_date' => 'date',
        'approved_at' => 'datetime',
        'version' => 'integer',
        'duration_days' => 'integer',
        'working_days_per_week' => 'integer',
        'hours_per_day' => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($schedule) {
            if (empty($schedule->schedule_number)) {
                $schedule->schedule_number = self::generateScheduleNumber();
            }
        });
    }

    public static function generateScheduleNumber(): string
    {
        $year = date('Y');
        $prefix = "SCH-{$year}-";
        
        $lastSchedule = self::where('schedule_number', 'like', "{$prefix}%")
            ->orderBy('schedule_number', 'desc')
            ->first();

        if ($lastSchedule) {
            $lastNumber = (int) substr($lastSchedule->schedule_number, -4);
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }

        return $prefix . $newNumber;
    }

    // Relationships
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function calendar(): BelongsTo
    {
        return $this->belongsTo(ScheduleCalendar::class, 'calendar_id');
    }

    public function preparedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'prepared_by_id');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by_id');
    }

    public function activities(): HasMany
    {
        return $this->hasMany(ScheduleActivity::class);
    }

    public function dependencies(): HasMany
    {
        return $this->hasMany(ActivityDependency::class);
    }

    public function baselines(): HasMany
    {
        return $this->hasMany(ScheduleBaseline::class);
    }

    public function updates(): HasMany
    {
        return $this->hasMany(ScheduleUpdate::class);
    }
}
