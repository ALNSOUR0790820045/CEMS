<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'company_id',
        'status',
        'start_date',
        'end_date',
        'planned_value',
        'earned_value',
        'actual_cost',
        'budget',
        'progress',
        'client_name',
        'location',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'planned_value' => 'decimal:2',
        'earned_value' => 'decimal:2',
        'actual_cost' => 'decimal:2',
        'budget' => 'decimal:2',
        'progress' => 'integer',
    ];

    // Relationships
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    // Earned Value Management calculations
    public function getSpiAttribute()
    {
        if ($this->planned_value == 0) return 0;
        return round($this->earned_value / $this->planned_value, 2);
    }

    public function getCpiAttribute()
    {
        if ($this->actual_cost == 0) return 0;
        return round($this->earned_value / $this->actual_cost, 2);
    }

    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'active' => 'green',
            'completed' => 'blue',
            'on_hold' => 'yellow',
            'delayed' => 'red',
            default => 'gray',
        };
    }
}
