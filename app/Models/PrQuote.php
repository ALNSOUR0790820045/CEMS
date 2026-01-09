<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PrQuote extends Model
{
    protected $fillable = [
        'quote_number',
        'purchase_requisition_id',
        'vendor_id',
        'quote_date',
        'validity_date',
        'total_amount',
        'currency_id',
        'payment_terms',
        'delivery_terms',
        'status',
        'attachment_path',
        'notes',
        'company_id',
    ];

    protected $casts = [
        'quote_date' => 'date',
        'validity_date' => 'date',
        'total_amount' => 'decimal:2',
    ];

    // Boot method for auto-numbering
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($quote) {
            if (empty($quote->quote_number)) {
                $year = date('Y');
                $lastQuote = static::where('quote_number', 'like', "QT-{$year}-%")
                    ->orderBy('id', 'desc')
                    ->first();

                if ($lastQuote) {
                    $lastNumber = intval(substr($lastQuote->quote_number, -4));
                    $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
                } else {
                    $newNumber = '0001';
                }

                $quote->quote_number = "QT-{$year}-{$newNumber}";
            }
        });
    }

    // Relationships
    public function purchaseRequisition()
    {
        return $this->belongsTo(PurchaseRequisition::class);
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function items()
    {
        return $this->hasMany(PrQuoteItem::class);
    }

    // Methods
    public function select()
    {
        // Mark this quote as selected and others as rejected
        $this->update(['status' => 'selected']);
        
        // Mark other quotes for same PR as rejected
        static::where('purchase_requisition_id', $this->purchase_requisition_id)
            ->where('id', '!=', $this->id)
            ->where('status', '!=', 'selected')
            ->update(['status' => 'rejected']);
    }
}
