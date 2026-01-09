<?php

namespace App\Services\GL;

use App\Models\GLJournalEntry;
use Carbon\Carbon;

class JournalNumberGenerator
{
    /**
     * Generate a unique journal entry number.
     * Format: JE-YYYY-MM-XXXX
     * Example: JE-2026-01-0001
     */
    public function generate(?string $date = null): string
    {
        $date = $date ? Carbon::parse($date) : now();
        $year = $date->format('Y');
        $month = $date->format('m');
        
        $prefix = "JE-{$year}-{$month}-";
        
        // Get the last journal entry number for this month
        $lastEntry = GLJournalEntry::where('journal_number', 'LIKE', $prefix . '%')
            ->orderBy('journal_number', 'desc')
            ->first();
        
        if ($lastEntry) {
            // Extract the sequence number and increment
            $lastNumber = intval(substr($lastEntry->journal_number, -4));
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }
        
        // Format with leading zeros (4 digits)
        $sequence = str_pad($newNumber, 4, '0', STR_PAD_LEFT);
        
        return $prefix . $sequence;
    }
}
