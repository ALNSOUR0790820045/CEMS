<?php

namespace App\Models;

use Spatie\Permission\Models\Role as SpatieRole;

class Role extends SpatieRole
{
    protected $fillable = [
        'name',
        'guard_name',
        'company_id',
    ];

    protected $casts = [
        'company_id' => 'integer',
    ];

    /**
     * Get the company that owns the role.
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
