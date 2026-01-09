<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\BankController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PriceListController;
use App\Http\Controllers\PriceListItemController;
use App\Http\Controllers\PriceSearchController;
use App\Http\Controllers\PriceRequestController;
use App\Http\Controllers\PriceQuotationController;
use App\Http\Controllers\PriceComparisonController;

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
    
    // Contract Templates
    Route::prefix('contract-templates')->name('contract-templates.')->group(function () {
        Route::get('/', [\App\Http\Controllers\ContractTemplateController::class, 'index'])->name('index');
        Route::post('/generate-contract', [\App\Http\Controllers\ContractTemplateController::class, 'storeGenerated'])->name('store-generated');
        Route::get('/preview/{id}', [\App\Http\Controllers\ContractTemplateController::class, 'preview'])->name('preview');
        Route::get('/jea-01', [\App\Http\Controllers\ContractTemplateController::class, 'jea01'])->name('jea-01');
        Route::get('/jea-02', [\App\Http\Controllers\ContractTemplateController::class, 'jea02'])->name('jea-02');
        Route::get('/{contractTemplate}', [\App\Http\Controllers\ContractTemplateController::class, 'show'])->name('show');
        Route::get('/{contractTemplate}/clauses', [\App\Http\Controllers\ContractTemplateController::class, 'clauses'])->name('clauses');
        Route::get('/{contractTemplate}/generate', [\App\Http\Controllers\ContractTemplateController::class, 'generate'])->name('generate');
    });
    
    // Contracts Export
    Route::get('/contracts/{id}/export-word', [\App\Http\Controllers\ContractTemplateController::class, 'exportWord'])->name('contracts.export-word');
    Route::get('/contracts/{id}/export-pdf', [\App\Http\Controllers\ContractTemplateController::class, 'exportPdf'])->name('contracts.export-pdf');
    
    // API Routes (JSON responses)
    Route::prefix('api')->name('api.')->group(function () {
        Route::get('/contract-templates', [\App\Http\Controllers\Api\ContractTemplateApiController::class, 'index']);
        Route::post('/contract-templates', [\App\Http\Controllers\Api\ContractTemplateApiController::class, 'store']);
        Route::get('/contract-templates/{id}', [\App\Http\Controllers\Api\ContractTemplateApiController::class, 'show']);
        Route::put('/contract-templates/{id}', [\App\Http\Controllers\Api\ContractTemplateApiController::class, 'update']);
        Route::get('/contract-templates/{id}/clauses', [\App\Http\Controllers\Api\ContractTemplateApiController::class, 'clauses']);
        Route::get('/contract-templates/{id}/variables', [\App\Http\Controllers\Api\ContractTemplateApiController::class, 'variables']);
        Route::post('/contract-templates/{id}/generate', [\App\Http\Controllers\Api\ContractTemplateApiController::class, 'generate']);
        Route::post('/contracts/generate-from-template', [\App\Http\Controllers\Api\ContractTemplateApiController::class, 'generateFromTemplate']);
        Route::get('/contracts/{id}/export-word', [\App\Http\Controllers\Api\ContractTemplateApiController::class, 'exportWord']);
        Route::get('/contracts/{id}/export-pdf', [\App\Http\Controllers\Api\ContractTemplateApiController::class, 'exportPdf']);
    });
});
