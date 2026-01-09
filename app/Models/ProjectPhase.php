<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectPhase extends Model
{
    protected $fillable = [
        'project_id',
        'name',
        'code',
        'description',
        'planned_start',
        'planned_end',
        'actual_start',
        'actual_end',
        'weight',
        'progress',
        'sort_order',
    ];

    protected $casts = [
        'planned_start' => 'date',
        'planned_end' => 'date',
        'actual_start' => 'date',
        'actual_end' => 'date',
        'weight' => 'decimal:2',
        'progress' => 'decimal:2',
        'sort_order' => 'integer',
    ];

    // Relationships
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function milestones()
    {
        return $this->hasMany(ProjectMilestone::class, 'phase_id');
    }
}
