<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PriceEscalationContract extends Model
{
    protected $fillable = [
        'project_id',
        'contract_date',
        'contract_amount',
        'formula_type',
        'materials_weight',
        'labor_weight',
        'fixed_portion',
        'base_materials_index',
        'base_labor_index',
        'threshold_percentage',
        'max_escalation_percentage',
        'calculation_frequency',
        'is_active',
        'effective_from',
        'effective_to',
    ];

    protected $casts = [
        'contract_date' => 'date',
        'contract_amount' => 'decimal:2',
        'materials_weight' => 'decimal:2',
        'labor_weight' => 'decimal:2',
        'fixed_portion' => 'decimal:2',
        'base_materials_index' => 'decimal:4',
        'base_labor_index' => 'decimal:4',
        'threshold_percentage' => 'decimal:2',
        'max_escalation_percentage' => 'decimal:2',
        'is_active' => 'boolean',
        'effective_from' => 'date',
        'effective_to' => 'date',
    ];

    // Relationships
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function calculations(): HasMany
    {
        return $this->hasMany(PriceEscalationCalculation::class);
    }

    /**
     * Validate that weights sum to 100%
     */
    public function validateWeights(): bool
    {
        $total = $this->materials_weight + $this->labor_weight + $this->fixed_portion;
        return abs($total - 100) < 0.01; // Allow small floating point differences
    }

    /**
     * Set base indices from DSI for contract date
     */
    public function setBaseIndices(): void
    {
        $baseIndex = DsiIndex::getIndexForDate($this->contract_date);
        
        if ($baseIndex) {
            $this->base_materials_index = $baseIndex->materials_index;
            $this->base_labor_index = $baseIndex->labor_index;
            $this->save();
        }
    }
}
