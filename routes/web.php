<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;

// Guest Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.post');
});

// Authenticated Routes
Route::middleware('auth')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
});
// Authenticated Routes
Route::middleware('auth')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
    
    // Companies Management
    Route::resource('companies', \App\Http\Controllers\CompanyController::class);
    
    // Time Bar Protection Module
    Route::prefix('time-bar')->name('time-bar.')->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\TimeBarController::class, 'dashboard'])->name('dashboard');
        Route::get('/alerts', [\App\Http\Controllers\TimeBarController::class, 'alerts'])->name('alerts');
        Route::get('/expiring', [\App\Http\Controllers\TimeBarController::class, 'expiring'])->name('expiring');
        Route::get('/expired', [\App\Http\Controllers\TimeBarController::class, 'expired'])->name('expired');
        Route::get('/calendar', [\App\Http\Controllers\TimeBarController::class, 'calendar'])->name('calendar');
        Route::get('/reports', [\App\Http\Controllers\TimeBarController::class, 'reports'])->name('reports');
        Route::get('/settings', [\App\Http\Controllers\TimeBarController::class, 'settings'])->name('settings');
        Route::put('/settings', [\App\Http\Controllers\TimeBarController::class, 'updateSettings'])->name('settings.update');
        Route::get('/clauses', [\App\Http\Controllers\TimeBarController::class, 'clauses'])->name('clauses');
        
        // Events
        Route::prefix('events')->name('events.')->group(function () {
            Route::get('/', [\App\Http\Controllers\TimeBarController::class, 'index'])->name('index');
            Route::get('/create', [\App\Http\Controllers\TimeBarController::class, 'create'])->name('create');
            Route::post('/', [\App\Http\Controllers\TimeBarController::class, 'store'])->name('store');
            Route::get('/{event}', [\App\Http\Controllers\TimeBarController::class, 'show'])->name('show');
            Route::get('/{event}/edit', [\App\Http\Controllers\TimeBarController::class, 'edit'])->name('edit');
            Route::put('/{event}', [\App\Http\Controllers\TimeBarController::class, 'update'])->name('update');
            Route::post('/{event}/send-notice', [\App\Http\Controllers\TimeBarController::class, 'sendNotice'])->name('send-notice');
        });
    });
});