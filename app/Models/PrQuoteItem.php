<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PrQuoteItem extends Model
{
    protected $fillable = [
        'pr_quote_id',
        'pr_item_id',
        'quantity',
        'unit_price',
        'discount_percentage',
        'total_price',
        'delivery_days',
        'notes',
    ];

    protected $casts = [
        'quantity' => 'decimal:3',
        'unit_price' => 'decimal:2',
        'discount_percentage' => 'decimal:2',
        'total_price' => 'decimal:2',
    ];

    // Relationships
    public function prQuote()
    {
        return $this->belongsTo(PrQuote::class);
    }

    public function prItem()
    {
        return $this->belongsTo(PurchaseRequisitionItem::class, 'pr_item_id');
    }
}
