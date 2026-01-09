<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectActivity extends Model
{
    protected $fillable = [
        'project_id',
        'activity_code',
        'activity_name',
        'planned_start_date',
        'planned_end_date',
        'actual_start_date',
        'actual_end_date',
        'is_critical_path',
    ];

    protected $casts = [
        'planned_start_date' => 'date',
        'planned_end_date' => 'date',
        'actual_start_date' => 'date',
        'actual_end_date' => 'date',
        'is_critical_path' => 'boolean',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }
}
