<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EquipmentFuelLog extends Model
{
    protected $fillable = [
        'equipment_id',
        'project_id',
        'fill_date',
        'quantity',
        'unit_price',
        'total_cost',
        'meter_reading',
        'supplier',
        'receipt_number',
        'recorded_by',
    ];

    protected $casts = [
        'fill_date' => 'date',
        'quantity' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'total_cost' => 'decimal:2',
        'meter_reading' => 'decimal:2',
    ];

    public function equipment(): BelongsTo
    {
        return $this->belongsTo(Equipment::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function recordedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }
}
