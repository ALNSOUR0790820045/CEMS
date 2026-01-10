<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PhotoTag extends Model
{
    protected $fillable = [
        'name',
        'name_en',
        'color',
        'usage_count',
        'company_id',
    ];

    protected $casts = [
        'usage_count' => 'integer',
    ];

    // Relationships
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}
