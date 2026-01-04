<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseOrderItem extends Model
{
    protected $fillable = [
        'purchase_order_id',
        'material_id',
        'description',
        'quantity',
        'unit_id',
        'unit_price',
        'tax_rate',
        'discount_rate',
        'line_total',
        'received_quantity',
        'remaining_quantity',
        'delivery_date',
        'specifications',
    ];

    protected $casts = [
        'delivery_date' => 'date',
        'quantity' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'tax_rate' => 'decimal:2',
        'discount_rate' => 'decimal:2',
        'line_total' => 'decimal:2',
        'received_quantity' => 'decimal:2',
        'remaining_quantity' => 'decimal:2',
    ];

    // Relationships
    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function material()
    {
        return $this->belongsTo(Material::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    // Methods
    public function calculateLineTotal()
    {
        $baseAmount = $this->quantity * $this->unit_price;
        $discountAmount = $baseAmount * ($this->discount_rate / 100);
        $afterDiscount = $baseAmount - $discountAmount;
        $taxAmount = $afterDiscount * ($this->tax_rate / 100);
        $this->line_total = $afterDiscount + $taxAmount;
        $this->remaining_quantity = $this->quantity - $this->received_quantity;
        $this->save();
    }

    protected static function booted()
    {
        static::saving(function ($item) {
            $baseAmount = $item->quantity * $item->unit_price;
            $discountAmount = $baseAmount * ($item->discount_rate / 100);
            $afterDiscount = $baseAmount - $discountAmount;
            $taxAmount = $afterDiscount * ($item->tax_rate / 100);
            $item->line_total = $afterDiscount + $taxAmount;
            $item->remaining_quantity = $item->quantity - $item->received_quantity;
        });

        static::saved(function ($item) {
            $item->purchaseOrder->calculateTotals();
        });

        static::deleted(function ($item) {
            $item->purchaseOrder->calculateTotals();
        });
    }
}
