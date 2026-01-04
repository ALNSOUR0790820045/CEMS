<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Contract extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'contract_number',
        'title',
        'project_id',
        'contract_type',
        'signing_date',
        'commencement_date',
        'completion_date',
        'contract_value',
        'currency',
        'default_notice_period',
        'status',
        'notes',
    ];

    protected $casts = [
        'signing_date' => 'date',
        'commencement_date' => 'date',
        'completion_date' => 'date',
        'contract_value' => 'decimal:2',
        'default_notice_period' => 'integer',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function correspondence(): HasMany
    {
        return $this->hasMany(Correspondence::class);
    }

    public function claims(): HasMany
    {
        return $this->hasMany(Claim::class);
    }

    public function variationOrders(): HasMany
    {
        return $this->hasMany(VariationOrder::class);
    }

    public function timeBarEvents(): HasMany
    {
        return $this->hasMany(TimeBarEvent::class);
    }

    public function timeBarContractualClauses(): HasMany
    {
        return $this->hasMany(TimeBarContractualClause::class);
    }

    public function timeBarSettings(): HasMany
    {
        return $this->hasMany(TimeBarSetting::class);
    }
}
