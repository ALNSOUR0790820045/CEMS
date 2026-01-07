<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PurchaseOrder extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'po_number',
        'po_date',
        'vendor_id',
        'purchase_requisition_id',
        'project_id',
        'delivery_address',
        'delivery_date',
        'payment_terms_id',
        'currency_id',
        'exchange_rate',
        'subtotal',
        'discount_amount',
        'tax_amount',
        'total_amount',
        'status',
        'approved_by_id',
        'approved_at',
        'sent_at',
        'notes',
        'terms',
        'terms_and_conditions',
        'expected_delivery_date',
        'company_id',
        'created_by',
    ];

    protected $casts = [
        'po_date' => 'date',
        'delivery_date' => 'date',
        'expected_delivery_date' => 'date',
        'approved_at' => 'datetime',
        'sent_at' => 'datetime',
        'total_amount' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'exchange_rate' => 'decimal:4',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function purchaseRequisition()
    {
        return $this->belongsTo(PurchaseRequisition::class);
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by_id');
    }

    public function items()
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }

    public function grns()
    {
        return $this->hasMany(GRN::class);
    }

    public function receipts()
    {
        return $this->hasMany(PoReceipt::class);
    }

    public function amendments()
    {
        return $this->hasMany(PoAmendment::class);
    }

    public function apInvoices()
    {
        return $this->hasMany(ApInvoice::class);
    }
}