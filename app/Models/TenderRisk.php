<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TenderRisk extends Model
{
    protected $fillable = [
        'tender_id',
        'risk_code',
        'risk_category',
        'risk_title',
        'risk_description',
        'probability',
        'probability_score',
        'impact',
        'impact_score',
        'risk_score',
        'risk_level',
        'cost_impact_min',
        'cost_impact_max',
        'cost_impact_expected',
        'schedule_impact_days',
        'response_strategy',
        'response_plan',
        'response_cost',
        'status',
        'owner_id',
    ];

    protected $casts = [
        'probability_score' => 'integer',
        'impact_score' => 'integer',
        'risk_score' => 'integer',
        'cost_impact_min' => 'decimal:2',
        'cost_impact_max' => 'decimal:2',
        'cost_impact_expected' => 'decimal:2',
        'schedule_impact_days' => 'integer',
        'response_cost' => 'decimal:2',
    ];

    // Relationships
    public function tender(): BelongsTo
    {
        return $this->belongsTo(Tender::class);
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function events(): HasMany
    {
        return $this->hasMany(TenderRiskEvent::class);
    }

    // Auto-calculate risk score and level
    protected static function booted()
    {
        static::saving(function ($risk) {
            // Calculate risk score
            $risk->risk_score = $risk->probability_score * $risk->impact_score;
            
            // Calculate risk level
            if ($risk->risk_score >= 21) {
                $risk->risk_level = 'critical';
            } elseif ($risk->risk_score >= 13) {
                $risk->risk_level = 'high';
            } elseif ($risk->risk_score >= 7) {
                $risk->risk_level = 'medium';
            } else {
                $risk->risk_level = 'low';
            }
        });
    }

    // Translations
    public function getCategoryNameAttribute(): string
    {
        return match ($this->risk_category) {
            'technical' => 'فنية',
            'financial' => 'مالية',
            'contractual' => 'تعاقدية',
            'schedule' => 'جدولة',
            'resources' => 'موارد',
            'external' => 'خارجية',
            'safety' => 'سلامة',
            'quality' => 'جودة',
            'political' => 'سياسية',
            'environmental' => 'بيئية',
            'other' => 'أخرى',
            default => $this->risk_category,
        };
    }

    public function getProbabilityNameAttribute(): string
    {
        return match ($this->probability) {
            'very_low' => 'نادر جداً (< 10%)',
            'low' => 'نادر (10-30%)',
            'medium' => 'محتمل (30-50%)',
            'high' => 'مرجح (50-70%)',
            'very_high' => 'شبه مؤكد (> 70%)',
            default => $this->probability,
        };
    }

    public function getImpactNameAttribute(): string
    {
        return match ($this->impact) {
            'very_low' => 'ضئيل جداً',
            'low' => 'طفيف',
            'medium' => 'متوسط',
            'high' => 'كبير',
            'very_high' => 'كارثي',
            default => $this->impact,
        };
    }

    public function getResponseStrategyNameAttribute(): string
    {
        return match ($this->response_strategy) {
            'avoid' => 'تجنب',
            'mitigate' => 'تخفيف',
            'transfer' => 'نقل',
            'accept' => 'قبول',
            default => $this->response_strategy ?? '-',
        };
    }

    public function getStatusNameAttribute(): string
    {
        return match ($this->status) {
            'identified' => 'محددة',
            'assessed' => 'مقيّمة',
            'planned' => 'مخطط لها',
            'monitored' => 'مراقبة',
            'closed' => 'مغلقة',
            default => $this->status,
        };
    }

    public function getRiskLevelBadgeAttribute(): string
    {
        return match ($this->risk_level) {
            'critical' => '<span class="badge badge-critical">⚫ حرج</span>',
            'high' => '<span class="badge badge-high">🔴 عالي</span>',
            'medium' => '<span class="badge badge-medium">🟡 متوسط</span>',
            'low' => '<span class="badge badge-low">🟢 منخفض</span>',
            default => $this->risk_level,
        };
    }
}
