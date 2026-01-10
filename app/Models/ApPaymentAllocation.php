<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApPaymentAllocation extends Model
{
    protected $fillable = [
        'ap_payment_id',
        'ap_invoice_id',
        'allocated_amount',
    ];

    protected $casts = [
        'allocated_amount' => 'decimal:2',
    ];

    // Boot method to update invoice paid amount
    protected static function boot()
    {
        parent::boot();

        static::created(function ($allocation) {
            $allocation->apInvoice->updatePaidAmount();
        });

        static::updated(function ($allocation) {
            $allocation->apInvoice->updatePaidAmount();
        });

        static::deleted(function ($allocation) {
            $allocation->apInvoice->updatePaidAmount();
        });
    }

    // Relationships
    public function apPayment()
    {
        return $this->belongsTo(ApPayment::class);
    }

    public function apInvoice()
    {
        return $this->belongsTo(ApInvoice::class);
    }
}
