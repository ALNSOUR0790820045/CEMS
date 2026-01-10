<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InspectionType extends Model
{
    protected $fillable = [
        'code',
        'name',
        'name_en',
        'category',
        'description',
        'default_checklist_id',
        'requires_witness',
        'requires_approval',
        'frequency',
        'is_active',
        'company_id',
    ];

    protected $casts = [
        'requires_witness' => 'boolean',
        'requires_approval' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function defaultChecklist(): BelongsTo
    {
        return $this->belongsTo(InspectionTemplate::class, 'default_checklist_id');
    }

    public function requests(): HasMany
    {
        return $this->hasMany(InspectionRequest::class);
    }

    public function inspections(): HasMany
    {
        return $this->hasMany(Inspection::class);
    }
}
