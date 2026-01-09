<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PettyCashReplenishment extends Model
{
    protected $fillable = [
        'replenishment_number',
        'replenishment_date',
        'petty_cash_account_id',
        'amount',
        'payment_method',
        'reference_number',
        'from_account_type',
        'from_account_id',
        'status',
        'requested_by_id',
        'approved_by_id',
        'approved_at',
        'notes',
        'company_id',
    ];

    protected $casts = [
        'replenishment_date' => 'date',
        'amount' => 'decimal:2',
        'approved_at' => 'datetime',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function pettyCashAccount(): BelongsTo
    {
        return $this->belongsTo(PettyCashAccount::class);
    }

    public function requestedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by_id');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by_id');
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }
}
