<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DashboardWidget extends Model
{
    protected $fillable = [
        'dashboard_id',
        'widget_type',
        'title',
        'data_source',
        'config',
        'position_x',
        'position_y',
        'width',
        'height',
        'refresh_interval',
        'is_visible',
    ];

    protected $casts = [
        'config' => 'array',
        'is_visible' => 'boolean',
        'position_x' => 'integer',
        'position_y' => 'integer',
        'width' => 'integer',
        'height' => 'integer',
        'refresh_interval' => 'integer',
    ];

    public function dashboard(): BelongsTo
    {
        return $this->belongsTo(Dashboard::class);
    }

    public function scopeVisible($query)
    {
        return $query->where('is_visible', true);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('widget_type', $type);
    }
}
