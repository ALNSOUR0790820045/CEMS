<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ClientContactController;
use App\Http\Controllers\ClientBankAccountController;
use App\Http\Controllers\ClientDocumentController;

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
    Route::resource('companies', \App\Http\Controllers\CompanyController::class);
    
    // Clients Management
    Route::resource('clients', ClientController::class);
    Route::post('clients/{id}/restore', [ClientController::class, 'restore'])->name('clients.restore');
    Route::get('clients/generate-code', [ClientController::class, 'generateCode'])->name('clients.generate-code');
    
    // Client Contacts
    Route::get('clients/{client}/contacts', [ClientContactController::class, 'index'])->name('clients.contacts.index');
    Route::post('clients/{client}/contacts', [ClientContactController::class, 'store'])->name('clients.contacts.store');
    Route::put('clients/{client}/contacts/{contact}', [ClientContactController::class, 'update'])->name('clients.contacts.update');
    Route::delete('clients/{client}/contacts/{contact}', [ClientContactController::class, 'destroy'])->name('clients.contacts.destroy');
    Route::post('clients/{client}/contacts/{contact}/primary', [ClientContactController::class, 'setPrimary'])->name('clients.contacts.primary');
    
    // Client Bank Accounts
    Route::get('clients/{client}/bank-accounts', [ClientBankAccountController::class, 'index'])->name('clients.bank-accounts.index');
    Route::post('clients/{client}/bank-accounts', [ClientBankAccountController::class, 'store'])->name('clients.bank-accounts.store');
    Route::put('clients/{client}/bank-accounts/{bankAccount}', [ClientBankAccountController::class, 'update'])->name('clients.bank-accounts.update');
    Route::delete('clients/{client}/bank-accounts/{bankAccount}', [ClientBankAccountController::class, 'destroy'])->name('clients.bank-accounts.destroy');
    Route::post('clients/{client}/bank-accounts/{bankAccount}/primary', [ClientBankAccountController::class, 'setPrimary'])->name('clients.bank-accounts.primary');
    
    // Client Documents
    Route::get('clients/{client}/documents', [ClientDocumentController::class, 'index'])->name('clients.documents.index');
    Route::post('clients/{client}/documents', [ClientDocumentController::class, 'store'])->name('clients.documents.store');
    Route::get('clients/{client}/documents/{document}/download', [ClientDocumentController::class, 'download'])->name('clients.documents.download');
    Route::delete('clients/{client}/documents/{document}', [ClientDocumentController::class, 'destroy'])->name('clients.documents.destroy');
});