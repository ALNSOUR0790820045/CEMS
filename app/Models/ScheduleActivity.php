<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ScheduleActivity extends Model
{
    protected $fillable = [
        'project_schedule_id',
        'activity_code',
        'wbs_id',
        'name',
        'name_en',
        'description',
        'activity_type',
        'parent_id',
        'level',
        'planned_start',
        'planned_finish',
        'planned_duration',
        'actual_start',
        'actual_finish',
        'actual_duration',
        'remaining_duration',
        'percent_complete',
        'early_start',
        'early_finish',
        'late_start',
        'late_finish',
        'total_float',
        'free_float',
        'is_critical',
        'constraint_type',
        'constraint_date',
        'calendar_id',
        'responsible_id',
        'cost_account_id',
        'budgeted_cost',
        'actual_cost',
        'earned_value',
        'status',
        'notes',
        'sort_order',
    ];

    protected $casts = [
        'planned_start' => 'date',
        'planned_finish' => 'date',
        'actual_start' => 'date',
        'actual_finish' => 'date',
        'early_start' => 'date',
        'early_finish' => 'date',
        'late_start' => 'date',
        'late_finish' => 'date',
        'constraint_date' => 'date',
        'planned_duration' => 'integer',
        'actual_duration' => 'integer',
        'remaining_duration' => 'integer',
        'total_float' => 'integer',
        'free_float' => 'integer',
        'sort_order' => 'integer',
        'level' => 'integer',
        'percent_complete' => 'decimal:2',
        'budgeted_cost' => 'decimal:2',
        'actual_cost' => 'decimal:2',
        'earned_value' => 'decimal:2',
        'is_critical' => 'boolean',
    ];

    // Relationships
    public function schedule(): BelongsTo
    {
        return $this->belongsTo(ProjectSchedule::class, 'project_schedule_id');
    }

    public function wbs(): BelongsTo
    {
        return $this->belongsTo(ProjectWbs::class, 'wbs_id');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(ScheduleActivity::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(ScheduleActivity::class, 'parent_id');
    }

    public function calendar(): BelongsTo
    {
        return $this->belongsTo(ScheduleCalendar::class, 'calendar_id');
    }

    public function responsible(): BelongsTo
    {
        return $this->belongsTo(User::class, 'responsible_id');
    }

    public function costAccount(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'cost_account_id');
    }

    public function predecessors(): HasMany
    {
        return $this->hasMany(ActivityDependency::class, 'successor_id');
    }

    public function successors(): HasMany
    {
        return $this->hasMany(ActivityDependency::class, 'predecessor_id');
    }

    public function resources(): HasMany
    {
        return $this->hasMany(ScheduleResource::class);
    }

    public function baselineActivities(): HasMany
    {
        return $this->hasMany(BaselineActivity::class);
    }
}
