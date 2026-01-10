<?php

namespace App\Traits;

trait HasAutoNumber
{
    /**
     * Boot the trait.
     */
    protected static function bootHasAutoNumber()
    {
        static::creating(function ($model) {
            if (empty($model->{$model->getAutoNumberColumn()})) {
                $model->{$model->getAutoNumberColumn()} = $model->generateAutoNumber();
            }
        });
    }

    /**
     * Generate auto number for the model.
     */
    public function generateAutoNumber(): string
    {
        $prefix = $this->getAutoNumberPrefix();
        $year = date('Y');
        $column = $this->getAutoNumberColumn();
        
        $lastRecord = static::where($column, 'LIKE', "{$prefix}-{$year}-%")
            ->orderByDesc('id')
            ->value($column);
        
        if ($lastRecord) {
            $lastNumber = (int) substr($lastRecord, -4);
            $sequence = $lastNumber + 1;
        } else {
            $sequence = 1;
        }
        
        return sprintf('%s-%s-%04d', $prefix, $year, $sequence);
    }

    /**
     * Get the auto number prefix.
     */
    abstract public function getAutoNumberPrefix(): string;

    /**
     * Get the auto number column name.
     */
    abstract public function getAutoNumberColumn(): string;
}
