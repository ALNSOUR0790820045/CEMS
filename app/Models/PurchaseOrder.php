<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseOrder extends Model
{
    protected $fillable = [
        'po_number',
        'po_date',
        'vendor_id',
        'purchase_requisition_id',
        'project_id',
        'delivery_date',
        'delivery_location',
        'payment_terms',
        'currency_id',
        'exchange_rate',
        'subtotal',
        'tax_amount',
        'discount_amount',
        'total_amount',
        'status',
        'approved_by_id',
        'approved_at',
        'terms_conditions',
        'notes',
        'company_id',
        'created_by_id',
    ];

    protected $casts = [
        'po_date' => 'date',
        'delivery_date' => 'date',
        'approved_at' => 'datetime',
        'exchange_rate' => 'decimal:4',
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
    ];

    // Relationships
    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function purchaseRequisition()
    {
        return $this->belongsTo(PurchaseRequisition::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by_id');
    }

    public function items()
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }

    // Methods
    public function calculateTotals()
    {
        $subtotal = $this->items->sum('line_total');
        $this->subtotal = $subtotal;
        $this->total_amount = $subtotal + $this->tax_amount - $this->discount_amount;
        $this->save();
    }

    public function generatePoNumber()
    {
        $year = date('Y');
        $lastPo = static::whereYear('created_at', $year)
            ->orderBy('id', 'desc')
            ->first();

        if ($lastPo && preg_match('/PO-\d{4}-(\d{4})/', $lastPo->po_number, $matches)) {
            $number = intval($matches[1]) + 1;
        } else {
            $number = 1;
        }

        return sprintf('PO-%s-%04d', $year, $number);
    }

    public function canBeApproved()
    {
        return $this->status === 'submitted';
    }

    public function approve(User $user)
    {
        if (!$this->canBeApproved()) {
            return false;
        }

        $this->status = 'approved';
        $this->approved_by_id = $user->id;
        $this->approved_at = now();
        return $this->save();
    }
}
