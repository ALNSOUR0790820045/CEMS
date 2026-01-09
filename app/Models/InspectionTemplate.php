<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InspectionTemplate extends Model
{
    protected $fillable = [
        'template_number',
        'name',
        'name_en',
        'inspection_type_id',
        'category',
        'description',
        'version',
        'is_active',
        'company_id',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function inspectionType(): BelongsTo
    {
        return $this->belongsTo(InspectionType::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(TemplateItem::class);
    }
}
