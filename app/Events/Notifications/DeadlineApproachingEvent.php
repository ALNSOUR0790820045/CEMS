<?php

namespace App\Events\Notifications;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DeadlineApproachingEvent
{
    use Dispatchable, SerializesModels;

    public $item;
    public $type;
    public $deadline;
    public $company;

    /**
     * Create a new event instance.
     */
    public function __construct($item, $type, $deadline, $company)
    {
        $this->item = $item;
        $this->type = $type;
        $this->deadline = $deadline;
        $this->company = $company;
    }
}
