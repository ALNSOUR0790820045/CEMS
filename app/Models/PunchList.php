<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PunchList extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'list_number',
        'project_id',
        'name',
        'description',
        'list_type',
        'area_zone',
        'building',
        'floor',
        'discipline',
        'contractor_id',
        'subcontractor_id',
        'inspection_date',
        'inspector_id',
        'consultant_rep',
        'contractor_rep',
        'total_items',
        'completed_items',
        'pending_items',
        'completion_percentage',
        'target_completion_date',
        'actual_completion_date',
        'status',
        'issued_by_id',
        'issued_at',
        'verified_by_id',
        'verified_at',
        'closed_by_id',
        'closed_at',
        'notes',
        'company_id',
    ];

    protected $casts = [
        'inspection_date' => 'date',
        'target_completion_date' => 'date',
        'actual_completion_date' => 'date',
        'issued_at' => 'datetime',
        'verified_at' => 'datetime',
        'closed_at' => 'datetime',
        'completion_percentage' => 'decimal:2',
        'total_items' => 'integer',
        'completed_items' => 'integer',
        'pending_items' => 'integer',
    ];

    // Relationships
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function contractor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class, 'contractor_id');
    }

    public function subcontractor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class, 'subcontractor_id');
    }

    public function inspector(): BelongsTo
    {
        return $this->belongsTo(User::class, 'inspector_id');
    }

    public function issuedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'issued_by_id');
    }

    public function verifiedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by_id');
    }

    public function closedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'closed_by_id');
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(PunchItem::class);
    }

    // Helper methods
    public function updateStatistics(): void
    {
        $this->total_items = $this->items()->count();
        $this->completed_items = $this->items()->whereIn('status', ['completed', 'verified'])->count();
        $this->pending_items = $this->items()->whereIn('status', ['open', 'in_progress'])->count();
        
        if ($this->total_items > 0) {
            $this->completion_percentage = ($this->completed_items / $this->total_items) * 100;
        } else {
            $this->completion_percentage = 0;
        }
        
        $this->save();
    }
}
