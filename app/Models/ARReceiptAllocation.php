<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ARReceiptAllocation extends Model
{
    protected $table = 'a_r_receipt_allocations';

    protected $fillable = [
        'a_r_receipt_id',
        'a_r_invoice_id',
        'allocated_amount',
    ];

    protected $casts = [
        'allocated_amount' => 'decimal:2',
    ];

    public function arReceipt()
    {
        return $this->belongsTo(ARReceipt::class);
    }

    public function arInvoice()
    {
        return $this->belongsTo(ARInvoice::class);
    }
}
