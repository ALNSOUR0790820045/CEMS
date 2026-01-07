<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectMilestone extends Model
{
    protected $fillable = [
        'project_id',
        'phase_id',
        'name',
        'description',
        'target_date',
        'actual_date',
        'type',
        'is_critical',
        'status',
    ];

    protected $casts = [
        'target_date' => 'date',
        'actual_date' => 'date',
        'is_critical' => 'boolean',
    ];

    // Relationships
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function phase()
    {
        return $this->belongsTo(ProjectPhase::class, 'phase_id');
    }
}
