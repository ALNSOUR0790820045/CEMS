<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssetDepreciation extends Model
{
    protected $fillable = [
        'fixed_asset_id',
        'depreciation_date',
        'period_month',
        'period_year',
        'depreciation_amount',
        'accumulated_depreciation',
        'net_book_value',
        'gl_journal_entry_id',
        'is_posted',
        'posted_by_id',
        'posted_at',
        'company_id',
    ];

    protected $casts = [
        'depreciation_date' => 'date',
        'period_month' => 'integer',
        'period_year' => 'integer',
        'depreciation_amount' => 'decimal:2',
        'accumulated_depreciation' => 'decimal:2',
        'net_book_value' => 'decimal:2',
        'is_posted' => 'boolean',
        'posted_at' => 'datetime',
    ];

    // Relationships
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function fixedAsset(): BelongsTo
    {
        return $this->belongsTo(FixedAsset::class);
    }

    public function glJournalEntry(): BelongsTo
    {
        return $this->belongsTo(GLJournalEntry::class, 'gl_journal_entry_id');
    }

    public function postedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'posted_by_id');
    }

    // Scopes
    public function scopePosted($query)
    {
        return $query->where('is_posted', true);
    }

    public function scopeUnposted($query)
    {
        return $query->where('is_posted', false);
    }

    public function scopeByPeriod($query, $month, $year)
    {
        return $query->where('period_month', $month)
                     ->where('period_year', $year);
    }

    public function scopeByYear($query, $year)
    {
        return $query->where('period_year', $year);
    }

    public function scopeByAsset($query, $assetId)
    {
        return $query->where('fixed_asset_id', $assetId);
    }
}
