<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssetRevaluation extends Model
{
    protected $fillable = [
        'revaluation_number',
        'fixed_asset_id',
        'revaluation_date',
        'old_value',
        'new_value',
        'revaluation_surplus_deficit',
        'reason',
        'appraiser_name',
        'status',
        'approved_by_id',
        'approved_at',
        'gl_journal_entry_id',
        'notes',
        'company_id',
    ];

    protected $casts = [
        'revaluation_date' => 'date',
        'old_value' => 'decimal:2',
        'new_value' => 'decimal:2',
        'revaluation_surplus_deficit' => 'decimal:2',
        'approved_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($revaluation) {
            if (empty($revaluation->revaluation_number)) {
                $revaluation->revaluation_number = static::generateRevaluationNumber();
            }
            
            // Calculate surplus/deficit
            if (is_null($revaluation->revaluation_surplus_deficit)) {
                $revaluation->revaluation_surplus_deficit = $revaluation->new_value - $revaluation->old_value;
            }
        });
    }

    public static function generateRevaluationNumber(): string
    {
        $year = date('Y');
        $prefix = "REV-{$year}-";
        
        $lastRevaluation = static::where('revaluation_number', 'like', $prefix . '%')
            ->orderBy('revaluation_number', 'desc')
            ->first();
        
        if ($lastRevaluation) {
            $lastNumber = (int) substr($lastRevaluation->revaluation_number, -4);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }
        
        return $prefix . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }

    // Relationships
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function fixedAsset(): BelongsTo
    {
        return $this->belongsTo(FixedAsset::class);
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by_id');
    }

    public function glJournalEntry(): BelongsTo
    {
        return $this->belongsTo(GLJournalEntry::class, 'gl_journal_entry_id');
    }

    // Scopes
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

    public function scopeByAsset($query, $assetId)
    {
        return $query->where('fixed_asset_id', $assetId);
    }

    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('revaluation_date', [$startDate, $endDate]);
    }

    // Helper methods
    public function isSurplus(): bool
    {
        return $this->revaluation_surplus_deficit > 0;
    }

    public function isDeficit(): bool
    {
        return $this->revaluation_surplus_deficit < 0;
    }
}
