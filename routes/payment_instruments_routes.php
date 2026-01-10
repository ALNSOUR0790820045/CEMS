<?php

/**
 * Payment Instruments Module Routes
 * 
 * Add these routes to your routes/web.php file within the authenticated middleware group
 * 
 * Example:
 * Route::middleware('auth')->group(function () {
 *     // ... other routes ...
 *     
 *     // Include payment instruments routes
 *     require __DIR__.'/payment_instruments_routes.php';
 * });
 */

use App\Http\Controllers\CurrencyController;
use App\Http\Controllers\ExchangeRateController;
use App\Http\Controllers\CheckController;
use App\Http\Controllers\PromissoryNoteController;
use App\Http\Controllers\GuaranteeController;
use App\Http\Controllers\PaymentTemplateController;

// ============================================================================
// CURRENCIES
// ============================================================================
Route::resource('currencies', CurrencyController::class);

// ============================================================================
// EXCHANGE RATES
// ============================================================================
Route::prefix('exchange-rates')->name('exchange-rates.')->group(function () {
    Route::get('/', [ExchangeRateController::class, 'index'])->name('index');
    Route::get('/create', [ExchangeRateController::class, 'create'])->name('create');
    Route::post('/', [ExchangeRateController::class, 'store'])->name('store');
    Route::get('/{exchangeRate}/edit', [ExchangeRateController::class, 'edit'])->name('edit');
    Route::put('/{exchangeRate}', [ExchangeRateController::class, 'update'])->name('update');
    Route::delete('/{exchangeRate}', [ExchangeRateController::class, 'destroy'])->name('destroy');
    
    // Bulk operations
    Route::get('/bulk-update', [ExchangeRateController::class, 'bulkUpdate'])->name('bulk-update');
    Route::post('/update-bulk', [ExchangeRateController::class, 'updateRates'])->name('update-bulk');
    
    // API endpoint
    Route::get('/get-rate', [ExchangeRateController::class, 'getRate'])->name('get-rate');
});

// ============================================================================
// CHECKS
// ============================================================================
Route::prefix('checks')->name('checks.')->group(function () {
    // Standard CRUD
    Route::get('/', [CheckController::class, 'index'])->name('index');
    Route::get('/create', [CheckController::class, 'create'])->name('create');
    Route::post('/', [CheckController::class, 'store'])->name('store');
    Route::get('/{check}', [CheckController::class, 'show'])->name('show');
    Route::get('/{check}/edit', [CheckController::class, 'edit'])->name('edit');
    Route::put('/{check}', [CheckController::class, 'update'])->name('update');
    Route::delete('/{check}', [CheckController::class, 'destroy'])->name('destroy');
    
    // Status operations
    Route::post('/{check}/clear', [CheckController::class, 'clear'])->name('clear');
    Route::post('/{check}/bounce', [CheckController::class, 'bounce'])->name('bounce');
    Route::post('/{check}/cancel', [CheckController::class, 'cancel'])->name('cancel');
    
    // Reports & Notifications
    Route::get('/due-soon', [CheckController::class, 'dueSoon'])->name('due-soon');
    Route::get('/overdue', [CheckController::class, 'overdue'])->name('overdue');
    
    // Print & Export
    Route::get('/{check}/print', [CheckController::class, 'print'])->name('print');
    Route::get('/{check}/pdf', [CheckController::class, 'pdf'])->name('pdf');
});

