<?php

namespace App\Policies;

use App\Models\User;
use App\Models\PayrollEntry;

class PayrollEntryPolicy
{
    public function view(User $user, PayrollEntry $payrollEntry): bool
    {
        return $user->company_id === $payrollEntry->company_id;
    }

    public function update(User $user, PayrollEntry $payrollEntry): bool
    {
        return $user->company_id === $payrollEntry->company_id;
    }

    public function delete(User $user, PayrollEntry $payrollEntry): bool
    {
        return $user->company_id === $payrollEntry->company_id;
    }
}
