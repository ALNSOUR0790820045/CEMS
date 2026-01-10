<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CorrespondenceTemplate extends Model
{
    protected $fillable = [
        'name',
        'name_en',
        'type',
        'category',
        'subject_template',
        'content_template',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeIncoming($query)
    {
        return $query->where('type', 'incoming');
    }

    public function scopeOutgoing($query)
    {
        return $query->where('type', 'outgoing');
    }
}
