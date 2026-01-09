<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ScheduleResource extends Model
{
    protected $fillable = [
        'schedule_activity_id',
        'resource_type',
        'resource_id',
        'resource_name',
        'planned_units',
        'actual_units',
        'remaining_units',
        'unit_type',
        'rate',
        'planned_cost',
        'actual_cost',
    ];

    protected $casts = [
        'planned_units' => 'decimal:2',
        'actual_units' => 'decimal:2',
        'remaining_units' => 'decimal:2',
        'rate' => 'decimal:2',
        'planned_cost' => 'decimal:2',
        'actual_cost' => 'decimal:2',
    ];

    // Relationships
    public function activity(): BelongsTo
    {
        return $this->belongsTo(ScheduleActivity::class, 'schedule_activity_id');
    }

    // Polymorphic resource relationship
    public function resource()
    {
        // This can be extended to support actual resource models
        return null;
    }
}
