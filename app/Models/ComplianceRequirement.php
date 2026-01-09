<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ComplianceRequirement extends Model
{
    protected $fillable = [
        'code',
        'name',
        'name_en',
        'description',
        'category',
        'regulation_reference',
        'is_mandatory',
        'frequency',
        'responsible_role',
        'company_id',
    ];

    protected $casts = [
        'is_mandatory' => 'boolean',
    ];

    // Relationships
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function checks(): HasMany
    {
        return $this->hasMany(ComplianceCheck::class);
    }

    // Scopes
    public function scopeMandatory($query)
    {
        return $query->where('is_mandatory', true);
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    public function scopeByFrequency($query, $frequency)
    {
        return $query->where('frequency', $frequency);
    }

    // Accessors
    public function getCategoryNameAttribute()
    {
        $categories = [
            'safety' => 'السلامة',
            'environmental' => 'البيئة',
            'legal' => 'القانونية',
            'quality' => 'الجودة',
            'financial' => 'المالية',
        ];
        
        return $categories[$this->category] ?? $this->category;
    }

    public function getFrequencyNameAttribute()
    {
        $frequencies = [
            'one_time' => 'مرة واحدة',
            'monthly' => 'شهري',
            'quarterly' => 'ربع سنوي',
            'annually' => 'سنوي',
        ];
        
        return $frequencies[$this->frequency] ?? $this->frequency;
    }
}
