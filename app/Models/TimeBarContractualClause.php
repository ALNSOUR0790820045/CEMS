<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TimeBarContractualClause extends Model
{
    protected $fillable = [
        'contract_id',
        'clause_number',
        'clause_title',
        'clause_text',
        'notice_period_days',
        'notice_requirements',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'notice_period_days' => 'integer',
    ];

    public function contract(): BelongsTo
    {
        return $this->belongsTo(Contract::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
