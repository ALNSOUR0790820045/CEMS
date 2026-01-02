<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectWbs extends Model
{
    protected $fillable = [
        'project_id',
        'parent_id',
        'wbs_code',
        'name',
        'description',
        'level',
        'sort_order',
    ];

    protected $casts = [
        'level' => 'integer',
        'sort_order' => 'integer',
    ];

    // Relationships
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function parent()
    {
        return $this->belongsTo(ProjectWbs::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(ProjectWbs::class, 'parent_id');
    }

    public function boqItems()
    {
        return $this->hasMany(BoqItem::class, 'wbs_id');
    }
}
