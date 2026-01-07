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
        'name',
        'name_en',
        'code',
        'title',
        'description',
        'project_id',
        'client_id',
        'tender_id',
        'contract_value',
        'value',
        'amount',
        'currency',
        'currency_id',
        'contract_date',
        'start_date',
        'end_date',
        'signed_date',
        'duration_days',
        'contract_type',
        'type',
        'status',
        'notes',
        'company_id',
    ];

    protected $casts = [
        'contract_date' => 'date',
        'contract_value' => 'decimal: 2',
        'value' => 'decimal:2',
        'amount' => 'decimal: 2',
        'start_date' => 'date',
        'end_date' => 'date',
        'signed_date' => 'date',
    ];

    // Relationships
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function tender(): BelongsTo
    {
        return $this->belongsTo(Tender::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function projects(): HasMany
    {
        return $this->hasMany(Project:: class);
    }

    public function guarantees(): HasMany
    {
        return $this->hasMany(Guarantee::class);
    }

    public function variationOrders(): HasMany
    {
        return $this->hasMany(VariationOrder::class);
    }

    public function claims(): HasMany
    {
        return $this->hasMany(Claim:: class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }
}