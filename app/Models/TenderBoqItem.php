<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TenderBoqItem extends Model
{
    protected $fillable = [
        'tender_id',
        'item_code',
        'description',
        'description_en',
        'unit',
        'quantity',
        'unit_price',
        'total_price',
        'sort_order',
    ];

    protected $casts = [
        'quantity' => 'decimal:3',
        'unit_price' => 'decimal:2',
        'total_price' => 'decimal:2',
    ];

    // Relationships
    public function tender()
    {
        return $this->belongsTo(Tender::class);
    }

    public function wbsItems()
    {
        return $this->belongsToMany(TenderWbs::class, 'tender_wbs_boq_mapping');
    }
}
