<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class JournalEntry extends Model
{
    use SoftDeletes;

    // Entry Type Constants
    const TYPE_MANUAL = 'manual';

    const TYPE_SYSTEM = 'system';

    const TYPE_ADJUSTMENT = 'adjustment';

    const TYPE_CLOSING = 'closing';

    // Entry Status Constants
    const STATUS_DRAFT = 'draft';

    const STATUS_POSTED = 'posted';

    const STATUS_APPROVED = 'approved';

    const STATUS_CANCELLED = 'cancelled';

    protected $fillable = [
        'company_id',
        'entry_number',
        'entry_date',
        'type',
        'description',
        'reference',
        'project_id',
        'department_id',
        'created_by',
        'approved_by',
        'approved_at',
        'status',
    ];

    protected $casts = [
        'entry_date' => 'date',
        'approved_at' => 'datetime',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
