<?php

namespace App\Services\GL;

use App\Models\GLJournalEntry;
use App\Models\GLJournalEntryLine;
use Illuminate\Support\Facades\DB;
use Exception;

class JournalEntryReversalService
{
    protected JournalNumberGenerator $numberGenerator;
    protected JournalEntryPostingService $postingService;
    
    public function __construct(
        JournalNumberGenerator $numberGenerator,
        JournalEntryPostingService $postingService
    ) {
        $this->numberGenerator = $numberGenerator;
        $this->postingService = $postingService;
    }
    
    /**
     * Reverse a posted journal entry.
     * 
     * @param GLJournalEntry $originalEntry
     * @param int $userId
     * @param string|null $reversalDate
     * @return GLJournalEntry The reversal entry
     * @throws Exception
     */
    public function reverse(
        GLJournalEntry $originalEntry, 
        int $userId,
        ?string $reversalDate = null
    ): GLJournalEntry {
        // Validate entry can be reversed
        if ($originalEntry->status !== 'posted') {
            throw new Exception('Only posted journal entries can be reversed.');
        }
        
        if ($originalEntry->reversed_by_id) {
            throw new Exception('This journal entry has already been reversed.');
        }
        
        DB::beginTransaction();
        
        try {
            // Create reversal entry
            $reversalEntry = $this->createReversalEntry(
                $originalEntry,
                $userId,
                $reversalDate ?? now()->toDateString()
            );
            
            // Mark original entry as reversed
            $originalEntry->update([
                'reversed_by_id' => $reversalEntry->id,
            ]);
            
            // Auto-approve the reversal entry
            $reversalEntry->update([
                'status' => 'approved',
                'approved_by_id' => $userId,
                'approved_at' => now(),
            ]);
            
            // Post the reversal entry
            $this->postingService->post($reversalEntry, $userId);
            
            DB::commit();
            
            // Fire event
            event(new \App\Events\GL\JournalEntryReversed($originalEntry, $reversalEntry));
            
            return $reversalEntry->fresh();
            
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
    
    /**
     * Create the reversal journal entry with swapped debit/credit amounts.
     */
    protected function createReversalEntry(
        GLJournalEntry $originalEntry,
        int $userId,
        string $reversalDate
    ): GLJournalEntry {
        // Generate new journal number
        $journalNumber = $this->numberGenerator->generate($reversalDate);
        
        // Create reversal entry (mirror of original with swapped amounts)
        $reversalEntry = GLJournalEntry::create([
            'journal_number' => $journalNumber,
            'entry_date' => $reversalDate,
            'journal_type' => 'reversal',
            'reference_type' => $originalEntry->reference_type,
            'reference_number' => $originalEntry->reference_number,
            'description' => 'Reversal of ' . $originalEntry->journal_number . ' - ' . $originalEntry->description,
            'total_debit' => $originalEntry->total_credit, // Swapped
            'total_credit' => $originalEntry->total_debit, // Swapped
            'currency_id' => $originalEntry->currency_id,
            'exchange_rate' => $originalEntry->exchange_rate,
            'status' => 'draft',
            'created_by_id' => $userId,
            'reversed_from_id' => $originalEntry->id,
            'project_id' => $originalEntry->project_id,
            'department_id' => $originalEntry->department_id,
            'company_id' => $originalEntry->company_id,
        ]);
        
        // Create reversal lines with swapped debit/credit amounts
        foreach ($originalEntry->lines as $index => $originalLine) {
            GLJournalEntryLine::create([
                'journal_entry_id' => $reversalEntry->id,
                'line_number' => $originalLine->line_number,
                'gl_account_id' => $originalLine->gl_account_id,
                'debit_amount' => $originalLine->credit_amount, // Swapped
                'credit_amount' => $originalLine->debit_amount, // Swapped
                'description' => $originalLine->description,
                'cost_center_id' => $originalLine->cost_center_id,
                'project_id' => $originalLine->project_id,
                'currency_id' => $originalLine->currency_id,
                'exchange_rate' => $originalLine->exchange_rate,
                'base_currency_debit' => $originalLine->base_currency_credit, // Swapped
                'base_currency_credit' => $originalLine->base_currency_debit, // Swapped
                'company_id' => $originalLine->company_id,
            ]);
        }
        
        return $reversalEntry;
    }
}
