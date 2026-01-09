<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssetDisposal extends Model
{
    protected $fillable = [
        'disposal_number',
        'fixed_asset_id',
        'disposal_date',
        'disposal_type',
        'disposal_reason',
        'sale_price',
        'accumulated_depreciation_at_disposal',
        'net_book_value_at_disposal',
        'gain_loss',
        'buyer_name',
        'buyer_contact',
        'status',
        'approved_by_id',
        'approved_at',
        'gl_journal_entry_id',
        'notes',
        'company_id',
    ];

    protected $casts = [
        'disposal_date' => 'date',
        'sale_price' => 'decimal:2',
        'accumulated_depreciation_at_disposal' => 'decimal:2',
        'net_book_value_at_disposal' => 'decimal:2',
        'gain_loss' => 'decimal:2',
        'approved_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($disposal) {
            if (empty($disposal->disposal_number)) {
                $disposal->disposal_number = static::generateDisposalNumber();
            }
        });
    }

    public static function generateDisposalNumber(): string
    {
        $year = date('Y');
        $prefix = "DIS-{$year}-";
        
        $lastDisposal = static::where('disposal_number', 'like', $prefix . '%')
            ->orderBy('disposal_number', 'desc')
            ->first();
        
        if ($lastDisposal) {
            $lastNumber = (int) substr($lastDisposal->disposal_number, -4);
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

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeByType($query, $type)
    {
        return $query->where('disposal_type', $type);
    }

    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('disposal_date', [$startDate, $endDate]);
    }
}
