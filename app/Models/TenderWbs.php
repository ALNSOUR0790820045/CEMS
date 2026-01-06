<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TenderWBS extends Model
{
    protected $table = 'tender_wbs';

    protected $fillable = [
        'tender_id',
        'parent_id',
        'wbs_code',
        'name',
        'name_en',
        'description',
        'level',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'level' => 'integer',
        'sort_order' => 'integer',
    ];

    // Relationships
    public function tender(): BelongsTo
    {
        return $this->belongsTo(Tender::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(TenderWBS::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(TenderWBS::class, 'parent_id');
    }

    public function activities(): HasMany
    {
        return $this->hasMany(TenderActivity::class);
    }
}
