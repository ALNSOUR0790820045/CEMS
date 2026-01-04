<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'project_number',
        'name',
        'name_en',
        'description',
        'company_id',
        'start_date',
        'end_date',
        'status',
        'budget',
        'currency',
        'location',
        'project_manager_id',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'budget' => 'decimal:2',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function projectManager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'project_manager_id');
    }

    public function contracts(): HasMany
    {
        return $this->hasMany(Contract::class);
    }

    public function correspondence(): HasMany
    {
        return $this->hasMany(Correspondence::class);
    }

    public function claims(): HasMany
    {
        return $this->hasMany(Claim::class);
    }

    public function variationOrders(): HasMany
    {
        return $this->hasMany(VariationOrder::class);
    }

    public function timeBarEvents(): HasMany
    {
        return $this->hasMany(TimeBarEvent::class);
    }

    public function timeBarSettings(): HasMany
    {
        return $this->hasMany(TimeBarSetting::class);
    }
}
