<?php

namespace App\Events\Notifications;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ContractExpiringEvent
{
    use Dispatchable, SerializesModels;

    public $contract;
    public $expiryDate;
    public $company;

    /**
     * Create a new event instance.
     */
    public function __construct($contract, $expiryDate, $company)
    {
        $this->contract = $contract;
        $this->expiryDate = $expiryDate;
        $this->company = $company;
    }
}
