<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MainIpcItem extends Model
{
    protected $fillable = [
        'main_ipc_id',
        'boq_item_id',
        'wbs_id',
        'item_code',
        'description',
        'unit',
        'contract_quantity',
        'previous_quantity',
        'current_quantity',
        'cumulative_quantity',
        'unit_price',
        'current_amount',
        'cumulative_amount',
        'completion_percent',
        'notes',
    ];

    protected $casts = [
        'contract_quantity' => 'decimal:3',
        'previous_quantity' => 'decimal:3',
        'current_quantity' => 'decimal:3',
        'cumulative_quantity' => 'decimal:3',
        'unit_price' => 'decimal:2',
        'current_amount' => 'decimal:2',
        'cumulative_amount' => 'decimal:2',
        'completion_percent' => 'decimal:2',
    ];

    // Relationships
    public function mainIpc()
    {
        return $this->belongsTo(MainIpc::class);
    }

    public function boqItem()
    {
        return $this->belongsTo(BoqItem::class);
    }

    public function wbs()
    {
        return $this->belongsTo(ProjectWbs::class, 'wbs_id');
    }

    // Calculations
    public function calculateCumulativeQuantity()
    {
        $this->cumulative_quantity = $this->previous_quantity + $this->current_quantity;
    }

    public function calculateCurrentAmount()
    {
        $this->current_amount = $this->current_quantity * $this->unit_price;
    }

    public function calculateCumulativeAmount()
    {
        $this->cumulative_amount = $this->cumulative_quantity * $this->unit_price;
    }

    public function calculateCompletionPercent()
    {
        if ($this->contract_quantity > 0) {
            $this->completion_percent = ($this->cumulative_quantity / $this->contract_quantity) * 100;
        } else {
            $this->completion_percent = 0;
        }
    }

    public function recalculate()
    {
        $this->calculateCumulativeQuantity();
        $this->calculateCurrentAmount();
        $this->calculateCumulativeAmount();
        $this->calculateCompletionPercent();
    }
}
