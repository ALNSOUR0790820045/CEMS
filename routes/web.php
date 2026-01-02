<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TenderRiskController;

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
    
    // Tender Risk Management
    Route::prefix('tenders/{tender}/risks')->name('tender-risks.')->group(function () {
        Route::get('dashboard', [TenderRiskController::class, 'dashboard'])->name('dashboard');
        Route::get('/', [TenderRiskController::class, 'index'])->name('index');
        Route::get('create', [TenderRiskController::class, 'create'])->name('create');
        Route::post('/', [TenderRiskController::class, 'store'])->name('store');
        Route::get('{risk}/edit', [TenderRiskController::class, 'edit'])->name('edit');
        Route::put('{risk}', [TenderRiskController::class, 'update'])->name('update');
        Route::delete('{risk}', [TenderRiskController::class, 'destroy'])->name('destroy');
        Route::get('matrix', [TenderRiskController::class, 'matrix'])->name('matrix');
        Route::get('contingency', [TenderRiskController::class, 'contingency'])->name('contingency');
        Route::post('contingency', [TenderRiskController::class, 'updateContingency'])->name('update-contingency');
        Route::get('response-plan', [TenderRiskController::class, 'responsePlan'])->name('response-plan');
    });
});