<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TimeBarProtectionSetting extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'company_id',
        'entity_type',
        'protection_days',
        'protection_type',
        'is_active',
        'description',
        'excluded_roles',
        'metadata',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'protection_days' => 'integer',
        'excluded_roles' => 'array',
        'metadata' => 'array',
    ];

    /**
     * Get the company that owns the time bar protection setting.
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Check if a record is protected based on its creation date.
     *
     * @param \Carbon\Carbon $recordDate
     * @return bool
     */
    public function isProtected($recordDate): bool
    {
        if (!$this->is_active) {
            return false;
        }

        $daysSinceCreation = now()->diffInDays($recordDate);
        return $daysSinceCreation >= $this->protection_days;
    }

    /**
     * Scope to filter by entity type.
     */
    public function scopeForEntity($query, string $entityType)
    {
        return $query->where('entity_type', $entityType)
                    ->where('is_active', true);
    }

    /**
     * Scope to filter by company.
     */
    public function scopeForCompany($query, int $companyId)
    {
        return $query->where('company_id', $companyId);
    }
}
