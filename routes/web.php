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
    Route:: resource('companies', \App\Http\Controllers\CompanyController::class);
    
    // Dashboard Views
    Route::get('/dashboards/executive', function () {
        return view('dashboards.executive');
    })->name('dashboards.executive');
    
    Route::get('/dashboards/project', function () {
        return view('dashboards.project');
    })->name('dashboards.project');
    
    Route::get('/dashboards/financial', function () {
        return view('dashboards.financial');
    })->name('dashboards.financial');
});