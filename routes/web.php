<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CorrespondenceController;

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
    
    // Correspondence Management
    Route::prefix('correspondence')->name('correspondence.')->group(function () {
        Route::get('/dashboard', [CorrespondenceController::class, 'dashboard'])->name('dashboard');
        Route::get('/incoming', [CorrespondenceController::class, 'incoming'])->name('incoming');
        Route::get('/outgoing', [CorrespondenceController::class, 'outgoing'])->name('outgoing');
        Route::get('/pending', [CorrespondenceController::class, 'pending'])->name('pending');
        Route::get('/overdue', [CorrespondenceController::class, 'overdue'])->name('overdue');
        Route::get('/search', [CorrespondenceController::class, 'search'])->name('search');
        Route::get('/statistics', [CorrespondenceController::class, 'statistics'])->name('statistics');
        Route::get('/registers', [CorrespondenceController::class, 'registers'])->name('registers');
        Route::get('/templates', [CorrespondenceController::class, 'templates'])->name('templates');
        Route::get('/{correspondence}/thread', [CorrespondenceController::class, 'thread'])->name('thread');
        Route::post('/{correspondence}/send', [CorrespondenceController::class, 'send'])->name('send');
        Route::post('/{correspondence}/approve', [CorrespondenceController::class, 'approve'])->name('approve');
        Route::post('/{correspondence}/forward', [CorrespondenceController::class, 'forward'])->name('forward');
        Route::post('/{correspondence}/reply', [CorrespondenceController::class, 'reply'])->name('reply');
    });
    
    Route::resource('correspondence', CorrespondenceController::class);
});