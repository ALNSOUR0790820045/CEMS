<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TenderProcurementSupplier extends Model
{
    protected $fillable = [
        'tender_procurement_package_id',
        'supplier_id',
        'quoted_price',
        'delivery_days',
        'payment_terms',
        'technical_compliance',
        'score',
        'is_recommended',
    ];

    protected $casts = [
        'quoted_price' => 'decimal:2',
        'is_recommended' => 'boolean',
    ];

    // Relationships
    public function procurementPackage(): BelongsTo
    {
        return $this->belongsTo(TenderProcurementPackage::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }
}
