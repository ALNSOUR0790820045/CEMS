<?php

namespace App\Events\Notifications;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ApprovalRequestedEvent
{
    use Dispatchable, SerializesModels;

    public $item;
    public $type;
    public $approvers;
    public $company;

    /**
     * Create a new event instance.
     */
    public function __construct($item, $type, $approvers, $company)
    {
        $this->item = $item;
        $this->type = $type;
        $this->approvers = $approvers;
        $this->company = $company;
    }
}
