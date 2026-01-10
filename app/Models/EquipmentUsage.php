<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EquipmentUsage extends Model
{
    protected $table = 'equipment_usage';

    protected $fillable = [
        'equipment_id',
        'project_id',
        'assignment_id',
        'usage_date',
        'hours_worked',
        'start_meter',
        'end_meter',
        'fuel_consumed',
        'operator_id',
        'work_description',
        'condition',
        'issues',
        'recorded_by',
    ];

    protected $casts = [
        'usage_date' => 'date',
        'hours_worked' => 'decimal:2',
        'start_meter' => 'decimal:2',
        'end_meter' => 'decimal:2',
        'fuel_consumed' => 'decimal:2',
    ];

    public function equipment(): BelongsTo
    {
        return $this->belongsTo(Equipment::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function assignment(): BelongsTo
    {
        return $this->belongsTo(EquipmentAssignment::class);
    }

    public function operator(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'operator_id');
    }

    public function recordedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }
}
