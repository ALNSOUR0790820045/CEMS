<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PoReceipt extends Model
{
    protected $fillable = [
        'receipt_number',
        'purchase_order_id',
        'receipt_date',
        'received_by_id',
        'warehouse_id',
        'delivery_note_number',
        'status',
        'notes',
        'company_id',
    ];

    protected $casts = [
        'receipt_date' => 'date',
    ];

    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function receivedBy()
    {
        return $this->belongsTo(User::class, 'received_by_id');
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function items()
    {
        return $this->hasMany(PoReceiptItem::class);
    }
}
