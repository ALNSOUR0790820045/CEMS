<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $fillable = [
        'company_id',
        'project_code',
        'name',
        'name_en',
        'description',
        'start_date',
        'end_date',
        'status',
        'budget',
        'manager_id',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'budget' => 'decimal:2',
    ];

    // Relationships
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function manager()
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    public function wbsItems()
    {
        return $this->hasMany(ProjectWbs::class);
    }

    public function activities()
    {
        return $this->hasMany(ProjectActivity::class);
    }

    public function milestones()
    {
        return $this->hasMany(ProjectMilestone::class);
    }
}
