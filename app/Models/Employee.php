<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Employee Model - Placeholder
 * This is a placeholder model for future Employee module implementation.
 * The fillable array will be populated when the Employee module is fully implemented.
 */
class Employee extends Model
{
    protected $fillable = [];

    // Relationships
    public function department()
    {
        return $this->belongsTo(Department::class);
    }
}
