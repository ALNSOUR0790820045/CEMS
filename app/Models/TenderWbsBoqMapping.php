<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TenderWbsBoqMapping extends Model
{
    protected $table = 'tender_wbs_boq_mapping';

    protected $fillable = [
        'tender_wbs_id',
        'tender_boq_item_id',
    ];

    // Relationships
    public function wbs()
    {
        return $this->belongsTo(TenderWbs::class, 'tender_wbs_id');
    }

    public function boqItem()
    {
        return $this->belongsTo(TenderBoqItem::class, 'tender_boq_item_id');
    }
}
