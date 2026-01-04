<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AlertRule extends Model
{
    use HasFactory;
    protected $fillable = [
        'rule_name',
        'rule_type',
        'trigger_condition',
        'notification_template',
        'target_users',
        'target_roles',
        'is_active',
        'company_id',
    ];

    protected $casts = [
        'trigger_condition' => 'array',
        'target_users' => 'array',
        'target_roles' => 'array',
        'is_active' => 'boolean',
    ];

    // Relationships
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForCompany($query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }
}
