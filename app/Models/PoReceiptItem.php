<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PoReceiptItem extends Model
{
    protected $fillable = [
        'po_receipt_id',
        'po_item_id',
        'quantity_received',
        'quantity_accepted',
        'quantity_rejected',
        'rejection_reason',
        'inspection_notes',
        'batch_number',
        'expiry_date',
    ];

    protected $casts = [
        'quantity_received' => 'decimal:2',
        'quantity_accepted' => 'decimal:2',
        'quantity_rejected' => 'decimal:2',
        'expiry_date' => 'date',
    ];

    public function receipt()
    {
        return $this->belongsTo(PoReceipt::class, 'po_receipt_id');
    }

    public function purchaseOrderItem()
    {
        return $this->belongsTo(PurchaseOrderItem::class, 'po_item_id');
    }
}
