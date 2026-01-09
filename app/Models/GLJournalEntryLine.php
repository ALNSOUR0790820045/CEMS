<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GLJournalEntryLine extends Model
{
    protected $table = 'gl_journal_entry_lines';

    protected $fillable = [
        'journal_entry_id',
        'line_number',
        'gl_account_id',
        'debit_amount',
        'credit_amount',
        'description',
        'cost_center_id',
        'project_id',
        'currency_id',
        'exchange_rate',
        'base_currency_debit',
        'base_currency_credit',
        'company_id',
    ];

    protected $casts = [
        'line_number' => 'integer',
        'debit_amount' => 'decimal:2',
        'credit_amount' => 'decimal:2',
        'exchange_rate' => 'decimal:4',
        'base_currency_debit' => 'decimal:2',
        'base_currency_credit' => 'decimal:2',
    ];

    /**
     * Get the company that owns the journal entry line.
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the journal entry for this line.
     */
    public function journalEntry(): BelongsTo
    {
        return $this->belongsTo(GLJournalEntry::class, 'journal_entry_id');
    }

    /**
     * Get the GL account for this line.
     */
    public function glAccount(): BelongsTo
    {
        return $this->belongsTo(GLAccount::class);
    }

    /**
     * Get the cost center for this line.
     */
    public function costCenter(): BelongsTo
    {
        return $this->belongsTo(CostCenter::class);
    }

    /**
     * Get the project for this line.
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Get the currency for this line.
     */
    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }
}
