<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BaselineActivity extends Model
{
    protected $fillable = [
        'schedule_baseline_id',
        'schedule_activity_id',
        'planned_start',
        'planned_finish',
        'planned_duration',
        'budgeted_cost',
    ];

    protected $casts = [
        'planned_start' => 'date',
        'planned_finish' => 'date',
        'planned_duration' => 'integer',
        'budgeted_cost' => 'decimal:2',
    ];

    // Relationships
    public function baseline(): BelongsTo
    {
        return $this->belongsTo(ScheduleBaseline::class, 'schedule_baseline_id');
    }

    public function activity(): BelongsTo
    {
        return $this->belongsTo(ScheduleActivity::class, 'schedule_activity_id');
    }
}
