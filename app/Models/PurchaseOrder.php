<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PurchaseOrder extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'project_id',
        'supplier_id',
        'warehouse_id',
        'order_number',
        'po_number',
        'order_date',
        'expected_date',
        'expected_delivery_date',
        'actual_delivery_date',
        'payment_term_id',
        'status',
        'subtotal',
        'tax_amount',
        'discount',
        'total',
        'total_amount',
        'payment_terms',
        'terms_conditions',
        'notes',
        'created_by',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'order_date' => 'date',
        'expected_date' => 'date',
        'expected_delivery_date' => 'date',
        'actual_delivery_date' => 'date',
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'discount' => 'decimal:2',
        'total' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'approved_at' => 'datetime',
    ];

    // Relationships
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function paymentTerm(): BelongsTo
    {
        return $this->belongsTo(PaymentTerm::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }

    public function siteReceipts(): HasMany
    {
        return $this->hasMany(SiteReceipt::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
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
        $this->total_amount = $this->total;
        $this->save();
    }
}