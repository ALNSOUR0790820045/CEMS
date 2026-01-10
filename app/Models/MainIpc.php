<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
<<<<<<< HEAD
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MainIpc extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'project_id',
        'ipc_number',
        'ipc_date',
        'period_from',
        'period_to',
        'amount',
        'previous_total',
        'current_total',
        'status',
        'notes',
    ];

    protected $casts = [
        'ipc_date' => 'date',
        'period_from' => 'date',
        'period_to' => 'date',
        'amount' => 'decimal:2',
        'previous_total' => 'decimal:2',
        'current_total' => 'decimal:2',
    ];

    // Relationships
    public function project(): BelongsTo
=======

class MainIpc extends Model
{
    protected $fillable = [
        'project_id',
        'ipc_number',
        'ipc_sequence',
        'period_from',
        'period_to',
        'submission_date',
        'previous_cumulative',
        'current_period_work',
        'current_cumulative',
        'approved_change_orders',
        'retention_percent',
        'retention_amount',
        'advance_payment_deduction',
        'other_deductions',
        'deductions_notes',
        'tax_rate',
        'tax_amount',
        'net_payable',
        'status',
        'pm_prepared_by',
        'pm_prepared_at',
        'pm_notes',
        'technical_reviewed_by',
        'technical_reviewed_at',
        'technical_decision',
        'technical_comments',
        'consultant_reviewed_by',
        'consultant_submission_date',
        'consultant_due_date',
        'consultant_reviewed_at',
        'consultant_decision',
        'consultant_approved_amount',
        'consultant_comments',
        'consultant_review_days',
        'client_approved_by',
        'client_submission_date',
        'client_due_date',
        'client_approved_at',
        'client_decision',
        'client_approved_amount',
        'client_comments',
        'client_review_days',
        'finance_reviewed_by',
        'finance_reviewed_at',
        'finance_decision',
        'finance_comments',
        'paid_by',
        'payment_date',
        'payment_reference',
        'paid_amount',
        'attachments',
    ];

    protected $casts = [
        'period_from' => 'date',
        'period_to' => 'date',
        'submission_date' => 'date',
        'previous_cumulative' => 'decimal:2',
        'current_period_work' => 'decimal:2',
        'current_cumulative' => 'decimal:2',
        'approved_change_orders' => 'decimal:2',
        'retention_percent' => 'decimal:2',
        'retention_amount' => 'decimal:2',
        'advance_payment_deduction' => 'decimal:2',
        'other_deductions' => 'decimal:2',
        'tax_rate' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'net_payable' => 'decimal:2',
        'pm_prepared_at' => 'datetime',
        'technical_reviewed_at' => 'datetime',
        'consultant_submission_date' => 'date',
        'consultant_due_date' => 'date',
        'consultant_reviewed_at' => 'datetime',
        'consultant_approved_amount' => 'decimal:2',
        'consultant_review_days' => 'integer',
        'client_submission_date' => 'date',
        'client_due_date' => 'date',
        'client_approved_at' => 'datetime',
        'client_approved_amount' => 'decimal:2',
        'client_review_days' => 'integer',
        'finance_reviewed_at' => 'datetime',
        'payment_date' => 'date',
        'paid_amount' => 'decimal:2',
        'attachments' => 'array',
    ];

