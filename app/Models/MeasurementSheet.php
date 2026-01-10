<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MeasurementSheet extends Model
{
    protected $fillable = [
        'progress_bill_id',
        'boq_item_id',
        'sheet_number',
        'location',
        'description',
        'length',
        'width',
        'height',
        'quantity',
        'unit_id',
        'calculated_by_id',
        'checked_by_id',
        'date_measured',
        'photos',
        'remarks',
    ];

    protected $casts = [
        'length' => 'decimal:3',
        'width' => 'decimal:3',
        'height' => 'decimal:3',
        'quantity' => 'decimal:4',
        'date_measured' => 'date',
        'photos' => 'array',
    ];

    // Relationships
    public function progressBill(): BelongsTo
    {
        return $this->belongsTo(ProgressBill::class);
    }

    public function boqItem(): BelongsTo
    {
        return $this->belongsTo(BoqItem::class);
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    public function calculatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'calculated_by_id');
    }

    public function checkedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'checked_by_id');
    }

    // Business logic
    public function calculateQuantity(): void
    {
        if ($this->length && $this->width && $this->height) {
            $this->quantity = $this->length * $this->width * $this->height;
        } elseif ($this->length && $this->width) {
            $this->quantity = $this->length * $this->width;
        } elseif ($this->length) {
            $this->quantity = $this->length;
        }
        
        $this->save();
    }
}
