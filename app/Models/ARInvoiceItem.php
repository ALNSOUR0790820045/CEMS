<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ARInvoiceItem extends Model
{
    protected $table = 'a_r_invoice_items';

    protected $fillable = [
        'a_r_invoice_id',
        'description',
        'quantity',
        'unit_price',
        'amount',
        'gl_account_id',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'amount' => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($item) {
            $item->amount = $item->quantity * $item->unit_price;
        });

        static::updating(function ($item) {
            $item->amount = $item->quantity * $item->unit_price;
        });
    }

    public function arInvoice()
    {
        return $this->belongsTo(ARInvoice::class);
    }

    public function glAccount()
    {
        return $this->belongsTo(GLAccount::class);
    }
}
