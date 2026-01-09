<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LaborCamp extends Model
{
    protected $fillable = [
        'camp_number',
        'name',
        'location',
        'project_id',
        'capacity',
        'current_occupancy',
        'supervisor',
        'phone',
        'status',
        'facilities',
    ];

    protected $casts = [
        'capacity' => 'integer',
        'current_occupancy' => 'integer',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
