<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VariationOrderItem extends Model
{
    protected $fillable = [
        'variation_order_id',
        'boq_item_id',
        'item_number',
        'description',
        'unit',
        'quantity',
        'unit_rate',
        'amount',
        'rate_basis',
        'rate_justification',
        'sort_order',
    ];

    protected $casts = [
        'quantity' => 'decimal:4',
        'unit_rate' => 'decimal:4',
        'amount' => 'decimal:2',
    ];

    public function variationOrder()
    {
        return $this->belongsTo(VariationOrder::class);
    }

    public function boqItem()
    {
        return $this->belongsTo(BoqItem::class);
    }

    // Auto-calculate amount when quantity or unit_rate changes
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($item) {
            $item->amount = $item->quantity * $item->unit_rate;
        });
    }
}
