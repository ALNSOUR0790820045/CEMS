<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Laborer extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'labor_number',
        'name',
        'name_en',
        'category_id',
        'nationality',
        'id_number',
        'id_expiry_date',
        'passport_number',
        'passport_expiry_date',
        'phone',
        'emergency_contact',
        'emergency_phone',
        'employment_type',
        'subcontractor_id',
        'joining_date',
        'contract_end_date',
        'daily_wage',
        'overtime_rate',
        'status',
        'current_project_id',
        'current_location',
        'safety_trained',
        'safety_training_date',
        'safety_training_expiry',
        'medical_checked',
        'medical_check_date',
        'photo',
        'skills',
        'notes',
        'is_active',
    ];

    protected $casts = [
        'id_expiry_date' => 'date',
        'passport_expiry_date' => 'date',
        'joining_date' => 'date',
        'contract_end_date' => 'date',
        'daily_wage' => 'decimal:2',
        'overtime_rate' => 'decimal:2',
        'safety_trained' => 'boolean',
        'safety_training_date' => 'date',
        'safety_training_expiry' => 'date',
        'medical_checked' => 'boolean',
        'medical_check_date' => 'date',
        'is_active' => 'boolean',
    ];

    public function category()
    {
        return $this->belongsTo(LaborCategory::class, 'category_id');
    }

    public function subcontractor()
    {
        return $this->belongsTo(Subcontractor::class);
    }

    public function currentProject()
    {
        return $this->belongsTo(Project::class, 'current_project_id');
    }

    public function assignments()
    {
        return $this->hasMany(LaborAssignment::class);
    }

    public function attendance()
    {
        return $this->hasMany(LaborDailyAttendance::class);
    }

    public function timesheetEntries()
    {
        return $this->hasMany(LaborTimesheetEntry::class);
    }
}
