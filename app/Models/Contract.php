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
        'client_id',
        'tender_id',
        'contract_date',
        'contract_value',
        'status',
    ];

    protected $casts = [
        'contract_date' => 'date',
        'contract_value' => 'decimal: 2',
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

    public function project(): HasOne
    {
        return $this->hasOne(Project::class);
    }

    public function guarantees(): HasMany
    {
        return $this->hasMany(Guarantee::class);
    }
}