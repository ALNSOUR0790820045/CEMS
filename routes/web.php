<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ClientController;

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
    
    // Clients Management
    Route::resource('clients', ClientController::class);
    
    // Projects Management
    Route::resource('projects', ProjectController::class);
    Route::get('/api/projects/generate-code', [ProjectController::class, 'generateCode'])->name('projects.generate-code');
});
