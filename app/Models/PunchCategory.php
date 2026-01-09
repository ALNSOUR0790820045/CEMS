<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PunchCategory extends Model
{
    protected $fillable = [
        'code',
        'name',
        'name_en',
        'parent_id',
        'discipline',
        'color',
        'is_active',
        'company_id',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Relationships
    public function parent(): BelongsTo
    {
        return $this->belongsTo(PunchCategory::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(PunchCategory::class, 'parent_id');
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}