// ============================================================================
// PROMISSORY NOTES
// ============================================================================
Route::prefix('promissory-notes')->name('promissory-notes.')->group(function () {
    // Standard CRUD
    Route::get('/', [PromissoryNoteController::class, 'index'])->name('index');
    Route::get('/create', [PromissoryNoteController::class, 'create'])->name('create');
    Route::post('/', [PromissoryNoteController::class, 'store'])->name('store');
    Route::get('/{promissoryNote}', [PromissoryNoteController::class, 'show'])->name('show');
    Route::get('/{promissoryNote}/edit', [PromissoryNoteController::class, 'edit'])->name('edit');
    Route::put('/{promissoryNote}', [PromissoryNoteController::class, 'update'])->name('update');
    Route::delete('/{promissoryNote}', [PromissoryNoteController::class, 'destroy'])->name('destroy');
    
    // Status operations
    Route::post('/{promissoryNote}/mark-paid', [PromissoryNoteController::class, 'markAsPaid'])->name('mark-paid');
    Route::post('/{promissoryNote}/mark-dishonored', [PromissoryNoteController::class, 'markAsDishonored'])->name('mark-dishonored');
    
    // Reports & Notifications
    Route::get('/due-soon', [PromissoryNoteController::class, 'dueSoon'])->name('due-soon');
    Route::get('/overdue', [PromissoryNoteController::class, 'overdue'])->name('overdue');
    
    // Print & Export
    Route::get('/{promissoryNote}/print', [PromissoryNoteController::class, 'print'])->name('print');
    Route::get('/{promissoryNote}/pdf', [PromissoryNoteController::class, 'pdf'])->name('pdf');
});

// ============================================================================
// GUARANTEES (existing routes - may need enhancement)
// ============================================================================
Route::prefix('guarantees')->name('guarantees.')->group(function () {
    // Standard CRUD
    Route::get('/', [GuaranteeController::class, 'index'])->name('index');
    Route::get('/create', [GuaranteeController::class, 'create'])->name('create');
    Route::post('/', [GuaranteeController::class, 'store'])->name('store');
    Route::get('/{guarantee}', [GuaranteeController::class, 'show'])->name('show');
    Route::get('/{guarantee}/edit', [GuaranteeController::class, 'edit'])->name('edit');
    Route::put('/{guarantee}', [GuaranteeController::class, 'update'])->name('update');
    Route::delete('/{guarantee}', [GuaranteeController::class, 'destroy'])->name('destroy');
    
    // Status operations
    Route::post('/{guarantee}/approve', [GuaranteeController::class, 'approve'])->name('approve');
    Route::get('/{guarantee}/renew', [GuaranteeController::class, 'showRenewForm'])->name('show-renew');
    Route::post('/{guarantee}/renew', [GuaranteeController::class, 'renew'])->name('renew');
    Route::get('/{guarantee}/release', [GuaranteeController::class, 'showReleaseForm'])->name('show-release');
    Route::post('/{guarantee}/release', [GuaranteeController::class, 'release'])->name('release');
    
    // Reports
    Route::get('/expiring', [GuaranteeController::class, 'expiring'])->name('expiring');
    Route::get('/statistics', [GuaranteeController::class, 'statistics'])->name('statistics');
    Route::get('/reports', [GuaranteeController::class, 'reports'])->name('reports');
    
    // Print & Export (add if not exists)
    Route::get('/{guarantee}/print', [GuaranteeController::class, 'print'])->name('print');
    Route::get('/{guarantee}/pdf', [GuaranteeController::class, 'pdf'])->name('pdf');
});

// ============================================================================
// PAYMENT TEMPLATES
// ============================================================================
Route::prefix('payment-templates')->name('payment-templates.')->group(function () {
    // Standard CRUD
    Route::get('/', [PaymentTemplateController::class, 'index'])->name('index');
    Route::get('/create', [PaymentTemplateController::class, 'create'])->name('create');
    Route::post('/', [PaymentTemplateController::class, 'store'])->name('store');
    Route::get('/{paymentTemplate}', [PaymentTemplateController::class, 'show'])->name('show');
    Route::get('/{paymentTemplate}/edit', [PaymentTemplateController::class, 'edit'])->name('edit');
    Route::put('/{paymentTemplate}', [PaymentTemplateController::class, 'update'])->name('update');
    Route::delete('/{paymentTemplate}', [PaymentTemplateController::class, 'destroy'])->name('destroy');
    
    // Additional operations
    Route::post('/{paymentTemplate}/preview', [PaymentTemplateController::class, 'preview'])->name('preview');
    Route::post('/{paymentTemplate}/duplicate', [PaymentTemplateController::class, 'duplicate'])->name('duplicate');
});
