<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ChangeOrderController;

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
    
    // Change Orders Management
    Route::resource('change-orders', ChangeOrderController::class);
    Route::post('change-orders/{changeOrder}/submit', [ChangeOrderController::class, 'submit'])->name('change-orders.submit');
    Route::get('change-orders/{changeOrder}/approve-form', [ChangeOrderController::class, 'approve'])->name('change-orders.approve');
    Route::post('change-orders/{changeOrder}/approve', [ChangeOrderController::class, 'processApproval'])->name('change-orders.process-approval');
    Route::get('change-orders-report', [ChangeOrderController::class, 'report'])->name('change-orders.report');
    Route::get('change-orders/{changeOrder}/export-pdf', [ChangeOrderController::class, 'exportPdf'])->name('change-orders.export-pdf');
    Route::get('projects/{project}/wbs', [ChangeOrderController::class, 'getProjectWbs'])->name('projects.wbs');
    Route::get('contracts/{contract}/details', [ChangeOrderController::class, 'getContractDetails'])->name('contracts.details');
});