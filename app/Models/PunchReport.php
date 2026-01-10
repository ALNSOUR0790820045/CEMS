<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PunchReport extends Model
{
    protected $fillable = [
        'report_number',
        'project_id',
        'report_type',
        'report_date',
        'period_from',
        'period_to',
        'filters',
        'generated_path',
        'generated_by_id',
    ];

    protected $casts = [
        'report_date' => 'date',
        'period_from' => 'date',
        'period_to' => 'date',
        'filters' => 'array',
    ];

    // Relationships
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function generatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'generated_by_id');
    }
}
