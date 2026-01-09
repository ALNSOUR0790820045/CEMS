<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PriceRequestItem extends Model
{
    protected $fillable = [
        'price_request_id',
        'item_description',
        'specifications',
        'unit',
        'quantity',
    ];

    protected $casts = [
        'quantity' => 'decimal:4',
    ];

    public function priceRequest()
    {
        return $this->belongsTo(PriceRequest::class);
    }

    public function quotationItems()
    {
        return $this->hasMany(PriceQuotationItem::class, 'request_item_id');
    }
}
