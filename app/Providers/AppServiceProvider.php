<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\PettyCashAccount;
use App\Models\PettyCashTransaction;
use App\Observers\PettyCashAccountObserver;
use App\Observers\PettyCashTransactionObserver;

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
        PettyCashAccount::observe(PettyCashAccountObserver::class);
        PettyCashTransaction::observe(PettyCashTransactionObserver::class);
    }
}
