<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    protected $fillable = [
        'code',
        'name',
        'name_en',
        'parent_id',
        'type',
        'nature',
        'level',
        'is_parent',
        'is_active',
        'opening_balance',
        'current_balance',
        'description',
    ];

    protected $casts = [
        'is_parent' => 'boolean',
        'is_active' => 'boolean',
        'opening_balance' => 'decimal:2',
        'current_balance' => 'decimal:2',
    ];

    // Self-referential relationship - Parent
    public function parent()
    {
        return $this->belongsTo(Account::class, 'parent_id');
    }

    // Self-referential relationship - Children
    public function children()
    {
        return $this->hasMany(Account::class, 'parent_id');
    }

    // Get all descendants (children, grandchildren, etc.)
    public function descendants()
    {
        return $this->children()->with('descendants');
    }

    // Get full code with hierarchy (e.g., 1-1-001)
    public function getFullCode()
    {
        if ($this->parent) {
            return $this->parent->getFullCode() . '-' . $this->code;
        }
        return $this->code;
    }

    // Get hierarchy level
    public function getHierarchyLevel()
    {
        $level = 0;
        $parent = $this->parent;
        
        while ($parent) {
            $level++;
            $parent = $parent->parent;
        }
        
        return $level;
    }

    // Get breadcrumb path
    public function getBreadcrumb()
    {
        $breadcrumb = [];
        $current = $this;
        
        while ($current) {
            array_unshift($breadcrumb, $current);
            $current = $current->parent;
        }
        
        return $breadcrumb;
    }

    // Scope for root accounts (no parent)
    public function scopeRoots($query)
    {
        return $query->whereNull('parent_id');
    }

    // Scope for active accounts
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Scope for parent accounts
    public function scopeParents($query)
    {
        return $query->where('is_parent', true);
    }

    // Scope by type
    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }
}
