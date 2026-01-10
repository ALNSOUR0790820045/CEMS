<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectProgressReport extends Model
{
    protected $fillable = [
        'project_id',
        'report_date',
        'report_number',
        'period_type',
        'physical_progress',
        'planned_progress',
        'variance',
        'work_done',
        'planned_work',
        'issues',
        'recommendations',
        'manpower_count',
        'equipment_count',
        'weather',
        'prepared_by',
        'approved_by',
    ];

    protected $casts = [
        'report_date' => 'date',
        'report_number' => 'integer',
        'physical_progress' => 'decimal:2',
        'planned_progress' => 'decimal:2',
        'variance' => 'decimal:2',
        'manpower_count' => 'integer',
        'equipment_count' => 'integer',
    ];

    // Relationships
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function preparedBy()
    {
        return $this->belongsTo(User::class, 'prepared_by');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
