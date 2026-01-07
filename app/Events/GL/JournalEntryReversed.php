<?php

namespace App\Events\GL;

use App\Models\GLJournalEntry;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class JournalEntryReversed
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public GLJournalEntry $originalEntry;
    public GLJournalEntry $reversalEntry;

    /**
     * Create a new event instance.
     */
    public function __construct(GLJournalEntry $originalEntry, GLJournalEntry $reversalEntry)
    {
        $this->originalEntry = $originalEntry;
        $this->reversalEntry = $reversalEntry;
    }
}
