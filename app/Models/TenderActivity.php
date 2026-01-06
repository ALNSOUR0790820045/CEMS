<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TenderActivity extends Model
{
    protected $fillable = [
        'tender_id',
        'tender_wbs_id',
        'activity_code',
        'name',
        'name_en',
        'description',
        'duration_days',
        'effort_hours',
        'planned_start_date',
        'planned_end_date',
        'early_start',
        'early_finish',
        'late_start',
        'late_finish',
        'total_float',
        'free_float',
        'is_critical',
        'type',
        'priority',
        'estimated_cost',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_critical' => 'boolean',
        'planned_start_date' => 'date',
        'planned_end_date' => 'date',
        'duration_days' => 'integer',
        'effort_hours' => 'decimal:2',
        'early_start' => 'integer',
        'early_finish' => 'integer',
        'late_start' => 'integer',
        'late_finish' => 'integer',
        'total_float' => 'integer',
        'free_float' => 'integer',
        'estimated_cost' => 'decimal:2',
        'sort_order' => 'integer',
    ];

    // Relationships
    public function tender(): BelongsTo
    {
        return $this->belongsTo(Tender::class);
    }

    public function wbs(): BelongsTo
    {
        return $this->belongsTo(TenderWBS::class, 'tender_wbs_id');
    }

    public function predecessors(): HasMany
    {
        return $this->hasMany(TenderActivityDependency::class, 'successor_id');
    }

    public function successors(): HasMany
    {
        return $this->hasMany(TenderActivityDependency::class, 'predecessor_id');
    }

    public function milestone(): HasMany
    {
        return $this->hasMany(TenderMilestone::class);
    }
}
