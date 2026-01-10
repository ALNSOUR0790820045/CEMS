<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DiaryEquipment extends Model
{
    protected $table = 'diary_equipment';

    protected $fillable = [
        'site_diary_id',
        'equipment_type',
        'quantity',
        'hours_worked',
        'hours_idle',
        'idle_reason',
        'fuel_consumed',
        'operator_name',
        'status',
        'notes',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'hours_worked' => 'decimal:2',
        'hours_idle' => 'decimal:2',
        'fuel_consumed' => 'decimal:2',
    ];

    // Relationships
    public function siteDiary(): BelongsTo
    {
        return $this->belongsTo(SiteDiary::class);
    }

    // Accessors
    public function getTotalHoursAttribute(): float
    {
        return $this->hours_worked + $this->hours_idle;
    }

    public function getUtilizationRateAttribute(): float
    {
        $total = $this->total_hours;
        return $total > 0 ? ($this->hours_worked / $total) * 100 : 0;
    }
}
