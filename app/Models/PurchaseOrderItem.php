<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseOrderItem extends Model
{
    protected $fillable = [
        'purchase_order_id',
        'material_id',
        'description',
        'specifications',
        'quantity',
        'unit_id',
        'unit_price',
        'discount_percentage',
        'discount_amount',
        'tax_percentage',
        'tax_amount',
        'total_price',
        'quantity_received',
        'quantity_invoiced',
        'delivery_date',
        'notes',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'discount_percentage' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'tax_percentage' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'total_price' => 'decimal:2',
        'quantity_received' => 'decimal:2',
        'quantity_invoiced' => 'decimal:2',
        'delivery_date' => 'date',
    ];

    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function material()
    {
        return $this->belongsTo(Material::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function grnItems()
    {
        return $this->hasMany(GRNItem::class);
    }

    public function receiptItems()
    {
        return $this->hasMany(PoReceiptItem::class, 'po_item_id');
    }
}
