<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BankStatement extends Model
{
    use HasFactory;

    protected $fillable = [
        'statement_number',
        'bank_account_id',
        'statement_date',
        'period_from',
        'period_to',
        'opening_balance',
        'closing_balance',
        'total_deposits',
        'total_withdrawals',
        'status',
        'reconciled_by_id',
        'reconciled_at',
        'company_id',
    ];

    protected $casts = [
        'statement_date' => 'date',
        'period_from' => 'date',
        'period_to' => 'date',
        'opening_balance' => 'decimal:2',
        'closing_balance' => 'decimal:2',
        'total_deposits' => 'decimal:2',
        'total_withdrawals' => 'decimal:2',
        'reconciled_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($statement) {
            if (!$statement->statement_number) {
                $statement->statement_number = static::generateStatementNumber();
            }
        });
    }

    public static function generateStatementNumber(): string
    {
        $year = date('Y');
        $lastStatement = static::where('statement_number', 'like', "BS-{$year}-%")
            ->latest('id')
            ->first();

        if ($lastStatement) {
            $lastNumber = (int)substr($lastStatement->statement_number, -4);
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }

        return "BS-{$year}-{$newNumber}";
    }

    public function bankAccount(): BelongsTo
    {
        return $this->belongsTo(BankAccount::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function reconciledBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reconciled_by_id');
    }

    public function lines(): HasMany
    {
        return $this->hasMany(BankStatementLine::class);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByBankAccount($query, $bankAccountId)
    {
        return $query->where('bank_account_id', $bankAccountId);
    }
}
