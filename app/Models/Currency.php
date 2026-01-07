<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
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

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function contracts(): HasMany
    {
        return $this->hasMany(Contract::class);
    }

    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }

    public function glAccounts(): HasMany
    {
        return $this->hasMany(GLAccount::class);
    }

    public function guarantees(): HasMany
    {
        return $this->hasMany(Guarantee::class);
    }

    public function apInvoices(): HasMany
    {
        return $this->hasMany(ApInvoice::class);
    }

    public function apPayments(): HasMany
    {
        return $this->hasMany(ApPayment::class);
    }

    public function arInvoices(): HasMany
    {
        return $this->hasMany(ARInvoice::class);
    }

    public function arReceipts(): HasMany
    {
        return $this->hasMany(ARReceipt::class);
    }

    public function bankAccounts(): HasMany
    {
        return $this->hasMany(BankAccount::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeBase($query)
    {
        return $query->where('is_base', true);
    }
}