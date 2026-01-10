# Payment Instruments Module - Quick Start Guide

## 🚀 Installation

### Step 1: Run Migrations
```bash
php artisan migrate
```

This will create/enhance the following tables:
- `currencies` (enhanced)
- `exchange_rates` (new)
- `checks` (new)
- `promissory_notes` (new)
- `payment_templates` (new)
- `guarantees` (enhanced)
- `branches` (enhanced)
- `bank_accounts` (enhanced)

### Step 2: Run Seeders
```bash
php artisan db:seed --class=CurrencySeeder
php artisan db:seed --class=PaymentTemplateSeeder
php artisan db:seed --class=PaymentInstrumentsPermissionsSeeder
```

This will populate:
- 10 currencies (JOD, SAR, USD, EUR, AED, EGP, QAR, KWD, BHD, OMR)
- 4 default payment templates
- 38 permissions across 4 roles

### Step 3: Add Routes
In `routes/web.php`, add within your authenticated middleware group:

```php
Route::middleware('auth')->group(function () {
    // ... your existing routes ...
    
    // Payment Instruments Module
    require __DIR__.'/payment_instruments_routes.php';
});
```

### Step 4: Run Tests (Optional)
```bash
php artisan test --filter=AmountToWordsServiceTest
```

## 📋 Quick Examples

### Create a Check
```php
use App\Models\Check;
use App\Services\AmountToWordsService;

$check = Check::create([
    'check_number' => Check::generateCheckNumber($bankAccountId),
    'bank_account_id' => 1,
    'issue_date' => now(),
    'due_date' => now()->addDays(30),
    'check_type' => Check::TYPE_POST_DATED,
    'amount' => 15000.500,
    'currency_id' => 1, // JOD
    'exchange_rate' => 0.710000,
    'amount_in_base_currency' => 15000.500 * 0.710000,
    'amount_words' => AmountToWordsService::convertToArabic(15000.500, 'JOD', 3),
    'amount_words_en' => AmountToWordsService::convertToEnglish(15000.500, 'JOD', 3),
    'beneficiary' => 'ABC Construction Company',
    'description' => 'Advance payment for project',
    'status' => Check::STATUS_PENDING,
    'created_by' => auth()->id(),
]);
```

### Convert Amount to Words
```php
use App\Services\AmountToWordsService;

// Arabic
$arabic = AmountToWordsService::convertToArabic(15000.500, 'JOD', 3);
// Output: "خمسة عشر ألفاً وخمسمئة فلس دينار أردني فقط لا غير"

// English
$english = AmountToWordsService::convertToEnglish(15000.00, 'SAR', 2);
// Output: "Fifteen Thousand Saudi Riyal Only"
```

### Create Exchange Rate
```php
use App\Models\ExchangeRate;

ExchangeRate::create([
    'from_currency_id' => 1, // JOD
    'to_currency_id' => 2,   // SAR
    'rate' => 0.710000,
    'date' => now(),
    'source' => 'manual',
    'created_by' => auth()->id(),
]);
```

### Convert Currency
```php
use App\Models\ExchangeRate;

$amount = 1000; // JOD
$converted = ExchangeRate::convert($amount, $jodId, $sarId, now());
// Returns amount in SAR
```

## 🎨 UI Implementation

You need to create Blade views for the following routes:

### Currencies
- `resources/views/currencies/index.blade.php` (already exists)
- `resources/views/currencies/create.blade.php` (already exists)
- `resources/views/currencies/edit.blade.php` (already exists)

### Exchange Rates
- `resources/views/exchange-rates/index.blade.php`
- `resources/views/exchange-rates/create.blade.php`
- `resources/views/exchange-rates/edit.blade.php`
- `resources/views/exchange-rates/bulk-update.blade.php`

### Checks
- `resources/views/checks/index.blade.php`
- `resources/views/checks/create.blade.php`
- `resources/views/checks/edit.blade.php`
- `resources/views/checks/show.blade.php`
- `resources/views/checks/print.blade.php`
- `resources/views/checks/pdf.blade.php`
- `resources/views/checks/due-soon.blade.php`
- `resources/views/checks/overdue.blade.php`

### Promissory Notes
- `resources/views/promissory-notes/index.blade.php`
- `resources/views/promissory-notes/create.blade.php`
- `resources/views/promissory-notes/edit.blade.php`
- `resources/views/promissory-notes/show.blade.php`
- `resources/views/promissory-notes/print.blade.php`
- `resources/views/promissory-notes/pdf.blade.php`

### Payment Templates
- `resources/views/payment-templates/index.blade.php`
- `resources/views/payment-templates/create.blade.php`
- `resources/views/payment-templates/edit.blade.php`
- `resources/views/payment-templates/show.blade.php`
- `resources/views/payment-templates/preview.blade.php`

## 🔐 Permissions

The module includes these permission groups:

### Currency Permissions
- `view_currencies`
- `create_currencies`
- `edit_currencies`
- `delete_currencies`
- `manage_currencies`
- `update_exchange_rates`

### Check Permissions
- `view_checks`
- `create_checks`
- `edit_checks`
- `delete_checks`
- `approve_checks`
- `cancel_checks`
- `print_checks`
- `clear_checks`

### Promissory Note Permissions
- `view_promissory_notes`
- `create_promissory_notes`
- `edit_promissory_notes`
- `delete_promissory_notes`
- `approve_promissory_notes`
- `print_promissory_notes`

### And more... (See PaymentInstrumentsPermissionsSeeder.php)

## 📊 Available Routes

After adding the routes file, these endpoints will be available:

```
GET    /currencies
GET    /exchange-rates
GET    /checks
GET    /promissory-notes
GET    /guarantees (existing, enhanced)
GET    /payment-templates

... and many more (see payment_instruments_routes.php)
```

## 🧪 Testing

The module includes comprehensive unit tests:

```bash
# Run all tests
php artisan test

# Run specific test
php artisan test --filter=AmountToWordsServiceTest

# Run with coverage
php artisan test --coverage
```

## 🔧 Configuration

### Base Currency
The seeder sets SAR as the base currency by default. To change:

```php
// In CurrencySeeder.php, set is_base => true for your preferred currency
```

### Decimal Places
- JOD, KWD, BHD: 3 decimal places
- All others: 2 decimal places

Configure in the currencies table's `decimal_places` column.

## 📱 API Integration (Future)

The controllers are ready for API integration. Add API routes in `routes/api.php`:

```php
Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('checks', CheckController::class);
    Route::apiResource('promissory-notes', PromissoryNoteController::class);
    // ... etc
});
```

## 🎯 Integration with Existing Modules

### Purchase Orders
```php
$check = Check::create([
    // ... other fields ...
    'reference_type' => 'App\Models\PurchaseOrder',
    'reference_id' => $po->id,
]);
```

### Projects
```php
$check->project_id = $project->id;
```

### Bank Accounts
```php
$check->bank_account_id = $bankAccount->id;
```

## 📞 Support

For issues or questions:
1. Check `PAYMENT_INSTRUMENTS_MODULE_README.md` for detailed documentation
2. Review the code comments in models and controllers
3. Examine the test cases for usage examples
4. Check the seeder files for default data structures

## ✅ Checklist

- [ ] Migrations run successfully
- [ ] Seeders executed
- [ ] Routes added
- [ ] Views created (your responsibility)
- [ ] Permissions assigned to users
- [ ] Navigation menu updated
- [ ] Tests passing
- [ ] Integration with existing modules tested

## 🎉 You're Ready!

The backend is complete. Now implement the UI views following your application's design patterns.
