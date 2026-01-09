<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GRNItem extends Model
{
    protected $table = 'grn_items';

    protected $fillable = [
        'grn_id',
        'purchase_order_item_id',
        'material_id',
        'ordered_quantity',
        'received_quantity',
        'accepted_quantity',
        'rejected_quantity',
        'unit_price',
        'total_amount',
        'batch_number',
        'expiry_date',
        'inspection_status',
        'rejection_reason',
        'notes',
    ];

    protected $casts = [
        'ordered_quantity' => 'decimal:2',
        'received_quantity' => 'decimal:2',
        'accepted_quantity' => 'decimal:2',
        'rejected_quantity' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'expiry_date' => 'date',
    ];

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($item) {
            $item->total_amount = $item->received_quantity * $item->unit_price;
        });

        static::saved(function ($item) {
            $item->grn->calculateTotalValue();
        });
    }

    public function grn()
    {
        return $this->belongsTo(GRN::class);
    }

    public function purchaseOrderItem()
    {
        return $this->belongsTo(PurchaseOrderItem::class);
    }

    public function material()
    {
        return $this->belongsTo(Material::class);
    }
}
