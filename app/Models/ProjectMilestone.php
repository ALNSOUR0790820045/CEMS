<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectMilestone extends Model
{
    protected $fillable = [
        'project_id',
        'activity_id',
        'name',
        'description',
        'target_date',
        'actual_date',
        'status',
        'type',
        'is_critical',
        'deliverables',
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

    public function activity()
    {
        return $this->belongsTo(ProjectActivity::class, 'activity_id');
    }

    // Accessor for status color
    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'pending' => '#ff9500',
            'achieved' => '#34c759',
            'missed' => '#ff3b30',
            default => '#86868b',
        };
    }

    // Accessor for status label
    public function getStatusLabelAttribute()
    {
        return match($this->status) {
            'pending' => 'قيد الانتظار',
            'achieved' => 'تم التحقيق',
            'missed' => 'فات الموعد',
            default => $this->status,
        };
    }

    // Accessor for type label
    public function getTypeLabelAttribute()
    {
        return match($this->type) {
            'project' => 'مشروع',
            'contractual' => 'تعاقدي',
            'payment' => 'دفع',
            'technical' => 'تقني',
            default => $this->type,
        };
    }
}
