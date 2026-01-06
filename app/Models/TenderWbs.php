<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TenderWbs extends Model
{
    protected $table = 'tender_wbs';

    protected $fillable = [
        'tender_id',
        'wbs_code',
        'name',
        'description',
        'parent_wbs_id',
        'level',
        'sequence_order',
    ];

    protected $casts = [
        'level' => 'integer',
        'sequence_order' => 'integer',
    ];

    public function tender()
    {
        return $this->belongsTo(Tender::class);
    }

    public function parent()
    {
        return $this->belongsTo(TenderWbs::class, 'parent_wbs_id');
    }

    public function children()
    {
        return $this->hasMany(TenderWbs::class, 'parent_wbs_id');
    }

    public function activities()
    {
        return $this->hasMany(TenderActivity::class, 'wbs_id');
    }
}
