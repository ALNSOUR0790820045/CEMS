<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Guarantee extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'guarantee_number',
        'type',
        'bank_name',
        'amount',
        'currency',
        'issue_date',
        'expiry_date',
        'status',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'issue_date' => 'date',
        'expiry_date' => 'date',
    ];

    // Relationships
    public function performanceBondProjects()
    {
        return $this->hasMany(Project::class, 'performance_bond_id');
    }

    public function advanceBondProjects()
    {
        return $this->hasMany(Project::class, 'advance_bond_id');
    }
}
