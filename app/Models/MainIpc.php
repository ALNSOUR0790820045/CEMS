<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MainIpc extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'project_id',
        'ipc_number',
        'ipc_date',
        'period_from',
        'period_to',
        'amount',
        'previous_total',
        'current_total',
        'status',
        'notes',
    ];

    protected $casts = [
        'ipc_date' => 'date',
        'period_from' => 'date',
        'period_to' => 'date',
        'amount' => 'decimal:2',
        'previous_total' => 'decimal:2',
        'current_total' => 'decimal:2',
    ];

    // Relationships
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function priceEscalationCalculations(): HasMany
    {
        return $this->hasMany(PriceEscalationCalculation::class);
    }
}
