<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Currency extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'code',
        'name',
        'name_en',
        'symbol',
        'exchange_rate',
        'is_base',
        'is_active',
        'company_id',
    ];

    protected $casts = [
        'exchange_rate' => 'decimal:4',
        'is_base' => 'boolean',
        'is_active' => 'boolean',
    ];

    /**
     * Get the company that owns the currency.
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}
