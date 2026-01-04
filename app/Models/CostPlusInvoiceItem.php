<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CostPlusInvoiceItem extends Model
{
    protected $fillable = [
        'invoice_id',
        'transaction_id',
        'amount',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(CostPlusInvoice::class, 'invoice_id');
    }

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(CostPlusTransaction::class, 'transaction_id');
    }
}
