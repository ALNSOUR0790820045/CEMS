<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EquipmentController;

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
    
    // Equipment Management
    Route::resource('equipment', EquipmentController::class);
    Route::post('equipment/{equipment}/assign', [EquipmentController::class, 'assign'])->name('equipment.assign');
    Route::post('equipment/{equipment}/return', [EquipmentController::class, 'returnEquipment'])->name('equipment.return');
    Route::post('equipment/{equipment}/transfer', [EquipmentController::class, 'transfer'])->name('equipment.transfer');
    Route::get('equipment/{equipment}/usage', [EquipmentController::class, 'usage'])->name('equipment.usage');
    Route::post('equipment/{equipment}/usage', [EquipmentController::class, 'storeUsage'])->name('equipment.usage.store');
    Route::get('equipment/{equipment}/maintenance', [EquipmentController::class, 'maintenance'])->name('equipment.maintenance');
    Route::post('equipment/{equipment}/maintenance', [EquipmentController::class, 'scheduleMaintenance'])->name('equipment.maintenance.store');
    Route::post('equipment/{equipment}/fuel', [EquipmentController::class, 'storeFuel'])->name('equipment.fuel.store');
    Route::get('equipment-available', [EquipmentController::class, 'available'])->name('equipment.available');
    Route::get('equipment-maintenance-due', [EquipmentController::class, 'maintenanceDue'])->name('equipment.maintenance-due');
    Route::get('equipment-statistics', [EquipmentController::class, 'statistics'])->name('equipment.statistics');
});
