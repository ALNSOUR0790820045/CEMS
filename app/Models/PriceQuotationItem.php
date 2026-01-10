<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PriceQuotationItem extends Model
{
    protected $fillable = [
        'quotation_id',
        'request_item_id',
        'unit_price',
        'total_price',
        'remarks',
    ];

    protected $casts = [
        'unit_price' => 'decimal:4',
        'total_price' => 'decimal:2',
    ];

    public function quotation()
    {
        return $this->belongsTo(PriceQuotation::class, 'quotation_id');
    }

    public function requestItem()
    {
        return $this->belongsTo(PriceRequestItem::class, 'request_item_id');
    }
}
