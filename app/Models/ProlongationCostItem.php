<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProlongationCostItem extends Model
{
    protected $fillable = [
        'eot_claim_id',
        'cost_category',
        'description',
        'duration_days',
        'daily_rate',
        'total_cost',
        'supporting_document',
        'justification',
    ];

    protected $casts = [
        'duration_days' => 'integer',
        'daily_rate' => 'decimal:2',
        'total_cost' => 'decimal:2',
    ];

    public function eotClaim(): BelongsTo
    {
        return $this->belongsTo(EotClaim::class);
    }

    // Helper method to get cost category label
    public function getCostCategoryLabelAttribute(): string
    {
        return match($this->cost_category) {
            'site_staff' => 'كادر الموقع',
            'site_facilities' => 'مرافق الموقع',
            'equipment_rental' => 'إيجار معدات',
            'utilities' => 'مرافق (كهرباء، ماء)',
            'security' => 'أمن',
            'insurance' => 'تأمينات إضافية',
            'head_office' => 'إدارة عامة',
            'financing' => 'فوائد/تمويل',
            'other' => 'أخرى',
            default => $this->cost_category,
        };
    }
}
