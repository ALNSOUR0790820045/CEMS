<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectWbs extends Model
{
    protected $table = 'project_wbs';

    protected $fillable = [
        'project_id',
        'parent_id',
        'wbs_code',
        'name',
        'name_en',
        'description',
        'level',
        'order',
    ];

    protected $casts = [
        'level' => 'integer',
        'order' => 'integer',
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

    public function activities()
    {
        return $this->hasMany(ProjectActivity::class, 'wbs_id');
    }

    // Helper method to get full WBS path
    public function getFullPath()
    {
        $path = [$this->name];
        $parent = $this->parent;
        
        while ($parent) {
            array_unshift($path, $parent->name);
            $parent = $parent->parent;
        }
        
        return implode(' > ', $path);
    }
}
