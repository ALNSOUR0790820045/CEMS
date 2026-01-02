<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseOrderItem extends Model
{
    protected $fillable = [
        'purchase_order_id',
        'product_id',
        'quantity',
        'unit_price',
        'tax_rate',
        'tax_amount',
        'discount',
        'total',
    ];

    protected $casts = [
        'quantity' => 'decimal:3',
        'unit_price' => 'decimal:2',
        'tax_rate' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'discount' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    // Relationships
    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // Auto-calculate totals
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($item) {
            $subtotal = $item->quantity * $item->unit_price - $item->discount;
            $item->tax_amount = $subtotal * ($item->tax_rate / 100);
            $item->total = $subtotal + $item->tax_amount;
        });

        static::saved(function ($item) {
            $item->purchaseOrder->calculateTotals();
        });

        static::deleted(function ($item) {
            $item->purchaseOrder->calculateTotals();
        });
    }
}
