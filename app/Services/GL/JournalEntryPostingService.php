<?php

namespace App\Services\GL;

use App\Models\GLJournalEntry;
use App\Models\GLAccount;
use App\Models\GLPeriod;
use Illuminate\Support\Facades\DB;
use Exception;

class JournalEntryPostingService
{
    /**
     * Post a journal entry to the ledger.
     * 
     * @param GLJournalEntry $entry
     * @param int $userId
     * @return GLJournalEntry
     * @throws Exception
     */
    public function post(GLJournalEntry $entry, int $userId): GLJournalEntry
    {
        // Validate entry can be posted
        if ($entry->status !== 'approved') {
            throw new Exception('Only approved journal entries can be posted.');
        }
        
        // Validate entry is balanced
        if (!$entry->is_balanced) {
            throw new Exception('Journal entry must be balanced before posting.');
        }
        
        // Validate entry date is in an open period
        $this->validatePeriod($entry->entry_date, $entry->company_id);
        
        DB::beginTransaction();
        
        try {
            // Update account balances for each line
            foreach ($entry->lines as $line) {
                $this->updateAccountBalance($line);
            }
            
            // Update journal entry status
            $entry->update([
                'status' => 'posted',
                'posted_by_id' => $userId,
                'posted_at' => now(),
                'posting_date' => now()->toDateString(),
            ]);
            
            DB::commit();
            
            // Fire event
            event(new \App\Events\GL\JournalEntryPosted($entry));
            
            return $entry->fresh();
            
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
    
    /**
     * Update the balance of a GL account based on a journal entry line.
     */
    protected function updateAccountBalance($line): void
    {
        $account = GLAccount::findOrFail($line->gl_account_id);
        
        // Calculate balance change based on account type
        $balanceChange = 0;
        
        // For assets and expenses: debit increases, credit decreases
        if (in_array($account->account_type, ['asset', 'expense'])) {
            $balanceChange = $line->base_currency_debit - $line->base_currency_credit;
        }
        // For liabilities, equity, and revenue: credit increases, debit decreases
        elseif (in_array($account->account_type, ['liability', 'equity', 'revenue'])) {
            $balanceChange = $line->base_currency_credit - $line->base_currency_debit;
        }
        
        // Update the account balance
        $account->increment('current_balance', $balanceChange);
    }
    
    /**
     * Validate that the entry date falls within an open period.
     */
    protected function validatePeriod(string $entryDate, int $companyId): void
    {
        $period = GLPeriod::where('company_id', $companyId)
            ->where('start_date', '<=', $entryDate)
            ->where('end_date', '>=', $entryDate)
            ->first();
        
        if (!$period) {
            throw new Exception('No fiscal period found for the entry date.');
        }
        
        if ($period->status !== 'open') {
            throw new Exception("The fiscal period for this date is {$period->status}. Only open periods can accept postings.");
        }
    }
}
