<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RiskMatrixSetting extends Model
{
    protected $fillable = [
        'company_id',
        'probability_levels',
        'impact_levels',
        'risk_thresholds',
        'cost_impact_ranges',
        'schedule_impact_ranges',
        'is_active',
    ];

    protected $casts = [
        'probability_levels' => 'array',
        'impact_levels' => 'array',
        'risk_thresholds' => 'array',
        'cost_impact_ranges' => 'array',
        'schedule_impact_ranges' => 'array',
        'is_active' => 'boolean',
    ];

    // Relationships
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    // Helper method to get default settings
    public static function getDefaultSettings(): array
    {
        return [
            'probability_levels' => [
                ['level' => 'very_low', 'score' => 1, 'description' => 'نادر جداً (< 10%)'],
                ['level' => 'low', 'score' => 2, 'description' => 'نادر (10-30%)'],
                ['level' => 'medium', 'score' => 3, 'description' => 'محتمل (30-50%)'],
                ['level' => 'high', 'score' => 4, 'description' => 'مرجح (50-70%)'],
                ['level' => 'very_high', 'score' => 5, 'description' => 'شبه مؤكد (> 70%)'],
            ],
            'impact_levels' => [
                ['level' => 'very_low', 'score' => 1, 'description' => 'ضئيل جداً'],
                ['level' => 'low', 'score' => 2, 'description' => 'طفيف'],
                ['level' => 'medium', 'score' => 3, 'description' => 'متوسط'],
                ['level' => 'high', 'score' => 4, 'description' => 'كبير'],
                ['level' => 'very_high', 'score' => 5, 'description' => 'كارثي'],
            ],
            'risk_thresholds' => [
                'low' => '1-4',
                'medium' => '5-9',
                'high' => '10-15',
                'critical' => '16-25',
            ],
        ];
    }
}
