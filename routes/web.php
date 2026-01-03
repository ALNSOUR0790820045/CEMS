<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GL\GLAccountController;
use App\Http\Controllers\GL\GLJournalEntryController;
use App\Http\Controllers\GL\GLFiscalYearController;

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
    
    // General Ledger Module
    Route::prefix('gl')->name('gl.')->group(function () {
        // Chart of Accounts
        Route::resource('accounts', GLAccountController::class);
        Route::get('accounts/{account}/ledger', [GLAccountController::class, 'ledger'])->name('accounts.ledger');
        
        // Journal Entries
        Route::resource('journal-entries', GLJournalEntryController::class);
        Route::post('journal-entries/{journalEntry}/submit', [GLJournalEntryController::class, 'submit'])->name('journal-entries.submit');
        Route::post('journal-entries/{journalEntry}/approve', [GLJournalEntryController::class, 'approve'])->name('journal-entries.approve');
        Route::post('journal-entries/{journalEntry}/reject', [GLJournalEntryController::class, 'reject'])->name('journal-entries.reject');
        Route::post('journal-entries/{journalEntry}/post', [GLJournalEntryController::class, 'post'])->name('journal-entries.post');
        Route::post('journal-entries/{journalEntry}/reverse', [GLJournalEntryController::class, 'reverse'])->name('journal-entries.reverse');
        
        // Fiscal Years
        Route::resource('fiscal-years', GLFiscalYearController::class);
    });
});