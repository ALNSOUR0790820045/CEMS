<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssetDisposal extends Model
{
    protected $fillable = [
        'fixed_asset_id',
        'disposal_date',
        'disposal_type',
        'disposal_amount',
        'buyer_name',
        'book_value_at_disposal',
        'gl_journal_entry_id',
        'notes',
        'company_id',
    ];

    protected $casts = [
        'disposal_date' => 'date',
        'disposal_amount' => 'decimal:2',
        'book_value_at_disposal' => 'decimal:2',
    ];

    protected $appends = ['gain_loss'];

    // Computed attribute for gain/loss
    public function getGainLossAttribute()
    {
        return ($this->disposal_amount ?? 0) - $this->book_value_at_disposal;
    }

    // Relationships
    public function fixedAsset()
    {
        return $this->belongsTo(FixedAsset::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
