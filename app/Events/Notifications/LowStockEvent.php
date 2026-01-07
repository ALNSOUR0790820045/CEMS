<?php

namespace App\Events\Notifications;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class LowStockEvent
{
    use Dispatchable, SerializesModels;

    public $material;

    public $currentStock;

    public $minStock;

    public $company;

    /**
     * Create a new event instance.
     */
    public function __construct($material, $currentStock, $minStock, $company)
    {
        $this->material = $material;
        $this->currentStock = $currentStock;
        $this->minStock = $minStock;
        $this->company = $company;
    }
}
