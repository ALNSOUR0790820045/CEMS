<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SubcontractorIpc extends Model
{
    protected $fillable = [
        'ipc_number',
        'ipc_date',
        'period_from',
        'period_to',
        'subcontractor_agreement_id',
        'subcontractor_id',
        'project_id',
        'ipc_type',
        'current_work_value',
        'previous_cumulative',
        'materials_on_site',
        'previous_advance_payment',
        'current_advance_deduction',
        'retention_percentage',
        'previous_back_charges',
        'current_back_charges',
        'currency_id',
        'status',
        'submitted_by_subcontractor',
        'submitted_at',
        'reviewed_by_id',
        'reviewed_at',
        'review_notes',
        'approved_by_id',
        'approved_at',
        'payment_id',
        'paid_amount',
        'paid_at',
        'attachment_path',
        'notes',
        'company_id',
        'created_by_id',
    ];

    protected $casts = [
        'ipc_date' => 'date',
        'period_from' => 'date',
        'period_to' => 'date',
        'current_work_value' => 'decimal:2',
        'previous_cumulative' => 'decimal:2',
        'materials_on_site' => 'decimal:2',
        'previous_advance_payment' => 'decimal:2',
        'current_advance_deduction' => 'decimal:2',
        'retention_percentage' => 'decimal:2',
        'previous_back_charges' => 'decimal:2',
        'current_back_charges' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'submitted_by_subcontractor' => 'boolean',
        'submitted_at' => 'datetime',
        'reviewed_at' => 'datetime',
        'approved_at' => 'datetime',
        'paid_at' => 'datetime',
    ];

    protected $appends = [
        'cumulative_to_date',
        'gross_amount',
        'cumulative_advance_deduction',
        'current_retention',
        'cumulative_retention',
        'cumulative_back_charges',
        'net_amount',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($ipc) {
            if (empty($ipc->ipc_number)) {
                $ipc->ipc_number = self::generateNumber();
            }
        });
    }

    public static function generateNumber(): string
    {
        $year = date('Y');
        $lastNumber = self::where('ipc_number', 'like', "SIPC-{$year}-%")
            ->orderBy('ipc_number', 'desc')
            ->first();

        if ($lastNumber) {
            $lastNum = (int) substr($lastNumber->ipc_number, -4);
            $newNum = str_pad($lastNum + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNum = '0001';
        }

        return "SIPC-{$year}-{$newNum}";
    }

    // Relationships
    public function agreement(): BelongsTo
    {
        return $this->belongsTo(SubcontractorAgreement::class, 'subcontractor_agreement_id');
    }

    public function subcontractor(): BelongsTo
    {
        return $this->belongsTo(Subcontractor::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }

    public function reviewedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by_id');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by_id');
    }

    public function payment(): BelongsTo
    {
        return $this->belongsTo(ApPayment::class, 'payment_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(SubcontractorIpcItem::class);
    }

    // Computed Attributes
    public function getCumulativeToDateAttribute(): float
    {
        return (float) $this->current_work_value + (float) $this->previous_cumulative;
    }

    public function getGrossAmountAttribute(): float
    {
        return (float) $this->current_work_value + (float) $this->materials_on_site;
    }

    public function getCumulativeAdvanceDeductionAttribute(): float
    {
        return (float) $this->current_advance_deduction + (float) $this->previous_advance_payment;
    }

    public function getCurrentRetentionAttribute(): float
    {
        return (float) $this->current_work_value * ((float) $this->retention_percentage / 100);
    }

    public function getCumulativeRetentionAttribute(): float
    {
        $previousRetention = $this->agreement->ipcs()
            ->where('id', '<', $this->id)
            ->sum('current_work_value') * ((float) $this->retention_percentage / 100);
        
        return $this->current_retention + $previousRetention;
    }

    public function getCumulativeBackChargesAttribute(): float
    {
        return (float) $this->current_back_charges + (float) $this->previous_back_charges;
    }

    public function getNetAmountAttribute(): float
    {
        return $this->gross_amount 
            - $this->current_advance_deduction 
            - $this->current_retention 
            - $this->current_back_charges;
    }

    // Scopes
    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }
}
