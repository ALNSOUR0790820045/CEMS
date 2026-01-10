<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Project extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'company_id',
        'name',
        'name_en',
        'code',
        'description',
        'location',
        'contract_value',
        'start_date',
        'end_date',
        'status',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'contract_value' => 'decimal:2',
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    // Relationships
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function ipcs(): HasMany
    {
        return $this->hasMany(MainIpc::class);
    }

    public function priceEscalationContract(): HasOne
    {
        return $this->hasOne(PriceEscalationContract::class);
    }
}
