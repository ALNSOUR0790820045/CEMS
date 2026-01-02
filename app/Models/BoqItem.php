<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BoqItem extends Model
{
    protected $fillable = [
        'project_id',
        'wbs_id',
        'item_code',
        'description',
        'unit',
        'quantity',
        'unit_price',
        'total_price',
        'sort_order',
    ];

    protected $casts = [
        'quantity' => 'decimal:3',
        'unit_price' => 'decimal:2',
        'total_price' => 'decimal:2',
        'sort_order' => 'integer',
    ];

    // Relationships
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function wbs()
    {
        return $this->belongsTo(ProjectWbs::class, 'wbs_id');
    }

    public function ipcItems()
    {
        return $this->hasMany(MainIpcItem::class);
    }
}
