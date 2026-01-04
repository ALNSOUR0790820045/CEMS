<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GLJournalEntry extends Model
{
    protected $table = 'gl_journal_entries';

    protected $fillable = [
        'entry_number',
        'entry_date',
        'description',
        'reference',
        'status',
        'company_id',
        'created_by_id',
    ];

    protected $casts = [
        'entry_date' => 'date',
    ];

    // Relationships
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }

    public function cashTransactions()
    {
        return $this->hasMany(CashTransaction::class, 'gl_journal_entry_id');
    }
}
