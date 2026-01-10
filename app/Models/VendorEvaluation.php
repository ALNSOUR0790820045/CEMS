<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VendorEvaluation extends Model
{
    protected $fillable = [
        'vendor_id',
        'evaluation_date',
        'evaluation_period_from',
        'evaluation_period_to',
        'quality_score',
        'delivery_score',
        'price_score',
        'service_score',
        'compliance_score',
        'overall_score',
        'strengths',
        'weaknesses',
        'recommendations',
        'evaluated_by_id',
        'company_id',
    ];

    protected $casts = [
        'evaluation_date' => 'date',
        'evaluation_period_from' => 'date',
        'evaluation_period_to' => 'date',
        'quality_score' => 'integer',
        'delivery_score' => 'integer',
        'price_score' => 'integer',
        'service_score' => 'integer',
        'compliance_score' => 'integer',
        'overall_score' => 'decimal:2',
    ];

    // Relationships
    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    public function evaluatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'evaluated_by_id');
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    // Automatically calculate overall score
    protected static function booted()
    {
        static::saving(function ($evaluation) {
            $scores = [
                $evaluation->quality_score,
                $evaluation->delivery_score,
                $evaluation->price_score,
                $evaluation->service_score,
                $evaluation->compliance_score,
            ];

            $evaluation->overall_score = array_sum($scores) / count($scores);
        });
    }
}
