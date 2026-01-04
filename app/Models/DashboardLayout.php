<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DashboardLayout extends Model
{
    protected $fillable = [
        'user_id',
        'dashboard_type',
        'layout_config',
    ];

    protected $casts = [
        'layout_config' => 'array',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
