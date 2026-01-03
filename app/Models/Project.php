<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends Model
{
    use SoftDeletes;

    // Project Status Constants
    const STATUS_PLANNING = 'planning';

    const STATUS_ACTIVE = 'active';

    const STATUS_ON_HOLD = 'on_hold';

    const STATUS_COMPLETED = 'completed';

    const STATUS_CANCELLED = 'cancelled';

    protected $fillable = [
        'company_id',
        'code',
        'name',
        'name_en',
        'description',
        'department_id',
        'manager_id',
        'budget',
        'actual_cost',
        'start_date',
        'end_date',
        'status',
        'is_billable',
    ];

    protected $casts = [
        'budget' => 'decimal:2',
        'actual_cost' => 'decimal:2',
        'start_date' => 'date',
        'end_date' => 'date',
        'is_billable' => 'boolean',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function manager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function journalEntries(): HasMany
    {
        return $this->hasMany(JournalEntry::class);
    }
}
