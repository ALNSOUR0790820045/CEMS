<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tender extends Model
{
    protected $fillable = [
        'tender_number',
        'name',
        'description',
        'company_id',
        'estimated_value',
        'submission_deadline',
        'status',
    ];

    protected $casts = [
        'estimated_value' => 'decimal:2',
        'submission_deadline' => 'date',
    ];

    // Relationships
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function procurementPackages(): HasMany
    {
        return $this->hasMany(TenderProcurementPackage::class);
    }

    public function longLeadItems(): HasMany
    {
        return $this->hasMany(TenderLongLeadItem::class);
    }
}
