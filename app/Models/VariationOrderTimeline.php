<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VariationOrderTimeline extends Model
{
    protected $table = 'variation_order_timeline';
    
    protected $fillable = [
        'variation_order_id',
        'action',
        'from_status',
        'to_status',
        'notes',
        'performed_by',
    ];

    public function variationOrder()
    {
        return $this->belongsTo(VariationOrder::class);
    }

    public function performedBy()
    {
        return $this->belongsTo(User::class, 'performed_by');
    }
}
