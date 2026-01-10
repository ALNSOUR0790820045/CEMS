<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EquipmentMaintenance extends Model
{
    protected $table = 'equipment_maintenance';

    protected $fillable = [
        'equipment_id',
        'maintenance_number',
        'type',
        'scheduled_date',
        'actual_date',
        'description',
        'work_done',
        'labor_cost',
        'parts_cost',
        'total_cost',
        'service_provider',
        'invoice_number',
        'meter_reading',
        'status',
        'performed_by',
        'notes',
    ];

    protected $casts = [
        'scheduled_date' => 'date',
        'actual_date' => 'date',
        'labor_cost' => 'decimal:2',
        'parts_cost' => 'decimal:2',
        'total_cost' => 'decimal:2',
        'meter_reading' => 'decimal:2',
    ];

    public function equipment(): BelongsTo
    {
        return $this->belongsTo(Equipment::class);
    }

    public function performedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'performed_by');
    }
}
