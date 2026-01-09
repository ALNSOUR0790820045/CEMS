<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\PayrollPeriod;
use App\Models\PayrollEntry;
use App\Models\EmployeeLoan;
use App\Policies\PayrollPeriodPolicy;
use App\Policies\PayrollEntryPolicy;
use App\Policies\EmployeeLoanPolicy;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::policy(PayrollPeriod::class, PayrollPeriodPolicy::class);
        Gate::policy(PayrollEntry::class, PayrollEntryPolicy::class);
        Gate::policy(EmployeeLoan::class, EmployeeLoanPolicy::class);
    }
}
