<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Dashboard extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'name_en',
        'description',
        'type',
        'layout',
        'is_default',
        'is_public',
        'created_by_id',
        'company_id',
    ];

    protected $casts = [
        'layout' => 'array',
        'is_default' => 'boolean',
        'is_public' => 'boolean',
    ];

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function widgets(): HasMany
    {
        return $this->hasMany(DashboardWidget::class);
    }

    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }
}
