<?php

namespace App\Events\Notifications;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BudgetExceededEvent
{
    use Dispatchable, SerializesModels;

    public $project;

    public $budget;

    public $spent;

    public $company;

    /**
     * Create a new event instance.
     */
    public function __construct($project, $budget, $spent, $company)
    {
        $this->project = $project;
        $this->budget = $budget;
        $this->spent = $spent;
        $this->company = $company;
    }
}
