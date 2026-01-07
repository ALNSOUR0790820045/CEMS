<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ApInvoice extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'invoice_number',
        'invoice_date',
        'due_date',
        'vendor_id',
        'project_id',
        'purchase_order_id',
        'currency_id',
        'exchange_rate',
        'subtotal',
        'tax_amount',
        'discount_amount',
        'paid_amount',
        'status',
        'payment_terms',
        'gl_account_id',
        'approved_by_id',
        'approved_at',
        'attachment_path',
        'notes',
        'company_id',
        'created_by_id',
    ];

    protected $casts = [
        'invoice_date' => 'date',
        'due_date' => 'date',
        'exchange_rate' => 'decimal:4',
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'approved_at' => 'datetime',
    ];

    // Computed attributes (from DB stored columns)
    protected $appends = ['total_amount', 'balance'];

    public function getTotalAmountAttribute()
    {
        return $this->attributes['total_amount'] ?? ($this->subtotal + $this->tax_amount - $this->discount_amount);
    }

    public function getBalanceAttribute()
    {
        return $this->attributes['balance'] ?? ($this->subtotal + $this->tax_amount - $this->discount_amount - $this->paid_amount);
    }

    // Boot method for auto-numbering
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($invoice) {
            if (empty($invoice->invoice_number)) {
                $year = date('Y');
                $lastInvoice = static::where('invoice_number', 'like', "API-{$year}-%")
                    ->orderBy('id', 'desc')
                    ->first();

                if ($lastInvoice) {
                    $lastNumber = intval(substr($lastInvoice->invoice_number, -4));
                    $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
                } else {
                    $newNumber = '0001';
                }

                $invoice->invoice_number = "API-{$year}-{$newNumber}";
            }
        });
    }

    // Relationships
    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }

    public function glAccount()
    {
        return $this->belongsTo(GlAccount::class);
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
        return $this->hasMany(ApInvoiceItem::class);
    }

    public function paymentAllocations()
    {
        return $this->hasMany(ApPaymentAllocation::class);
    }

    // Methods
    public function approve(User $user)
    {
        $this->update([
            'status' => 'approved',
            'approved_by_id' => $user->id,
            'approved_at' => now(),
        ]);
    }

    public function updatePaidAmount()
    {
        $this->paid_amount = $this->paymentAllocations()->sum('allocated_amount');

        if ($this->paid_amount >= $this->total_amount) {
            $this->status = 'paid';
        } elseif ($this->paid_amount > 0) {
            $this->status = 'partially_paid';
        }

        $this->save();
    }
}
