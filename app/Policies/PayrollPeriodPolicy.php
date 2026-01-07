<?php

namespace App\Policies;

use App\Models\User;
use App\Models\PayrollPeriod;

class PayrollPeriodPolicy
{
    public function view(User $user, PayrollPeriod $payrollPeriod): bool
    {
        return $user->company_id === $payrollPeriod->company_id;
    }

    public function update(User $user, PayrollPeriod $payrollPeriod): bool
    {
        return $user->company_id === $payrollPeriod->company_id;
    }

    public function delete(User $user, PayrollPeriod $payrollPeriod): bool
    {
        return $user->company_id === $payrollPeriod->company_id;
    }
}
