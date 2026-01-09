<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KpiDefinition extends Model
{
    protected $fillable = [
        'code',
        'name',
        'name_en',
        'description',
        'category',
        'calculation_formula',
        'unit',
        'target_value',
        'warning_threshold',
        'critical_threshold',
        'frequency',
        'is_active',
        'company_id',
    ];

    protected $casts = [
        'target_value' => 'decimal:2',
        'warning_threshold' => 'decimal:2',
        'critical_threshold' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function values(): HasMany
    {
        return $this->hasMany(KpiValue::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    public function scopeByFrequency($query, $frequency)
    {
        return $query->where('frequency', $frequency);
    }
}
