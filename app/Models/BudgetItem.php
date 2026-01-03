<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BudgetItem extends Model
{
    protected $fillable = [
        'budget_id',
        'gl_account_id',
        'month',
        'budgeted_amount',
        'actual_amount',
    ];

    protected $casts = [
        'month' => 'integer',
        'budgeted_amount' => 'decimal:2',
        'actual_amount' => 'decimal:2',
        'variance' => 'decimal:2',
    ];

    protected $appends = ['variance'];

    // Relationships
    public function budget()
    {
        return $this->belongsTo(Budget::class);
    }

    public function glAccount()
    {
        return $this->belongsTo(GlAccount::class);
    }

    // Accessors
    public function getVarianceAttribute()
    {
        return $this->actual_amount - $this->budgeted_amount;
    }
}
