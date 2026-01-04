<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BankStatement extends Model
{
    protected $fillable = [
        'statement_number',
        'bank_account_id',
        'statement_date',
        'opening_balance',
        'closing_balance',
        'status',
        'reconciled_by_id',
        'reconciled_at',
        'company_id',
    ];

    protected $casts = [
        'statement_date' => 'date',
        'opening_balance' => 'decimal:2',
        'closing_balance' => 'decimal:2',
        'reconciled_at' => 'datetime',
    ];

    // Boot method for auto-generating statement numbers
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($statement) {
            if (empty($statement->statement_number)) {
                $year = date('Y');
                $lastStatement = static::where('statement_number', 'like', "BS-{$year}-%")
                    ->orderBy('statement_number', 'desc')
                    ->first();

                if ($lastStatement) {
                    $lastNumber = (int) substr($lastStatement->statement_number, -4);
                    $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
                } else {
                    $newNumber = '0001';
                }

                $statement->statement_number = "BS-{$year}-{$newNumber}";
            }
        });
    }

    // Relationships
    public function bankAccount()
    {
        return $this->belongsTo(BankAccount::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function reconciledBy()
    {
        return $this->belongsTo(User::class, 'reconciled_by_id');
    }

    public function lines()
    {
        return $this->hasMany(BankStatementLine::class);
    }

    // Scopes
    public function scopeImported($query)
    {
        return $query->where('status', 'imported');
    }

    public function scopeReconciling($query)
    {
        return $query->where('status', 'reconciling');
    }

    public function scopeReconciled($query)
    {
        return $query->where('status', 'reconciled');
    }
}
