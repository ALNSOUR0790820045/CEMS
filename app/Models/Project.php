<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'company_id',
        'project_number',
        'name',
        'name_en',
        'description',
        'location',
        'start_date',
        'end_date',
        'contract_value',
        'status',
        'project_manager_id',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'contract_value' => 'decimal:2',
    ];

    // Relationships
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function projectManager()
    {
        return $this->belongsTo(User::class, 'project_manager_id');
    }

    public function wbs()
    {
        return $this->hasMany(ProjectWbs::class);
    }

    public function boqItems()
    {
        return $this->hasMany(BoqItem::class);
    }

    public function changeOrders()
    {
        return $this->hasMany(ChangeOrder::class);
    }

    public function mainIpcs()
    {
        return $this->hasMany(MainIpc::class);
    }
}
