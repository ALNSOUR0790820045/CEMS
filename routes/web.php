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
    Route::resource('companies', App\Http\Controllers\CompanyController::class);
    
    // Price Escalation Module
    Route::prefix('price-escalation')->name('price-escalation.')->group(function () {
        // Dashboard
        Route::get('/dashboard', [App\Http\Controllers\PriceEscalationController::class, 'dashboard'])
            ->name('dashboard');
        
        // Contracts
        Route::get('/', [\App\Http\Controllers\PriceEscalationController::class, 'index'])
            ->name('index');
        Route::get('/contract-setup/{project?}', [\App\Http\Controllers\PriceEscalationController::class, 'contractSetup'])
            ->name('contract-setup');
        Route::post('/contract', [\App\Http\Controllers\PriceEscalationController::class, 'storeContract'])
            ->name('contract.store');
        Route::put('/contract/{contract}', [\App\Http\Controllers\PriceEscalationController::class, 'updateContract'])
            ->name('contract.update');
        Route::delete('/contract/{contract}', [\App\Http\Controllers\PriceEscalationController::class, 'destroy'])
            ->name('contract.destroy');
        
        // DSI Indices
        Route::get('/dsi-indices', [\App\Http\Controllers\DsiIndexController::class, 'index'])
            ->name('dsi-indices');
        Route::post('/dsi-indices', [\App\Http\Controllers\DsiIndexController::class, 'store'])
            ->name('dsi-indices.store');
        Route::put('/dsi-indices/{index}', [\App\Http\Controllers\DsiIndexController::class, 'update'])
            ->name('dsi-indices.update');
        Route::delete('/dsi-indices/{index}', [\App\Http\Controllers\DsiIndexController::class, 'destroy'])
            ->name('dsi-indices.destroy');
        Route::get('/dsi-indices/trend', [\App\Http\Controllers\DsiIndexController::class, 'getTrend'])
            ->name('dsi-indices.trend');
        
        // DSI Import
        Route::get('/import-dsi', [\App\Http\Controllers\DsiIndexController::class, 'importForm'])
            ->name('import-dsi');
        Route::post('/import-dsi', [\App\Http\Controllers\DsiIndexController::class, 'import'])
            ->name('import-dsi.post');
        
        // Calculations
        Route::get('/calculations', [\App\Http\Controllers\PriceEscalationCalculationController::class, 'index'])
            ->name('calculations');
        Route::get('/calculate', [\App\Http\Controllers\PriceEscalationCalculationController::class, 'create'])
            ->name('calculate');
        Route::post('/calculations', [\App\Http\Controllers\PriceEscalationCalculationController::class, 'store'])
            ->name('calculations.store');
        Route::get('/calculations/{calculation}', [\App\Http\Controllers\PriceEscalationCalculationController::class, 'show'])
            ->name('calculations.show');
        Route::post('/calculations/{calculation}/approve', [\App\Http\Controllers\PriceEscalationCalculationController::class, 'approve'])
            ->name('calculations.approve');
        Route::post('/calculations/{calculation}/reject', [\App\Http\Controllers\PriceEscalationCalculationController::class, 'reject'])
            ->name('calculations.reject');
        Route::delete('/calculations/{calculation}', [\App\Http\Controllers\PriceEscalationCalculationController::class, 'destroy'])
            ->name('calculations.destroy');
        Route::post('/calculations/preview', [\App\Http\Controllers\PriceEscalationCalculationController::class, 'preview'])
            ->name('calculations.preview');
    });
});