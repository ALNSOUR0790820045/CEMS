<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TimeBarController;

Route::middleware(['auth:sanctum'])->prefix('time-bar')->name('api.time-bar.')->group(function () {
    // Events API
    Route::get('/events', [TimeBarController::class, 'index'])->name('events.index');
    Route::post('/events', [TimeBarController::class, 'store'])->name('events.store');
    Route::get('/events/{event}', [TimeBarController::class, 'show'])->name('events.show');
    Route::put('/events/{event}', [TimeBarController::class, 'update'])->name('events.update');
    Route::post('/events/{event}/send-notice', [TimeBarController::class, 'sendNotice'])->name('events.send-notice');
    
    // Dashboard and Statistics
    Route::get('/dashboard', [TimeBarController::class, 'dashboard'])->name('dashboard');
    Route::get('/statistics', [TimeBarController::class, 'statistics'])->name('statistics');
    
    // Alerts
    Route::get('/alerts', [TimeBarController::class, 'alerts'])->name('alerts');
    
    // Expiring and Expired
    Route::get('/expiring', [TimeBarController::class, 'expiring'])->name('expiring');
    Route::get('/expired', [TimeBarController::class, 'expired'])->name('expired');
    
    // Settings
    Route::get('/settings', [TimeBarController::class, 'settings'])->name('settings');
    Route::put('/settings', [TimeBarController::class, 'updateSettings'])->name('settings.update');
    
    // Clauses
    Route::get('/clauses', [TimeBarController::class, 'clauses'])->name('clauses');
});
