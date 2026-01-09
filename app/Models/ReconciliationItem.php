<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ReconciliationItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'bank_reconciliation_id',
        'item_type',
        'description',
        'amount',
        'transaction_date',
        'reference_number',
        'is_cleared',
        'cleared_date',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'transaction_date' => 'date',
        'cleared_date' => 'date',
        'is_cleared' => 'boolean',
    ];

    public function bankReconciliation(): BelongsTo
    {
        return $this->belongsTo(BankReconciliation::class);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('item_type', $type);
    }

    public function scopeCleared($query)
    {
        return $query->where('is_cleared', true);
    }

    public function scopeUncleared($query)
    {
        return $query->where('is_cleared', false);
    }
}
