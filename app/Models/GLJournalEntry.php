<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class GLJournalEntry extends Model
{
    use SoftDeletes;

    protected $table = 'gl_journal_entries';

    protected $fillable = [
        'journal_number',
        'entry_date',
        'posting_date',
        'journal_type',
        'reference_type',
        'reference_id',
        'reference_number',
        'description',
        'total_debit',
        'total_credit',
        'currency_id',
        'exchange_rate',
        'status',
        'created_by_id',
        'approved_by_id',
        'posted_by_id',
        'approved_at',
        'posted_at',
        'reversed_from_id',
        'reversed_by_id',
        'project_id',
        'department_id',
        'attachment',
        'company_id',
    ];

    protected $casts = [
        'entry_date' => 'date',
        'posting_date' => 'date',
        'total_debit' => 'decimal:2',
        'total_credit' => 'decimal:2',
        'exchange_rate' => 'decimal:4',
        'approved_at' => 'datetime',
        'posted_at' => 'datetime',
    ];

    /**
     * Get the company that owns the journal entry.
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the currency for the journal entry.
     */
    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }

    /**
     * Get the user who created the journal entry.
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }

    /**
     * Get the user who approved the journal entry.
     */
    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by_id');
    }

    /**
     * Get the user who posted the journal entry.
     */
    public function postedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'posted_by_id');
    }

    /**
     * Get the project for the journal entry.
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Get the department for the journal entry.
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Get the lines for the journal entry.
     */
    public function lines(): HasMany
    {
        return $this->hasMany(GLJournalEntryLine::class, 'journal_entry_id');
    }

    /**
     * Get the journal entry that this entry reversed.
     */
    public function reversedFrom(): BelongsTo
    {
        return $this->belongsTo(GLJournalEntry::class, 'reversed_from_id');
    }

    /**
     * Get the journal entry that reversed this entry.
     */
    public function reversedBy(): BelongsTo
    {
        return $this->belongsTo(GLJournalEntry::class, 'reversed_by_id');
    }

    /**
     * Get the reference model (polymorphic).
     */
    public function reference(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Check if the journal entry is balanced.
     */
    public function getIsBalancedAttribute(): bool
    {
        return bccomp($this->total_debit, $this->total_credit, 2) === 0;
    }

    /**
     * Get the difference between debit and credit.
     */
    public function getDifferenceAttribute(): string
    {
        return bcsub($this->total_debit, $this->total_credit, 2);
    }

    /**
     * Scope to filter by status.
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope to filter by type.
     */
    public function scopeByType($query, $type)
    {
        return $query->where('journal_type', $type);
    }

    /**
     * Scope to filter by date range.
     */
    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('entry_date', [$startDate, $endDate]);
    }

    /**
     * Scope to filter by project.
     */
    public function scopeByProject($query, $projectId)
    {
        return $query->where('project_id', $projectId);
    }

    /**
     * Scope to filter posted entries.
     */
    public function scopePosted($query)
    {
        return $query->where('status', 'posted');
    }

    /**
     * Scope to filter pending approval entries.
     */
    public function scopePendingApproval($query)
    {
        return $query->where('status', 'pending_approval');
    }

    /**
     * Scope to filter draft entries.
     */
    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }
}
