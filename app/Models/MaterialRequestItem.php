<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MaterialRequestItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'material_request_id',
        'material_id',
        'description',
        'quantity_requested',
        'quantity_approved',
        'quantity_issued',
        'unit_id',
        'unit_price',
        'total_price',
        'notes',
    ];

    protected $casts = [
        'quantity_requested' => 'decimal:2',
        'quantity_approved' => 'decimal:2',
        'quantity_issued' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'total_price' => 'decimal:2',
    ];

    // Relationships
    public function materialRequest(): BelongsTo
    {
        return $this->belongsTo(MaterialRequest::class);
    }

    public function material(): BelongsTo
    {
        return $this->belongsTo(Material::class);
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    // Automatic calculations
    protected static function booted()
    {
        static::saving(function ($item) {
            if ($item->quantity_approved && $item->unit_price) {
                $item->total_price = $item->quantity_approved * $item->unit_price;
            } elseif ($item->quantity_requested && $item->unit_price) {
                $item->total_price = $item->quantity_requested * $item->unit_price;
            }
        });
    }

    // Helper methods
    public function getRemainingQuantityAttribute()
    {
        $approved = $this->quantity_approved ?? $this->quantity_requested;
        return $approved - $this->quantity_issued;
    }

    public function isFullyIssued(): bool
    {
        $approved = $this->quantity_approved ?? $this->quantity_requested;
        return $this->quantity_issued >= $approved;
    }
}
