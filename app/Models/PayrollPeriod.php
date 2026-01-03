<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PayrollPeriod extends Model
{
    use HasFactory;
    protected $fillable = [
        'period_name',
        'period_type',
        'start_date',
        'end_date',
        'payment_date',
        'status',
        'total_gross',
        'total_deductions',
        'total_net',
        'company_id',
        'calculated_by_id',
        'approved_by_id',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'payment_date' => 'date',
        'total_gross' => 'decimal:2',
        'total_deductions' => 'decimal:2',
        'total_net' => 'decimal:2',
    ];

    // Relationships
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function calculatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'calculated_by_id');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by_id');
    }

    public function entries(): HasMany
    {
        return $this->hasMany(PayrollEntry::class);
    }

    // Business Logic
    public function canCalculate(): bool
    {
        return $this->status === 'open';
    }

    public function canApprove(): bool
    {
        return $this->status === 'calculated';
    }

    public function canPay(): bool
    {
        return $this->status === 'approved';
    }

    public function calculate(User $calculatedBy): void
    {
        if (!$this->canCalculate()) {
            throw new \Exception('Payroll period cannot be calculated in current status');
        }

        $this->entries()->each(function (PayrollEntry $entry) {
            $entry->calculateTotals();
        });

        $this->recalculateTotals();
        $this->status = 'calculated';
        $this->calculated_by_id = $calculatedBy->id;
        $this->save();
    }

    public function approve(User $approvedBy): void
    {
        if (!$this->canApprove()) {
            throw new \Exception('Payroll period cannot be approved in current status');
        }

        $this->status = 'approved';
        $this->approved_by_id = $approvedBy->id;
        $this->save();

        $this->entries()->update(['status' => 'approved']);
    }

    public function recalculateTotals(): void
    {
        $this->total_gross = $this->entries()->sum('gross_salary');
        $this->total_deductions = $this->entries()->sum('total_deductions');
        $this->total_net = $this->entries()->sum('net_salary');
        $this->save();
    }
}
