<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SiteReceipt extends Model
{
    protected $fillable = [
        'project_id',
        'purchase_order_id',
        'supplier_id',
        'receipt_number',
        'receipt_date',
        'receipt_time',
        'vehicle_number',
        'driver_name',
        'driver_phone',
        'latitude',
        'longitude',
        'location_name',
        'gps_captured_at',
        'invoice_document',
        'delivery_note',
        'packing_list',
        'quality_certificates',
        'status',
        'engineer_id',
        'engineer_signature',
        'engineer_signed_at',
        'engineer_notes',
        'storekeeper_id',
        'storekeeper_signature',
        'storekeeper_signed_at',
        'storekeeper_notes',
        'driver_signature_name',
        'driver_signature',
        'driver_signed_at',
        'grn_id',
        'auto_grn_created',
        'grn_created_at',
        'finance_notified',
        'finance_notified_at',
        'payment_status',
        'general_notes',
    ];

    protected $casts = [
        'receipt_date' => 'date',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'gps_captured_at' => 'datetime',
        'quality_certificates' => 'array',
        'engineer_signed_at' => 'datetime',
        'storekeeper_signed_at' => 'datetime',
        'driver_signed_at' => 'datetime',
        'auto_grn_created' => 'boolean',
        'grn_created_at' => 'datetime',
        'finance_notified' => 'boolean',
        'finance_notified_at' => 'datetime',
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

    public function engineer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'engineer_id');
    }

    public function storekeeper(): BelongsTo
    {
        return $this->belongsTo(User::class, 'storekeeper_id');
    }

    public function grn(): BelongsTo
    {
        return $this->belongsTo(GoodsReceiptNote::class, 'grn_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(SiteReceiptItem::class);
    }

    public function photos(): HasMany
    {
        return $this->hasMany(SiteReceiptPhoto::class);
    }

    /**
     * Check if all three signatures are completed
     */
    public function hasAllSignatures(): bool
    {
        return !empty($this->engineer_signature) 
            && !empty($this->storekeeper_signature) 
            && !empty($this->driver_signature);
    }

    /**
     * Check if all required documents are uploaded
     */
    public function hasAllDocuments(): bool
    {
        return !empty($this->invoice_document) 
            && !empty($this->delivery_note) 
            && !empty($this->packing_list) 
            && !empty($this->quality_certificates);
    }
}
