<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BOQItem extends Model
{
    protected $fillable = [
        'boq_header_id',
        'boq_section_id',
        'item_number',
        'code',
        'description',
        'description_en',
        'unit',
        'quantity',
        'unit_rate',
        'amount',
        'material_cost',
        'labor_cost',
        'equipment_cost',
        'subcontract_cost',
        'overhead_cost',
        'profit_margin',
        'executed_quantity',
        'executed_amount',
        'remaining_quantity',
        'progress_percentage',
        'wbs_id',
        'sort_order',
        'notes',
    ];

    protected $casts = [
        'quantity' => 'decimal:4',
        'unit_rate' => 'decimal:4',
        'amount' => 'decimal:2',
        'material_cost' => 'decimal:4',
        'labor_cost' => 'decimal:4',
        'equipment_cost' => 'decimal:4',
        'subcontract_cost' => 'decimal:4',
        'overhead_cost' => 'decimal:4',
        'profit_margin' => 'decimal:2',
        'executed_quantity' => 'decimal:4',
        'executed_amount' => 'decimal:2',
        'remaining_quantity' => 'decimal:4',
        'progress_percentage' => 'decimal:2',
        'sort_order' => 'integer',
    ];

    public function boqHeader(): BelongsTo
    {
        return $this->belongsTo(BOQHeader::class);
    }

    public function section(): BelongsTo
    {
        return $this->belongsTo(BOQSection::class, 'boq_section_id');
    }

    public function resources(): HasMany
    {
        return $this->hasMany(BOQItemResource::class);
    }

    public function calculateAmount(): void
    {
        $this->amount = $this->quantity * $this->unit_rate;
        $this->remaining_quantity = $this->quantity - $this->executed_quantity;
        if ($this->quantity > 0) {
            $this->progress_percentage = ($this->executed_quantity / $this->quantity) * 100;
        }
        $this->save();
    }
}
