# Time Bar Protection System

## Overview

The Time Bar Protection System allows you to define time-based protection rules for different entity types in your CEMS application. This prevents modifications to records after a certain time period has elapsed.

## Features

- **Flexible Protection Rules**: Define different protection periods for different entity types (invoices, contracts, payments, etc.)
- **Multiple Protection Types**:
  - `view_only`: Record can only be viewed, not edited
  - `full_lock`: Record cannot be viewed, edited, or deleted
  - `approval_required`: Changes require approval (to be implemented)
- **Company-Specific Settings**: Set global rules or company-specific overrides
- **Role-Based Bypass**: Certain roles can bypass protection rules
- **Soft Delete Support**: All settings support soft deletion

## Installation

### 1. Run Migration

```bash
php artisan migrate
```

This creates the `time_bar_protection_settings` table.

### 2. Seed Default Data (Optional)

```bash
php artisan db:seed --class=TimeBarProtectionSettingSeeder
```

This seeds default protection settings for common entity types.

## Usage

### Using the Trait

Add the `HasTimeBarProtection` trait to any model you want to protect:

```php
use App\Traits\HasTimeBarProtection;

class Invoice extends Model
{
    use HasTimeBarProtection;
    
    // Optional: Override the entity type
    protected function getEntityType(): string
    {
        return 'invoice';
    }
}
```

Then check protection status:

```php
$invoice = Invoice::find(1);

if ($invoice->isTimeBarProtected()) {
    // Handle protected record
}

if ($invoice->canEdit()) {
    // Allow editing
}
```

### Using the Service

Inject the `TimeBarProtectionService` into your controllers:

```php
use App\Services\TimeBarProtectionService;

public function update(Request $request, Invoice $invoice, TimeBarProtectionService $protectionService)
{
    if (!$protectionService->canEdit($invoice, 'invoice')) {
        return response()->json(['error' => 'This invoice is protected'], 403);
    }
    
    // Update the invoice
}
```

### Using Middleware

Apply the `CheckTimeBarProtection` middleware to routes:

```php
Route::put('/invoices/{invoice}', [InvoiceController::class, 'update'])
    ->middleware('check.time.bar.protection:invoice,edit');

Route::delete('/invoices/{invoice}', [InvoiceController::class, 'destroy'])
    ->middleware('check.time.bar.protection:invoice,delete');
```

## API Endpoints

### List Settings

```
GET /api/time-bar-protection
```

Query parameters:
- `company_id`: Filter by company
- `entity_type`: Filter by entity type
- `is_active`: Filter by active status

### Create Setting

```
POST /api/time-bar-protection
```

Body:
```json
{
  "company_id": 1,
  "entity_type": "invoice",
  "protection_days": 90,
  "protection_type": "view_only",
  "is_active": true,
  "description": "Invoices older than 90 days are read-only",
  "excluded_roles": ["super-admin", "financial-controller"]
}
```

### Update Setting

```
PUT /api/time-bar-protection/{id}
```

### Delete Setting

```
DELETE /api/time-bar-protection/{id}
```

### Toggle Active Status

```
PATCH /api/time-bar-protection/{id}/toggle-active
```

### Check Protection Status

```
POST /api/time-bar-protection/check
```

Body:
```json
{
  "entity_type": "invoice",
  "company_id": 1,
  "created_at": "2025-01-01T00:00:00Z"
}
```

## Database Schema

### time_bar_protection_settings

| Column | Type | Description |
|--------|------|-------------|
| id | bigint | Primary key |
| company_id | bigint | Foreign key to companies (nullable) |
| entity_type | string | Type of entity (invoice, contract, etc.) |
| protection_days | integer | Days after which protection applies |
| protection_type | enum | view_only, full_lock, approval_required |
| is_active | boolean | Whether the setting is active |
| description | text | Human-readable description |
| excluded_roles | json | Roles that can bypass protection |
| metadata | json | Additional settings |
| created_at | timestamp | |
| updated_at | timestamp | |
| deleted_at | timestamp | Soft delete |

## Testing

Run the tests:

```bash
php artisan test --filter=TimeBarProtection
```

## Examples

### Example 1: Protect Invoices After 90 Days

```php
TimeBarProtectionSetting::create([
    'entity_type' => 'invoice',
    'protection_days' => 90,
    'protection_type' => 'view_only',
    'is_active' => true,
    'description' => 'Invoices become read-only after 90 days',
    'excluded_roles' => ['super-admin', 'cfo'],
]);
```

### Example 2: Company-Specific Contract Protection

```php
TimeBarProtectionSetting::create([
    'company_id' => 5,
    'entity_type' => 'contract',
    'protection_days' => 180,
    'protection_type' => 'full_lock',
    'is_active' => true,
    'description' => 'Contracts are fully locked after 6 months',
]);
```

### Example 3: Check Protection in Controller

```php
public function update(Request $request, Invoice $invoice)
{
    $protectionInfo = app(TimeBarProtectionService::class)
        ->getProtectionInfo($invoice, 'invoice');
    
    if ($protectionInfo['is_protected'] && !$protectionInfo['can_bypass']) {
        return redirect()->back()
            ->with('error', "This invoice is {$protectionInfo['protection_type']} protected.");
    }
    
    // Proceed with update
}
```

## Configuration

No additional configuration is required. The system works out of the box after running migrations.

## Best Practices

1. **Start with Global Settings**: Create global protection rules first, then add company-specific overrides as needed
2. **Use Appropriate Protection Types**: Choose the right protection type for your use case
3. **Define Excluded Roles**: Always specify which roles should bypass protection
4. **Test Thoroughly**: Test protection rules with different user roles and record ages
5. **Document Entity Types**: Maintain a list of entity types used in your application

## Troubleshooting

### Protection Not Working

- Ensure the setting is active (`is_active = true`)
- Check that the entity type matches exactly
- Verify the record's creation date is older than `protection_days`

### Users Can Still Edit Protected Records

- Check if the user has a role in `excluded_roles`
- Verify middleware is applied to the route
- Ensure the trait or service is being used correctly

## Future Enhancements

- [ ] Implement approval workflow for `approval_required` type
- [ ] Add notification system when records become protected
- [ ] Create UI for managing protection settings
- [ ] Add audit logging for protection bypass attempts
