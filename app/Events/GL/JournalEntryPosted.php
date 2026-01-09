<?php

namespace App\Events\GL;

use App\Models\GLJournalEntry;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class JournalEntryPosted
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public GLJournalEntry $entry;

    /**
     * Create a new event instance.
     */
    public function __construct(GLJournalEntry $entry)
    {
        $this->entry = $entry;
    }
}
