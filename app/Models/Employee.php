<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Employee extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'employee_number',
        'position',
        'phone',
    ];

    public function assignedEquipment(): HasMany
    {
        return $this->hasMany(Equipment::class, 'assigned_operator_id');
    }

    public function equipmentAssignments(): HasMany
    {
        return $this->hasMany(EquipmentAssignment::class, 'operator_id');
    }
}
