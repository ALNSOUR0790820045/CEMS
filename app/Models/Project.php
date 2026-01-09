<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'company_id',
        'name',
        'name_en',
        'code',
        'description',
        'location',
        'latitude',
        'longitude',
        'start_date',
        'end_date',
        'status',
        'budget',
        'manager_id',
        'is_active',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'budget' => 'decimal:2',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'is_active' => 'boolean',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function manager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    public function siteReceipts(): HasMany
    {
        return $this->hasMany(SiteReceipt::class);
    }

    public function purchaseOrders(): HasMany
    {
        return $this->hasMany(PurchaseOrder::class);
    }

    public function siteDiaries(): HasMany
    {
        return $this->hasMany(SiteDiary::class);
    }
}
