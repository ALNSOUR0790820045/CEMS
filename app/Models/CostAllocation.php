<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class CostAllocation extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'allocation_number',
        'allocation_date',
        'cost_center_id',
        'cost_category_id',
        'project_id',
        'gl_account_id',
        'amount',
        'currency_id',
        'exchange_rate',
        'description',
        'reference_type',
        'reference_id',
        'status',
        'posted_by_id',
        'posted_at',
        'company_id',
    ];

    protected $casts = [
        'allocation_date' => 'date',
        'amount' => 'decimal:2',
        'exchange_rate' => 'decimal:4',
        'posted_at' => 'datetime',
    ];

    // Relationships
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function costCenter(): BelongsTo
    {
        return $this->belongsTo(CostCenter::class);
    }

    public function costCategory(): BelongsTo
    {
        return $this->belongsTo(CostCategory::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function glAccount(): BelongsTo
    {
        return $this->belongsTo(GLAccount::class);
    }

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }

    public function postedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'posted_by_id');
    }

    // Scopes
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByProject($query, $projectId)
    {
        return $query->where('project_id', $projectId);
    }

    public function scopeByCostCenter($query, $costCenterId)
    {
        return $query->where('cost_center_id', $costCenterId);
    }

    public function scopeByCompany($query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    // Helper methods
    public static function generateAllocationNumber()
    {
        $year = date('Y');
        $lastAllocation = static::whereYear('created_at', $year)
            ->orderBy('id', 'desc')
            ->first();

        if ($lastAllocation && preg_match('/CA-(\d{4})-(\d{4})/', $lastAllocation->allocation_number, $matches)) {
            $sequence = intval($matches[2]) + 1;
        } else {
            $sequence = 1;
        }

        return sprintf('CA-%s-%04d', $year, $sequence);
    }
}
