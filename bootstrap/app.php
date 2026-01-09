<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Console\Scheduling\Schedule;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'permission' => \App\Http\Middleware\CheckPermission::class,
            'role' => \App\Http\Middleware\CheckRole::class,
        ]);
    })
    ->withSchedule(function (Schedule $schedule) {
        // Database backup - daily at 2:00 AM
        $schedule->command('backup:database')->daily()->at('02:00');
        
        // Files backup - weekly on Sunday at 3:00 AM
        $schedule->command('backup:files')->weekly()->sundays()->at('03:00');
        
        // Full backup - monthly at 4:00 AM
        $schedule->command('backup:full')->monthly()->at('04:00');
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();