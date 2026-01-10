<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class PettyCashAccount extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'account_code',
        'account_name',
        'custodian_id',
        'float_amount',
        'current_balance',
        'minimum_balance',
        'gl_account_id',
        'project_id',
        'branch_id',
        'is_active',
        'company_id',
    ];

    protected $casts = [
        'float_amount' => 'decimal:2',
        'current_balance' => 'decimal:2',
        'minimum_balance' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function custodian(): BelongsTo
    {
        return $this->belongsTo(User::class, 'custodian_id');
    }

    public function glAccount(): BelongsTo
    {
        return $this->belongsTo(GLAccount::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(PettyCashTransaction::class);
    }

    public function replenishments(): HasMany
    {
        return $this->hasMany(PettyCashReplenishment::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeLowBalance($query)
    {
        return $query->whereRaw('current_balance <= minimum_balance');
    }

    public function hasAvailableBalance($amount): bool
    {
        return $this->current_balance >= $amount;
    }

    public function isLowBalance(): bool
    {
        return $this->current_balance <= $this->minimum_balance;
    }
}
