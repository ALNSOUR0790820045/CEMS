<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProjectWbs extends Model
{
    protected $table = 'project_wbs';

    protected $fillable = [
        'project_id',
        'parent_id',
        'wbs_code',
        'name',
        'description',
        'level',
        'budget',
    ];

    protected $casts = [
        'level' => 'integer',
        'budget' => 'decimal:2',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(ProjectWbs::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(ProjectWbs::class, 'parent_id');
    }

    public function changeOrderItems(): HasMany
    {
        return $this->hasMany(ChangeOrderItem::class, 'wbs_id');
    }
}
