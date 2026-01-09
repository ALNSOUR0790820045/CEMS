<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LaborAssignment extends Model
{
    protected $fillable = [
        'laborer_id',
        'project_id',
        'assignment_date',
        'expected_end_date',
        'actual_end_date',
        'status',
        'work_scope',
        'assigned_by',
    ];

    protected $casts = [
        'assignment_date' => 'date',
        'expected_end_date' => 'date',
        'actual_end_date' => 'date',
    ];

    public function laborer()
    {
        return $this->belongsTo(Laborer::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function assignedByUser()
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }
}
