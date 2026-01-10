<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CorrespondenceRegister extends Model
{
    protected $fillable = [
        'register_number',
        'name',
        'type',
        'project_id',
        'year',
        'last_sequence',
        'prefix',
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

    public function generateReferenceNumber(): string
    {
        $this->increment('last_sequence');
        $sequence = str_pad($this->last_sequence, 4, '0', STR_PAD_LEFT);
        return "{$this->prefix}-{$this->year}-{$sequence}";
    }
}
