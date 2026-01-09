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
        'wbs_id',
        'activity_code',
        'activity_name',
        'name',
        'name_en',
        'description',
        'quantity',
        'unit_id',
        'unit_price',
        'parent_activity_id',
        'sequence_order',
        'duration_days',
        'effort_hours',
        'start_date',
        'end_date',
        'planned_start_date',
        'planned_end_date',
        'early_start',
        'early_finish',
        'late_start',
        'late_finish',
        'total_float',
        'free_float',
        'is_critical',
        'completion_percentage',
        'type',
        'priority',
        'estimated_cost',
        'sort_order',
        'status',
        'is_active',
        'company_id',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_critical' => 'boolean',
        'quantity' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'completion_percentage' => 'decimal: 2',
        'effort_hours' => 'decimal: 2',
        'estimated_cost' => 'decimal: 2',
        'start_date' => 'date',
        'end_date' => 'date',
        'planned_start_date' => 'date',
        'planned_end_date' => 'date',
        'duration_days' => 'integer',
        'early_start' => 'integer',
        'early_finish' => 'integer',
        'late_start' => 'integer',
        'late_finish' => 'integer',
        'total_float' => 'integer',
        'free_float' => 'integer',
        'sort_order' => 'integer',
        'sequence_order' => 'integer',
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

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(TenderActivity::class, 'parent_activity_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(TenderActivity::class, 'parent_activity_id');
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

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}
