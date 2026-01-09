<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChangeOrderItem extends Model
{
    protected $fillable = [
        'change_order_id',
        'item_code',
        'description',
        'wbs_id',
        'original_quantity',
        'changed_quantity',
        'quantity_difference',
        'unit',
        'unit_price',
        'amount',
        'notes',
    ];

    protected $casts = [
        'original_quantity' => 'decimal:3',
        'changed_quantity' => 'decimal:3',
        'quantity_difference' => 'decimal:3',
        'unit_price' => 'decimal:2',
        'amount' => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($item) {
            $item->calculateQuantityDifference();
            $item->calculateAmount();
        });
    }

    /**
     * Calculate quantity difference
     */
    public function calculateQuantityDifference(): void
    {
        $this->quantity_difference = $this->changed_quantity - $this->original_quantity;
    }

    /**
     * Calculate item amount
     */
    public function calculateAmount(): void
    {
        $this->amount = $this->quantity_difference * $this->unit_price;
    }

    // Relationships
    public function changeOrder(): BelongsTo
    {
        return $this->belongsTo(ChangeOrder::class);
    }

    public function wbs(): BelongsTo
    {
        return $this->belongsTo(ProjectWbs::class, 'wbs_id');
    }
}
