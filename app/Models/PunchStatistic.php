<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PunchStatistic extends Model
{
    protected $fillable = [
        'project_id',
        'date',
        'total_items',
        'open_items',
        'in_progress_items',
        'completed_items',
        'verified_items',
        'overdue_items',
        'by_discipline',
        'by_severity',
        'by_contractor',
        'avg_resolution_days',
    ];

    protected $casts = [
        'date' => 'date',
        'by_discipline' => 'array',
        'by_severity' => 'array',
        'by_contractor' => 'array',
        'avg_resolution_days' => 'decimal:2',
        'total_items' => 'integer',
        'open_items' => 'integer',
        'in_progress_items' => 'integer',
        'completed_items' => 'integer',
        'verified_items' => 'integer',
        'overdue_items' => 'integer',
    ];

    // Relationships
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }
}
