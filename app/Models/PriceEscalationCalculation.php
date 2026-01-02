<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PriceEscalationCalculation extends Model
{
    protected $fillable = [
        'price_escalation_contract_id',
        'main_ipc_id',
        'calculation_number',
        'calculation_date',
        'period_from',
        'period_to',
        'base_materials_index',
        'base_labor_index',
        'current_materials_index',
        'current_labor_index',
        'materials_change_percent',
        'labor_change_percent',
        'escalation_percentage',
        'ipc_amount',
        'escalation_amount',
        'threshold_met',
        'applied',
        'status',
        'approved_by',
        'approved_at',
        'notes',
    ];

    protected $casts = [
        'calculation_date' => 'date',
        'period_from' => 'date',
        'period_to' => 'date',
        'base_materials_index' => 'decimal:4',
        'base_labor_index' => 'decimal:4',
        'current_materials_index' => 'decimal:4',
        'current_labor_index' => 'decimal:4',
        'materials_change_percent' => 'decimal:2',
        'labor_change_percent' => 'decimal:2',
        'escalation_percentage' => 'decimal:2',
        'ipc_amount' => 'decimal:2',
        'escalation_amount' => 'decimal:2',
        'threshold_met' => 'boolean',
        'applied' => 'boolean',
        'approved_at' => 'datetime',
    ];

    // Relationships
    public function contract(): BelongsTo
    {
        return $this->belongsTo(PriceEscalationContract::class, 'price_escalation_contract_id');
    }

    public function ipc(): BelongsTo
    {
        return $this->belongsTo(MainIpc::class, 'main_ipc_id');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Calculate escalation for an IPC
     */
    public static function calculateForIpc(PriceEscalationContract $contract, MainIpc $ipc, $currentDate = null): array
    {
        $currentDate = $currentDate ?? now();
        
        // Get base indices from contract
        $L0 = $contract->base_materials_index;
        $P0 = $contract->base_labor_index;
        
        // Get current indices
        $currentIndices = DsiIndex::getIndexForDate($currentDate);
        
        if (!$currentIndices) {
            throw new \Exception('DSI indices not found for date: ' . $currentDate);
        }
        
        $L1 = $currentIndices->materials_index;
        $P1 = $currentIndices->labor_index;
        
        // Calculate changes
        $deltaL = (($L1 - $L0) / $L0) * 100;
        $deltaP = (($P1 - $P0) / $P0) * 100;
        
        // Apply formula: E = (A × ΔL) + (B × ΔP)
        $A = $contract->materials_weight / 100;
        $B = $contract->labor_weight / 100;
        
        $E = ($A * $deltaL) + ($B * $deltaP);
        
        // Check threshold
        $thresholdMet = $E >= $contract->threshold_percentage;
        
        // Apply max if set
        if ($contract->max_escalation_percentage && $E > $contract->max_escalation_percentage) {
            $E = $contract->max_escalation_percentage;
        }
        
        // Calculate amount
        $escalationAmount = $thresholdMet ? ($ipc->amount * ($E / 100)) : 0;
        
        return [
            'base_materials_index' => $L0,
            'base_labor_index' => $P0,
            'current_materials_index' => $L1,
            'current_labor_index' => $P1,
            'materials_change_percent' => round($deltaL, 2),
            'labor_change_percent' => round($deltaP, 2),
            'escalation_percentage' => round($E, 2),
            'ipc_amount' => $ipc->amount,
            'escalation_amount' => round($escalationAmount, 2),
            'threshold_met' => $thresholdMet,
            'applied' => $thresholdMet,
        ];
    }

    /**
     * Generate calculation number
     */
    public static function generateCalculationNumber(): string
    {
        $year = now()->year;
        $lastCalc = self::where('calculation_number', 'like', "PE-{$year}-%")
            ->orderByDesc('id')
            ->first();
        
        if ($lastCalc) {
            $lastNumber = (int) substr($lastCalc->calculation_number, -3);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }
        
        return sprintf('PE-%d-%03d', $year, $newNumber);
    }
}
