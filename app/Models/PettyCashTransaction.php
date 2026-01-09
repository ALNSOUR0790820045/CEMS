<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class PettyCashTransaction extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'transaction_number',
        'transaction_date',
        'petty_cash_account_id',
        'transaction_type',
        'amount',
        'description',
        'expense_category_id',
        'cost_center_id',
        'project_id',
        'receipt_number',
        'receipt_date',
        'payee_name',
        'status',
        'requested_by_id',
        'approved_by_id',
        'approved_at',
        'posted_by_id',
        'posted_at',
        'gl_journal_entry_id',
        'attachment_path',
        'notes',
        'company_id',
    ];

    protected $casts = [
        'transaction_date' => 'date',
        'receipt_date' => 'date',
        'amount' => 'decimal:2',
        'approved_at' => 'datetime',
        'posted_at' => 'datetime',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function pettyCashAccount(): BelongsTo
    {
        return $this->belongsTo(PettyCashAccount::class);
    }

    public function expenseCategory(): BelongsTo
    {
        return $this->belongsTo(ExpenseCategory::class);
    }

    public function costCenter(): BelongsTo
    {
        return $this->belongsTo(CostCenter::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function requestedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by_id');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by_id');
    }

    public function postedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'posted_by_id');
    }

    public function glJournalEntry(): BelongsTo
    {
        return $this->belongsTo(GLJournalEntry::class);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('transaction_type', $type);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopePosted($query)
    {
        return $query->where('status', 'posted');
    }
}
