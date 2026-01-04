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
    
    // Companies Management
    Route::resource('companies', \App\Http\Controllers\CompanyController::class);
    
    // BOQ Management
    Route::resource('boq', \App\Http\Controllers\BOQController::class);
    Route::post('boq/{boq}/calculate', [\App\Http\Controllers\BOQController::class, 'calculate'])->name('boq.calculate');
    Route::post('boq/{boq}/duplicate', [\App\Http\Controllers\BOQController::class, 'duplicate'])->name('boq.duplicate');
    Route::post('boq/{boq}/approve', [\App\Http\Controllers\BOQController::class, 'approve'])->name('boq.approve');
    Route::get('boq/{boq}/cost-analysis', [\App\Http\Controllers\BOQController::class, 'costAnalysis'])->name('boq.cost-analysis');
    Route::post('boq/{boq}/sections', [\App\Http\Controllers\BOQController::class, 'addSection'])->name('boq.sections.store');
    Route::post('boq/{boq}/items', [\App\Http\Controllers\BOQController::class, 'addItem'])->name('boq.items.store');
    Route::put('boq/{boq}/items/{item}', [\App\Http\Controllers\BOQController::class, 'updateItem'])->name('boq.items.update');
    Route::delete('boq/{boq}/items/{item}', [\App\Http\Controllers\BOQController::class, 'deleteItem'])->name('boq.items.destroy');
    
    // Units API
    Route::apiResource('units', \App\Http\Controllers\UnitController::class);
});