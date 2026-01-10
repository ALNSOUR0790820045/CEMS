<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class GoodsReceiptNote extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'project_id',
        'purchase_order_id',
        'supplier_id',
        'grn_number',
        'receipt_date',
        'status',
        'notes',
        'received_by',
        'verified_by',
        'verified_at',
        'inventory_updated',
    ];

    protected $casts = [
        'receipt_date' => 'date',
        'verified_at' => 'datetime',
        'inventory_updated' => 'boolean',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function purchaseOrder(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function receivedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'received_by');
    }

    public function verifiedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function siteReceipt(): HasOne
    {
        return $this->hasOne(SiteReceipt::class, 'grn_id');
    }
}
