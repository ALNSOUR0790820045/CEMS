<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseOrder extends Model
{
    protected $fillable = [
        'order_number',
        'supplier_id',
        'warehouse_id',
        'order_date',
        'expected_date',
        'payment_term_id',
        'status',
        'subtotal',
        'tax_amount',
        'discount',
        'total',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'order_date' => 'date',
        'expected_date' => 'date',
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'discount' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    // Relationships
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function paymentTerm()
    {
        return $this->belongsTo(PaymentTerm::class);
    }

    public function items()
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Auto-generate order number
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($purchaseOrder) {
            if (empty($purchaseOrder->order_number)) {
                $year = date('Y');
                $lastOrder = static::whereYear('created_at', $year)
                    ->orderBy('id', 'desc')
                    ->first();
                
                $number = $lastOrder ? intval(substr($lastOrder->order_number, -4)) + 1 : 1;
                $purchaseOrder->order_number = sprintf('PO-%s-%04d', $year, $number);
            }
        });
    }

    // Calculate totals
    public function calculateTotals()
    {
        $subtotal = 0;
        $taxAmount = 0;

        foreach ($this->items as $item) {
            $itemSubtotal = $item->quantity * $item->unit_price - $item->discount;
            $itemTax = $itemSubtotal * ($item->tax_rate / 100);
            
            $subtotal += $itemSubtotal;
            $taxAmount += $itemTax;
        }

        $this->subtotal = $subtotal;
        $this->tax_amount = $taxAmount;
        $this->total = $subtotal + $taxAmount - $this->discount;
        $this->save();
    }
}
