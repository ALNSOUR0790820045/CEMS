<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssetDepreciation extends Model
{
    protected $table = 'asset_depreciation';

    protected $fillable = [
        'fixed_asset_id',
        'period_date',
        'depreciation_amount',
        'accumulated_depreciation',
        'book_value',
        'gl_journal_entry_id',
        'posted',
    ];

    protected $casts = [
        'period_date' => 'date',
        'depreciation_amount' => 'decimal:2',
        'accumulated_depreciation' => 'decimal:2',
        'book_value' => 'decimal:2',
        'posted' => 'boolean',
    ];

    // Relationships
    public function fixedAsset()
    {
        return $this->belongsTo(FixedAsset::class);
    }
}
