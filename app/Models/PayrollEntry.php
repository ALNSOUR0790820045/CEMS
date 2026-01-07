<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PayrollEntry extends Model
{
    use HasFactory;
    protected $fillable = [
        'payroll_period_id',
        'employee_id',
        'basic_salary',
        'total_allowances',
        'total_deductions',
        'days_worked',
        'days_absent',
        'overtime_hours',
        'overtime_amount',
        'status',
        'payment_method',
        'bank_account_id',
        'payment_date',
        'notes',
        'company_id',
    ];

    protected $casts = [
        'basic_salary' => 'decimal:2',
        'total_allowances' => 'decimal:2',
        'total_deductions' => 'decimal:2',
        'overtime_hours' => 'decimal:2',
        'overtime_amount' => 'decimal:2',
        'payment_date' => 'date',
    ];

    protected $appends = [
        'gross_salary',
        'net_salary',
    ];

    // Relationships
    public function payrollPeriod(): BelongsTo
    {
        return $this->belongsTo(PayrollPeriod::class);
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'employee_id');
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function bankAccount(): BelongsTo
    {
        return $this->belongsTo(BankAccount::class);
    }

    public function allowances(): HasMany
    {
        return $this->hasMany(PayrollAllowance::class);
    }

    public function deductions(): HasMany
    {
        return $this->hasMany(PayrollDeduction::class);
    }

    // Business Logic
    public function calculateTotals(): void
    {
        // Calculate total allowances
        $this->total_allowances = $this->allowances()->sum('amount');

        // Calculate total deductions
        $this->total_deductions = $this->deductions()->sum('amount');

        // Add loan installments if any
        $this->addLoanDeductions();

        $this->status = 'calculated';
        $this->save();
    }

    protected function addLoanDeductions(): void
    {
        $activeLoans = EmployeeLoan::where('employee_id', $this->employee_id)
            ->where('status', 'active')
            ->where('paid_installments', '<', DB::raw('total_installments'))
            ->get();

        foreach ($activeLoans as $loan) {
            // Check if deduction already exists for this loan
            $exists = $this->deductions()
                ->where('deduction_type', 'loan')
                ->where('deduction_name', 'LIKE', '%Loan #' . $loan->id . '%')
                ->exists();

            if (!$exists) {
                DB::transaction(function () use ($loan) {
                    $this->deductions()->create([
                        'deduction_type' => 'loan',
                        'deduction_name' => 'Loan Installment #' . $loan->id,
                        'amount' => $loan->installment_amount,
                    ]);

                    // Update loan paid installments
                    $loan->increment('paid_installments');

                    if ($loan->paid_installments >= $loan->total_installments) {
                        $loan->update(['status' => 'completed']);
                    }
                });
            }
        }
    }

    // Accessors
    public function getGrossSalaryAttribute(): float
    {
        return round($this->basic_salary + $this->total_allowances, 2);
    }

    public function getNetSalaryAttribute(): float
    {
        return round($this->basic_salary + $this->total_allowances - $this->total_deductions, 2);
    }
}
