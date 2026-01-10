<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SiteReceiptItem extends Model
{
    protected $fillable = [
        'site_receipt_id',
        'product_id',
        'po_item_id',
        'ordered_quantity',
        'received_quantity',
        'accepted_quantity',
        'rejected_quantity',
        'unit',
        'condition',
        'condition_notes',
        'batch_number',
        'serial_number',
        'manufacturing_date',
        'expiry_date',
    ];

    protected $casts = [
        'ordered_quantity' => 'decimal:3',
        'received_quantity' => 'decimal:3',
        'accepted_quantity' => 'decimal:3',
        'rejected_quantity' => 'decimal:3',
        'manufacturing_date' => 'date',
        'expiry_date' => 'date',
    ];

    public function siteReceipt(): BelongsTo
    {
        return $this->belongsTo(SiteReceipt::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function poItem(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrderItem::class, 'po_item_id');
    }
}
