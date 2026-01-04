<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GuaranteeController;
use App\Http\Controllers\BankController;
use App\Http\Controllers\CompanyController;

// Guest Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.post');
});

// Authenticated Routes
Route::middleware('auth')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
    
    // Companies Management
    Route::resource('companies', CompanyController::class);
    
    // Banks Management
    Route::resource('banks', BankController::class);
    
    // Guarantees Management
    Route::resource('guarantees', GuaranteeController::class);
    Route::post('guarantees/{guarantee}/approve', [GuaranteeController::class, 'approve'])->name('guarantees.approve');
    Route::get('guarantees/{guarantee}/renew', [GuaranteeController::class, 'showRenewForm'])->name('guarantees.renew');
    Route::post('guarantees/{guarantee}/renew', [GuaranteeController::class, 'renew'])->name('guarantees.renew.store');
    Route::get('guarantees/{guarantee}/release', [GuaranteeController::class, 'showReleaseForm'])->name('guarantees.release');
    Route::post('guarantees/{guarantee}/release', [GuaranteeController::class, 'release'])->name('guarantees.release.store');
    
    // Guarantees Reports & Statistics
    Route::get('guarantees-expiring', [GuaranteeController::class, 'expiring'])->name('guarantees.expiring');
    Route::get('guarantees-statistics', [GuaranteeController::class, 'statistics'])->name('guarantees.statistics');
    Route::get('guarantees-reports', [GuaranteeController::class, 'reports'])->name('guarantees.reports');
});
