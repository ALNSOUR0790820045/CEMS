<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    protected $fillable = [
        'name',
        'email',
        'phone',
        'department_id',
        'company_id',
    ];

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function materialRequests()
    {
        return $this->hasMany(MaterialRequest::class, 'requested_by_id');
    }
}
