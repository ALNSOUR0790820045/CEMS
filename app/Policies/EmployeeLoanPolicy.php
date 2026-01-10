<?php

namespace App\Policies;

use App\Models\User;
use App\Models\EmployeeLoan;

class EmployeeLoanPolicy
{
    public function view(User $user, EmployeeLoan $employeeLoan): bool
    {
        return $user->company_id === $employeeLoan->company_id;
    }

    public function update(User $user, EmployeeLoan $employeeLoan): bool
    {
        return $user->company_id === $employeeLoan->company_id;
    }

    public function delete(User $user, EmployeeLoan $employeeLoan): bool
    {
        return $user->company_id === $employeeLoan->company_id;
    }
}
