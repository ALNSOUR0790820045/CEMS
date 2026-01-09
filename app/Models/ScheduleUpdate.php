<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ScheduleUpdate extends Model
{
    protected $fillable = [
        'project_schedule_id',
        'update_number',
        'update_date',
        'data_date',
        'narrative',
        'updated_by_id',
        'activities_updated',
        'activities_added',
        'activities_deleted',
        'schedule_variance_days',
        'critical_path_changed',
    ];

    protected $casts = [
        'update_date' => 'date',
        'data_date' => 'date',
        'activities_updated' => 'integer',
        'activities_added' => 'integer',
        'activities_deleted' => 'integer',
        'schedule_variance_days' => 'integer',
        'critical_path_changed' => 'boolean',
    ];

    // Relationships
    public function schedule(): BelongsTo
    {
        return $this->belongsTo(ProjectSchedule::class, 'project_schedule_id');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by_id');
    }
}
