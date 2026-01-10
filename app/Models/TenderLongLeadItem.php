<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TenderLongLeadItem extends Model
{
    protected $fillable = [
        'tender_id',
        'tender_procurement_package_id',
        'item_name',
        'description',
        'lead_time_weeks',
        'must_order_by',
        'estimated_cost',
        'is_critical',
        'mitigation_plan',
    ];

    protected $casts = [
        'must_order_by' => 'date',
        'estimated_cost' => 'decimal:2',
        'is_critical' => 'boolean',
    ];

    // Relationships
    public function tender(): BelongsTo
    {
        return $this->belongsTo(Tender::class);
    }

    public function procurementPackage(): BelongsTo
    {
        return $this->belongsTo(TenderProcurementPackage::class);
    }

    // Helpers
    public function isDueWithinDays(int $days): bool
    {
        return $this->must_order_by->diffInDays(now(), false) <= $days;
    }

    public function isOverdue(): bool
    {
        return $this->must_order_by->isPast();
    }
}
