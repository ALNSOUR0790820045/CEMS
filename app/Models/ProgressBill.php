<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProgressBill extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'bill_number',
        'project_id',
        'contract_id',
        'period_from',
        'period_to',
        'bill_date',
        'bill_type',
        'bill_sequence',
        'previous_bill_id',
        'gross_amount',
        'previous_amount',
        'current_amount',
        'retention_percentage',
        'retention_amount',
        'cumulative_retention',
        'advance_recovery_percentage',
        'advance_recovery_amount',
        'other_deductions',
        'deduction_remarks',
        'net_amount',
        'vat_percentage',
        'vat_amount',
        'total_payable',
        'currency_id',
        'status',
        'prepared_by_id',
        'reviewed_by_id',
        'certified_by_id',
        'approved_by_id',
        'submitted_at',
        'certified_at',
        'approved_at',
        'paid_at',
        'payment_reference',
        'rejection_reason',
        'notes',
        'company_id',
    ];

    protected $casts = [
        'period_from' => 'date',
        'period_to' => 'date',
        'bill_date' => 'date',
        'gross_amount' => 'decimal:2',
        'previous_amount' => 'decimal:2',
        'current_amount' => 'decimal:2',
        'retention_percentage' => 'decimal:2',
        'retention_amount' => 'decimal:2',
        'cumulative_retention' => 'decimal:2',
        'advance_recovery_percentage' => 'decimal:2',
        'advance_recovery_amount' => 'decimal:2',
        'other_deductions' => 'decimal:2',
        'net_amount' => 'decimal:2',
        'vat_percentage' => 'decimal:2',
        'vat_amount' => 'decimal:2',
        'total_payable' => 'decimal:2',
        'submitted_at' => 'datetime',
        'certified_at' => 'datetime',
        'approved_at' => 'datetime',
        'paid_at' => 'datetime',
    ];

    // Relationships
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function contract(): BelongsTo
    {
        return $this->belongsTo(Contract::class);
    }

    public function previousBill(): BelongsTo
    {
        return $this->belongsTo(ProgressBill::class, 'previous_bill_id');
    }

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function preparedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'prepared_by_id');
    }

    public function reviewedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by_id');
    }

    public function certifiedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'certified_by_id');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(ProgressBillItem::class);
    }

    public function variations(): HasMany
    {
        return $this->hasMany(ProgressBillVariation::class);
    }

    public function deductions(): HasMany
    {
        return $this->hasMany(ProgressBillDeduction::class);
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(ProgressBillAttachment::class);
    }

    public function measurementSheets(): HasMany
    {
        return $this->hasMany(MeasurementSheet::class);
    }

    public function approvalWorkflow(): HasMany
    {
        return $this->hasMany(BillApprovalWorkflow::class);
    }

    // Business logic methods
    public function calculateAmounts(): void
    {
        // Calculate current amount from items
        $this->current_amount = $this->items()->sum('current_amount');
        $this->gross_amount = $this->items()->sum('cumulative_amount');
        
        // Calculate retention
        $this->retention_amount = $this->current_amount * ($this->retention_percentage / 100);
        
        // Calculate advance recovery
        $this->advance_recovery_amount = $this->current_amount * ($this->advance_recovery_percentage / 100);
        
        // Calculate net amount
        $this->net_amount = $this->current_amount 
            - $this->retention_amount 
            - $this->advance_recovery_amount 
            - $this->other_deductions;
        
        // Calculate VAT
        $this->vat_amount = $this->net_amount * ($this->vat_percentage / 100);
        
        // Calculate total payable
        $this->total_payable = $this->net_amount + $this->vat_amount;
        
        $this->save();
    }

    public function generateBillNumber(): string
    {
        $year = date('Y');
        $lastBill = static::where('bill_number', 'like', "PB-{$year}-%")
            ->orderBy('bill_number', 'desc')
            ->first();
        
        if ($lastBill) {
            $lastNumber = (int) substr($lastBill->bill_number, -3);
            $newNumber = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '001';
        }
        
        return "PB-{$year}-{$newNumber}";
    }
}
