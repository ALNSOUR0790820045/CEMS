<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PriceQuotation extends Model
{
    protected $fillable = [
        'price_request_id',
        'vendor_id',
        'quotation_number',
        'quotation_date',
        'validity_date',
        'total_amount',
        'currency',
        'payment_terms',
        'delivery_terms',
        'file_path',
        'is_selected',
    ];

    protected $casts = [
        'quotation_date' => 'date',
        'validity_date' => 'date',
        'total_amount' => 'decimal:2',
        'is_selected' => 'boolean',
    ];

    public function priceRequest()
    {
        return $this->belongsTo(PriceRequest::class);
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function items()
    {
        return $this->hasMany(PriceQuotationItem::class, 'quotation_id');
    }

    public function comparison()
    {
        return $this->hasOne(PriceComparison::class, 'selected_quotation_id');
    }
}
