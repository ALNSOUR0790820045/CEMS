<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BOQItemResource extends Model
{
    protected $fillable = [
        'boq_item_id',
        'resource_type',
        'resource_id',
        'resource_name',
        'unit',
        'quantity_per_unit',
        'unit_cost',
        'total_cost',
        'wastage_percentage',
        'notes',
    ];

    protected $casts = [
        'quantity_per_unit' => 'decimal:6',
        'unit_cost' => 'decimal:4',
        'total_cost' => 'decimal:2',
        'wastage_percentage' => 'decimal:2',
    ];

    public function boqItem(): BelongsTo
    {
        return $this->belongsTo(BOQItem::class);
    }
}
