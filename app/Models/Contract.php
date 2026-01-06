<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Contract extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'contract_number',
        'name',
        'code',
        'description',
        'project_id',
        'client_id',
        'tender_id',
        'title',
        'contract_date',
        'contract_value',
        'value',
        'start_date',
        'end_date',
        'status',
    ];

    protected $casts = [
        'contract_date' => 'date',
        'contract_value' => 'decimal: 2',
        'value' => 'decimal: 2',
        'start_date' => 'date',
        'end_date' => 'date',
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

    public function guarantees(): HasMany
    {
        return $this->hasMany(Guarantee:: class);
    }

    public function variationOrders(): HasMany
    {
        return $this->hasMany(VariationOrder::class);
    }
}