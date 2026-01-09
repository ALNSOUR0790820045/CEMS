<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PriceHistory extends Model
{
    protected $fillable = [
        'price_list_item_id',
        'effective_date',
        'old_price',
        'new_price',
        'change_percentage',
        'change_reason',
        'notes',
        'updated_by',
    ];

    protected $casts = [
        'effective_date' => 'date',
        'old_price' => 'decimal:4',
        'new_price' => 'decimal:4',
        'change_percentage' => 'decimal:4',
    ];

    public function priceListItem()
    {
        return $this->belongsTo(PriceListItem::class);
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
