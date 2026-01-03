<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeLoan extends Model
{
    protected $fillable = [
        'employee_id',
        'loan_date',
        'loan_amount',
        'installment_amount',
        'total_installments',
        'paid_installments',
        'status',
        'notes',
        'company_id',
    ];

    protected $casts = [
        'loan_date' => 'date',
        'loan_amount' => 'decimal:2',
        'installment_amount' => 'decimal:2',
    ];

    // Relationships
    public function employee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'employee_id');
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    // Accessors
    public function getRemainingBalanceAttribute(): float
    {
        return round($this->loan_amount - ($this->installment_amount * $this->paid_installments), 2);
    }

    public function getRemainingInstallmentsAttribute(): int
    {
        return max(0, $this->total_installments - $this->paid_installments);
    }
}
