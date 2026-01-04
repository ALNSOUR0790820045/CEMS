<?php

namespace App\Observers;

use App\Models\PettyCashTransaction;

class PettyCashTransactionObserver
{
    /**
     * Handle the PettyCashTransaction "creating" event.
     */
    public function creating(PettyCashTransaction $pettyCashTransaction): void
    {
        if (empty($pettyCashTransaction->transaction_number)) {
            $pettyCashTransaction->transaction_number = $this->generateTransactionNumber();
        }
    }

    /**
     * Generate unique transaction number in format: PCT-YYYY-XXXX
     */
    private function generateTransactionNumber(): string
    {
        $year = now()->year;
        $prefix = "PCT-{$year}-";
        
        $lastTransaction = PettyCashTransaction::where('transaction_number', 'like', "{$prefix}%")
            ->orderBy('transaction_number', 'desc')
            ->first();

        if ($lastTransaction) {
            $lastNumber = (int) substr($lastTransaction->transaction_number, -4);
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }

        return $prefix . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    }
}
