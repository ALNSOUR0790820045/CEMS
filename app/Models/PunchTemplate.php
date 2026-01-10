<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PunchTemplate extends Model
{
    protected $fillable = [
        'name',
        'name_en',
        'description',
        'discipline',
        'category',
        'items',
        'is_active',
        'company_id',
    ];

    protected $casts = [
        'items' => 'array',
        'is_active' => 'boolean',
    ];

    // Relationships
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}
