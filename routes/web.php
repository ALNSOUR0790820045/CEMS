<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TenderProcurementController;

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
    Route::resource('companies', \App\Http\Controllers\CompanyController::class);
    
    // Tender Procurement Management
    Route::prefix('tenders/{tender}')->name('tender-procurement.')->group(function () {
        Route::get('/procurement', [TenderProcurementController::class, 'index'])->name('index');
        Route::get('/procurement/create', [TenderProcurementController::class, 'create'])->name('create');
        Route::post('/procurement', [TenderProcurementController::class, 'store'])->name('store');
        Route::get('/procurement/{package}', [TenderProcurementController::class, 'show'])->name('show');
        Route::get('/procurement/{package}/edit', [TenderProcurementController::class, 'edit'])->name('edit');
        Route::put('/procurement/{package}', [TenderProcurementController::class, 'update'])->name('update');
        Route::delete('/procurement/{package}', [TenderProcurementController::class, 'destroy'])->name('destroy');
        
        // Supplier management
        Route::get('/procurement/{package}/suppliers', [TenderProcurementController::class, 'suppliers'])->name('suppliers');
        Route::post('/procurement/{package}/suppliers', [TenderProcurementController::class, 'addSupplier'])->name('suppliers.add');
        Route::put('/procurement/{package}/suppliers/{supplier}', [TenderProcurementController::class, 'updateSupplier'])->name('suppliers.update');
        
        // Timeline and Long Lead Items
        Route::get('/procurement-timeline', [TenderProcurementController::class, 'timeline'])->name('timeline');
        Route::get('/long-lead-items', [TenderProcurementController::class, 'longLeadItems'])->name('long-lead-items');
        Route::post('/long-lead-items', [TenderProcurementController::class, 'storeLongLeadItem'])->name('long-lead-items.store');
    });
});