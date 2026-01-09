<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RiskRegister extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'register_number',
        'project_id',
        'name',
        'description',
        'version',
        'status',
        'prepared_by_id',
        'approved_by_id',
        'approved_at',
        'review_frequency',
        'last_review_date',
        'next_review_date',
        'company_id',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
        'last_review_date' => 'date',
        'next_review_date' => 'date',
    ];

    // Auto-generate register number
    protected static function booted()
    {
        static::creating(function ($register) {
            if (empty($register->register_number)) {
                $year = date('Y');
                $count = static::whereYear('created_at', $year)->count() + 1;
                $register->register_number = sprintf('RR-%s-%04d', $year, $count);
            }
        });
    }

    // Relationships
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function preparedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'prepared_by_id');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by_id');
    }

    public function risks(): HasMany
    {
        return $this->hasMany(Risk::class);
    }

    // Helper methods
    public function approve(User $user): void
    {
        $this->update([
            'approved_by_id' => $user->id,
            'approved_at' => now(),
            'status' => 'active',
        ]);
    }

    public function isApproved(): bool
    {
        return $this->status === 'active' && $this->approved_at !== null;
    }

    public function isOverdue(): bool
    {
        return $this->next_review_date && $this->next_review_date->isPast();
    }
}
