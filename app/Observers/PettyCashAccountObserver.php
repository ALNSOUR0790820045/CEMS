<?php

namespace App\Observers;

use App\Models\PettyCashAccount;

class PettyCashAccountObserver
{
    /**
     * Handle the PettyCashAccount "creating" event.
     */
    public function creating(PettyCashAccount $pettyCashAccount): void
    {
        if (empty($pettyCashAccount->account_code)) {
            $pettyCashAccount->account_code = $this->generateAccountCode();
        }
    }

    /**
     * Generate unique account code in format: PC-YYYY-XXX
     */
    private function generateAccountCode(): string
    {
        $year = now()->year;
        $prefix = "PC-{$year}-";
        
        $lastAccount = PettyCashAccount::where('account_code', 'like', "{$prefix}%")
            ->orderBy('account_code', 'desc')
            ->first();

        if ($lastAccount) {
            $lastNumber = (int) substr($lastAccount->account_code, -3);
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }

        return $prefix . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
    }
}
