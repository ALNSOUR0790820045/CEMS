<?php

namespace App\Events\Notifications;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PaymentOverdueEvent
{
    use Dispatchable, SerializesModels;

    public $invoice;
    public $daysOverdue;
    public $company;

    /**
     * Create a new event instance.
     */
    public function __construct($invoice, $daysOverdue, $company)
    {
        $this->invoice = $invoice;
        $this->daysOverdue = $daysOverdue;
        $this->company = $company;
    }
}
