<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class BankAccount extends Model
{
    use HasFactory, SoftDeletes;
    
    protected $fillable = [
        'user_id',
        'account_name',
        'account_number',
        'bank_name',
        'branch',
        'swift_code',
        'iban',
        'currency_id',
        'check_template_id',
        'balance',
        'current_balance',
        'bank_balance',
        'is_active',
        'is_primary',
        'gl_account_id',
        'company_id',
    ];

    protected $casts = [
        'balance' => 'decimal:2',
        'current_balance' => 'decimal:2',
        'bank_balance' => 'decimal:2',
        'is_active' => 'boolean',
        'is_primary' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }

    public function glAccount(): BelongsTo
    {
        return $this->belongsTo(GLAccount::class);
    }

    public function checkTemplate(): BelongsTo
    {
        return $this->belongsTo(PaymentTemplate::class, 'check_template_id');
    }

    public function checks(): HasMany
    {
        return $this->hasMany(Check::class);
    }

    public function apPayments(): HasMany
    {
        return $this->hasMany(ApPayment::class);
    }

    public function arReceipts(): HasMany
    {
        return $this->hasMany(ARReceipt::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function bankStatements(): HasMany
    {
        return $this->hasMany(BankStatement::class);
    }

    public function bankReconciliations(): HasMany
    {
        return $this->hasMany(BankReconciliation::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopePrimary($query)
    {
        return $query->where('is_primary', true);
    }
}
