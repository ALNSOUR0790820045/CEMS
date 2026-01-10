<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PoAmendment extends Model
{
    protected $fillable = [
        'amendment_number',
        'purchase_order_id',
        'amendment_date',
        'amendment_type',
        'description',
        'old_value',
        'new_value',
        'reason',
        'status',
        'requested_by_id',
        'approved_by_id',
        'approved_at',
    ];

    protected $casts = [
        'amendment_date' => 'date',
        'approved_at' => 'datetime',
    ];

    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function requestedBy()
    {
        return $this->belongsTo(User::class, 'requested_by_id');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by_id');
    }
}
