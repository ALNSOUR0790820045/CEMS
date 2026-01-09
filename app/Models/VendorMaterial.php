<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VendorMaterial extends Model
{
    protected $fillable = [
        'vendor_id',
        'material_id',
        'unit_price',
        'currency_id',
        'lead_time_days',
        'minimum_order_quantity',
        'is_preferred',
        'notes',
        'company_id',
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'minimum_order_quantity' => 'decimal:2',
        'lead_time_days' => 'integer',
        'is_preferred' => 'boolean',
    ];

    // Relationships
    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    // Scopes
    public function scopePreferred($query)
    {
        return $query->where('is_preferred', true);
    }
}