    // Relationships
    public function project()
>>>>>>> origin/main
    {
        return $this->belongsTo(Project::class);
    }

<<<<<<< HEAD
    public function priceEscalationCalculations(): HasMany
    {
        return $this->hasMany(PriceEscalationCalculation::class);
=======
    public function items()
    {
        return $this->hasMany(MainIpcItem::class);
    }

    public function pmPreparer()
    {
        return $this->belongsTo(User::class, 'pm_prepared_by');
    }

    public function technicalReviewer()
    {
        return $this->belongsTo(User::class, 'technical_reviewed_by');
    }

    public function consultantReviewer()
    {
        return $this->belongsTo(User::class, 'consultant_reviewed_by');
    }

    public function clientApprover()
    {
        return $this->belongsTo(User::class, 'client_approved_by');
    }

    public function financeReviewer()
    {
        return $this->belongsTo(User::class, 'finance_reviewed_by');
    }

    public function paidByUser()
    {
        return $this->belongsTo(User::class, 'paid_by');
    }

    // Calculations
    public function calculateCurrentCumulative()
    {
        $this->current_cumulative = $this->previous_cumulative + $this->current_period_work + $this->approved_change_orders;
    }

    public function calculateRetentionAmount()
    {
        $this->retention_amount = $this->current_cumulative * ($this->retention_percent / 100);
    }

    public function calculateTaxAmount()
    {
        $taxableAmount = $this->current_cumulative - $this->retention_amount;
        $this->tax_amount = $taxableAmount * ($this->tax_rate / 100);
    }

    public function calculateNetPayable()
    {
        $this->net_payable = $this->current_cumulative 
            - $this->retention_amount 
            - $this->advance_payment_deduction 
            - $this->other_deductions 
            + $this->tax_amount;
    }

    public function recalculate()
    {
        $this->calculateCurrentCumulative();
        $this->calculateRetentionAmount();
        $this->calculateTaxAmount();
        $this->calculateNetPayable();
    }

    // Status helpers
    public function getStatusBadgeAttribute()
    {
        $statusMap = [
            'draft' => ['label' => 'مسودة', 'color' => 'gray'],
            'pending_pm' => ['label' => 'معلق - مدير المشروع', 'color' => 'yellow'],
            'pending_technical' => ['label' => 'معلق - المدير الفني', 'color' => 'yellow'],
            'pending_consultant' => ['label' => 'معلق - الاستشاري', 'color' => 'blue'],
            'pending_client' => ['label' => 'معلق - العميل', 'color' => 'blue'],
            'pending_finance' => ['label' => 'معلق - المالية', 'color' => 'orange'],
            'approved_for_payment' => ['label' => 'جاهز للدفع', 'color' => 'green'],
            'paid' => ['label' => 'تم الدفع', 'color' => 'green'],
            'rejected' => ['label' => 'مرفوض', 'color' => 'red'],
            'on_hold' => ['label' => 'معلق', 'color' => 'gray'],
        ];

        return $statusMap[$this->status] ?? ['label' => $this->status, 'color' => 'gray'];
    }

    public function getApprovalProgressAttribute()
    {
        $stages = [
            'draft' => 0,
            'pending_pm' => 0,
            'pending_technical' => 17,
            'pending_consultant' => 34,
            'pending_client' => 51,
            'pending_finance' => 68,
            'approved_for_payment' => 85,
            'paid' => 100,
        ];

        return $stages[$this->status] ?? 0;
    }

    // Days calculation
    public function getConsultantDaysRemainingAttribute()
    {
        if (!$this->consultant_due_date || $this->consultant_reviewed_at) {
            return null;
        }
        return now()->diffInDays($this->consultant_due_date, false);
    }

    public function getClientDaysRemainingAttribute()
    {
        if (!$this->client_due_date || $this->client_approved_at) {
            return null;
        }
        return now()->diffInDays($this->client_due_date, false);
    }

    public function getIsOverdueAttribute()
    {
        if ($this->status === 'pending_consultant' && $this->consultant_days_remaining < 0) {
            return true;
        }
        if ($this->status === 'pending_client' && $this->client_days_remaining < 0) {
            return true;
        }
        return false;
    }

    // Scopes
    public function scopeOverdue($query)
    {
        return $query->where(function($q) {
            $q->where('status', 'pending_consultant')
              ->where('consultant_due_date', '<', now())
              ->whereNull('consultant_reviewed_at');
        })->orWhere(function($q) {
            $q->where('status', 'pending_client')
              ->where('client_due_date', '<', now())
              ->whereNull('client_approved_at');
        });
    }

    public function scopePending($query)
    {
        return $query->whereIn('status', [
            'pending_pm',
            'pending_technical',
            'pending_consultant',
            'pending_client',
            'pending_finance',
        ]);
    }

    public function scopeApproved($query)
    {
        return $query->whereIn('status', ['approved_for_payment', 'paid']);
>>>>>>> origin/main
    }
}
