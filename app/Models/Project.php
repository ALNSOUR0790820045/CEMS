<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'project_number',
        'name',
        'name_en',
        'description',
        'location',
        'company_id',
        'start_date',
        'end_date',
        'status',
        'budget',
        'is_active',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'budget' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function laborers()
    {
        return $this->hasMany(Laborer::class, 'current_project_id');
    }

    public function boqItems()
    {
        return $this->hasMany(BoqItem::class);
    }

    public function laborAssignments()
    {
        return $this->hasMany(LaborAssignment::class);
    }

    public function laborAttendance()
    {
        return $this->hasMany(LaborDailyAttendance::class);
    }

    public function laborProductivity()
    {
        return $this->hasMany(LaborProductivity::class);
    }

    public function laborTimesheets()
    {
        return $this->hasMany(LaborTimesheet::class);
    }

    public function laborCamps()
    {
        return $this->hasMany(LaborCamp::class);
    }
}
