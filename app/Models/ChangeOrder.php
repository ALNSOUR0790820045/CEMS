<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChangeOrder extends Model
{
    protected $fillable = [
        'project_id',
        'co_number',
        'description',
        'amount',
        'type',
        'status',
        'approval_date',
        'approved_by',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'approval_date' => 'date',
    ];

    // Relationships
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // Scopes
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }
}
