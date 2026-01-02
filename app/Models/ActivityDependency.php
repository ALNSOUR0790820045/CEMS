<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityDependency extends Model
{
    protected $fillable = [
        'predecessor_id',
        'successor_id',
        'type',
        'lag_days',
    ];

    protected $casts = [
        'lag_days' => 'integer',
    ];

    // Relationships
    public function predecessor()
    {
        return $this->belongsTo(ProjectActivity::class, 'predecessor_id');
    }

    public function successor()
    {
        return $this->belongsTo(ProjectActivity::class, 'successor_id');
    }

    // Validation to prevent circular dependencies
    public static function boot()
    {
        parent::boot();

        static::creating(function ($dependency) {
            // Prevent self-dependency
            if ($dependency->predecessor_id === $dependency->successor_id) {
                throw new \Exception('Activity cannot depend on itself');
            }

            // Check for circular dependencies
            if (self::hasCircularDependency($dependency->predecessor_id, $dependency->successor_id)) {
                throw new \Exception('Circular dependency detected');
            }
        });
    }

    // Check for circular dependencies
    private static function hasCircularDependency($predecessorId, $successorId, $visited = [])
    {
        if (in_array($successorId, $visited)) {
            return $successorId === $predecessorId;
        }

        $visited[] = $successorId;

        $dependencies = self::where('predecessor_id', $successorId)->get();

        foreach ($dependencies as $dep) {
            if (self::hasCircularDependency($predecessorId, $dep->successor_id, $visited)) {
                return true;
            }
        }

        return false;
    }

    // Get dependency type label
    public function getTypeLabel()
    {
        return match($this->type) {
            'FS' => 'Finish-to-Start',
            'SS' => 'Start-to-Start',
            'FF' => 'Finish-to-Finish',
            'SF' => 'Start-to-Finish',
            default => $this->type,
        };
    }
}
