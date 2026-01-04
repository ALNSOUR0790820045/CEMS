<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PriceListController;
use App\Http\Controllers\PriceListItemController;
use App\Http\Controllers\PriceSearchController;
use App\Http\Controllers\PriceRequestController;
use App\Http\Controllers\PriceQuotationController;
use App\Http\Controllers\PriceComparisonController;

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
    
    // Price Lists Management
    Route::resource('price-lists', PriceListController::class);
    Route::get('price-lists/{priceList}/items', [PriceListController::class, 'items'])->name('price-lists.items');
    
    // Price List Items
    Route::get('price-lists/{priceList}/items/create', [PriceListItemController::class, 'create'])->name('price-list-items.create');
    Route::post('price-lists/{priceList}/items', [PriceListItemController::class, 'store'])->name('price-list-items.store');
    Route::put('price-list-items/{item}', [PriceListItemController::class, 'update'])->name('price-list-items.update');
    Route::get('price-list-items/{item}/history', [PriceListItemController::class, 'history'])->name('price-list-items.history');
    
    // Price Search
    Route::get('prices/search', [PriceSearchController::class, 'search'])->name('prices.search');
    Route::get('prices/materials', [PriceSearchController::class, 'materials'])->name('prices.materials');
    Route::get('prices/labor', [PriceSearchController::class, 'labor'])->name('prices.labor');
    Route::get('prices/equipment', [PriceSearchController::class, 'equipment'])->name('prices.equipment');
    Route::get('prices/compare', [PriceSearchController::class, 'compare'])->name('prices.compare');
    
    // Price Requests
    Route::resource('price-requests', PriceRequestController::class);
    Route::post('price-requests/{priceRequest}/send', [PriceRequestController::class, 'send'])->name('price-requests.send');
    
    // Price Quotations
    Route::get('price-requests/{priceRequest}/quotations', [PriceQuotationController::class, 'index'])->name('price-requests.quotations');
    Route::post('price-requests/{priceRequest}/quotations', [PriceQuotationController::class, 'store'])->name('price-quotations.store');
    Route::get('price-quotations/{quotation}', [PriceQuotationController::class, 'show'])->name('price-quotations.show');
    
    // Price Comparisons
    Route::get('price-requests/{priceRequest}/compare', [PriceComparisonController::class, 'create'])->name('price-comparisons.create');
    Route::post('price-requests/{priceRequest}/compare', [PriceComparisonController::class, 'store'])->name('price-comparisons.store');
    Route::get('price-comparisons/{comparison}', [PriceComparisonController::class, 'show'])->name('price-comparisons.show');
    Route::post('price-comparisons/{comparison}/approve', [PriceComparisonController::class, 'approve'])->name('price-comparisons.approve');
});
