<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\BankController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SiteReceiptController;

// Guest Routes
Route::middleware('guest')->group(function () {
    Route:: get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login. post');
});

// Authenticated Routes
Route::middleware('auth')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
    
    // Companies Management
    Route::resource('companies', \App\Http\Controllers\CompanyController::class);
    
    // Tender WBS Management
    Route::get('/tenders/{tender}/wbs', [\App\Http\Controllers\TenderWbsController::class, 'index'])->name('tender-wbs.index');
    Route::get('/tenders/{tender}/wbs/create', [\App\Http\Controllers\TenderWbsController::class, 'create'])->name('tender-wbs.create');
    Route::post('/tenders/{tender}/wbs', [\App\Http\Controllers\TenderWbsController::class, 'store'])->name('tender-wbs.store');
    Route::get('/tenders/{tender}/wbs/{wbs}/edit', [\App\Http\Controllers\TenderWbsController::class, 'edit'])->name('tender-wbs.edit');
    Route::put('/tenders/{tender}/wbs/{wbs}', [\App\Http\Controllers\TenderWbsController::class, 'update'])->name('tender-wbs.update');
    Route::delete('/tenders/{tender}/wbs/{wbs}', [\App\Http\Controllers\TenderWbsController::class, 'destroy'])->name('tender-wbs.destroy');
    Route::get('/tenders/{tender}/wbs/import', [\App\Http\Controllers\TenderWbsController::class, 'import'])->name('tender-wbs.import');
    Route::post('/tenders/{tender}/wbs/update-sort', [\App\Http\Controllers\TenderWbsController::class, 'updateSort'])->name('tender-wbs.update-sort');
});
