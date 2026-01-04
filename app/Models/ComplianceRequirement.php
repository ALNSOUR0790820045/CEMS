<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ComplianceRequirement extends Model
{
    protected $fillable = [
        'requirement_code',
        'requirement_name',
        'regulatory_body',
        'requirement_type',
        'applicable_to',
        'description',
        'frequency',
        'is_mandatory',
        'penalty_description',
        'company_id',
    ];

    protected $casts = [
        'is_mandatory' => 'boolean',
    ];

    // Relationships
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function trackings()
    {
        return $this->hasMany(ComplianceTracking::class);
    }

    // Scopes
    public function scopeMandatory($query)
    {
        return $query->where('is_mandatory', true);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('requirement_type', $type);
    }

    public function scopeApplicableTo($query, $applicableTo)
    {
        return $query->where('applicable_to', $applicableTo);
    }
}
