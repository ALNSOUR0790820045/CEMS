# Contracts Module - CEMS ERP

## Overview
Complete contract management system for construction projects with support for change orders, amendments, clauses, and milestones tracking.

## Features Implemented

### ✅ Core Features
- **Contract CRUD Operations**: Create, read, update, delete contracts
- **Auto-generated Contract Codes**: CNT-YYYY-XXXX format
- **Change Order Management**: Track value and time impacts
- **Amendment Management**: Version control with approval workflow
- **Contract Clauses**: Organize by category with critical marking
- **Milestone Tracking**: Monitor project progress and payments
- **Multi-tenant Support**: Company-level data isolation
- **Soft Deletes**: Recoverable data deletion
- **File Attachments**: Upload and manage contract documents

### 📊 Dashboard & KPIs
- Active Contracts Count
- Total Contract Value
- Pending Change Orders
- Expiring Soon Contracts

### 🔍 Filters & Search
- Search by contract code, number, or title
- Filter by status, client, type
- Advanced filtering capabilities

### 🎨 User Interface
- Professional RTL Arabic interface
- Responsive design
- KPI dashboard cards
- Inline validation
- Status badges with color coding

## Database Schema

### Dependencies
1. **clients** - Client/customer information
2. **currencies** - Multi-currency support
3. **g_l_accounts** - GL account integration

### Main Tables
1. **contracts** - Main contract records
2. **contract_change_orders** - Change orders with approval workflow
3. **contract_amendments** - Contract amendments history
4. **contract_clauses** - Contract terms and conditions
5. **contract_milestones** - Project milestones and payments

## Installation & Setup

### 1. Run Migrations
```bash
php artisan migrate
```

This will create all necessary tables:
- clients
- currencies
- g_l_accounts
- contracts
- contract_change_orders
- contract_amendments
- contract_clauses
- contract_milestones

### 2. Seed Sample Data (Optional)
```bash
php artisan db:seed --class=ContractsModuleSeeder
```

This creates:
- 3 currencies (SAR, USD, EUR)
- 2 GL accounts
- 3 sample clients
- 3 sample contracts
- Sample clauses and milestones for one contract

### 3. Configure Storage
Ensure the storage link is created for file uploads:
```bash
php artisan storage:link
```

### 4. Set Permissions
Configure file upload permissions:
```bash
chmod -R 775 storage/app/public/contracts
```

## Usage Guide

### Creating a Contract

1. Navigate to **المالية > العقود** (Finance > Contracts)
2. Click **عقد جديد** (New Contract)
3. Fill in the form with:
   - **Basic Information**: Contract number, title, client, type, category
   - **Financial Terms**: Value, currency, retention %, advance payment %
   - **Dates**: Signing, commencement, completion dates
   - **Management**: Contract manager, project manager
   - **Scope**: Work scope, payment terms, penalty clauses
   - **Attachments**: Upload signed contract document
4. Click **حفظ العقد** (Save Contract)

### Contract Statuses

- **مسودة (draft)**: Initial state, can be edited and deleted
- **قيد التفاوض (under_negotiation)**: Being negotiated, can be edited
- **موقع (signed)**: Signed but not started
- **نشط (active)**: Currently active
- **معلق (on_hold)**: Temporarily paused
- **مكتمل (completed)**: Work completed
- **منتهي (terminated)**: Terminated early
- **مغلق (closed)**: Closed and archived

### Change Orders

Change orders are managed through the Contract model:

```php
// Create a change order
$changeOrder = ContractChangeOrder::create([
    'contract_id' => $contract->id,
    'title' => 'Additional Works',
    'description' => 'Description of changes',
    'reason' => 'Client request',
    'change_type' => 'addition',
    'financial_impact' => 'increase',
    'value_change' => 500000.00,
    'time_impact' => 'extension',
    'days_change' => 30,
    'status' => 'draft',
    'submitted_by_id' => auth()->id(),
    'company_id' => auth()->user()->company_id,
]);

// Submit for approval
$changeOrder->submit();

// Approve
$changeOrder->approve($userId);

// Implement (updates contract values)
$changeOrder->implement();
```

### Amendments

```php
// Create an amendment
$amendment = ContractAmendment::create([
    'contract_id' => $contract->id,
    'title' => 'Contract Extension',
    'description' => 'Extend contract period',
    'amendment_date' => now(),
    'effective_date' => now()->addDays(7),
    'previous_contract_value' => $contract->current_contract_value,
    'new_contract_value' => 55000000.00,
    'previous_completion_date' => $contract->completion_date,
    'new_completion_date' => $contract->completion_date->addMonths(3),
    'status' => 'draft',
    'company_id' => auth()->user()->company_id,
]);

// Approve amendment (automatically updates contract)
$amendment->approve($userId);
```

## Model Relationships

### Contract Model

```php
// Relationships
$contract->client              // BelongsTo Client
$contract->currency            // BelongsTo Currency
$contract->contractManager     // BelongsTo User
$contract->projectManager      // BelongsTo User
$contract->glRevenueAccount    // BelongsTo GLAccount
$contract->glReceivableAccount // BelongsTo GLAccount
$contract->company             // BelongsTo Company
$contract->changeOrders        // HasMany ContractChangeOrder
$contract->amendments          // HasMany ContractAmendment
$contract->clauses             // HasMany ContractClause
$contract->milestones          // HasMany ContractMilestone

// Scopes
Contract::active()                    // Active contracts
Contract::byStatus('active')          // Filter by status
Contract::byClient($clientId)         // Filter by client
Contract::byType('lump_sum')          // Filter by type
Contract::expiringSoon(30)            // Expiring in 30 days

// Accessors
$contract->days_remaining             // Days until completion
$contract->progress_percentage        // Project progress %
$contract->is_expired                 // Boolean: past completion date
$contract->is_near_expiry             // Boolean: within 30 days of expiry
```

## API Endpoints (Ready for Implementation)

Routes are configured in `routes/web.php`:

```php
// Contracts
GET    /contracts                         // List all contracts
GET    /contracts/create                  // Show create form
POST   /contracts                         // Store new contract
GET    /contracts/{id}                    // Show contract details
GET    /contracts/{id}/edit               // Show edit form
PUT    /contracts/{id}                    // Update contract
DELETE /contracts/{id}                    // Delete contract (draft only)
POST   /contracts/{id}/clone              // Clone contract
GET    /contracts/generate-code           // Generate new contract code
```

## Validation Rules

### Contract Validation
- `contract_number`: Required, string, max 255
- `contract_title`: Required, string, max 255
- `client_id`: Required, must exist in clients table
- `contract_type`: Required, enum
- `contract_value`: Required, numeric, min 0
- `currency_id`: Required, must exist
- `signing_date`: Required, date
- `commencement_date`: Required, date, after_or_equal signing_date
- `completion_date`: Required, date, after commencement_date
- `retention_percentage`: Numeric, min 0, max 100
- `contract_manager_id`: Required, must exist

### Change Order Validation
- `title`: Required, string, max 255
- `description`: Required, string
- `reason`: Required, string
- `change_type`: Required, enum
- `financial_impact`: Required, enum
- `value_change`: Required, numeric
- `time_impact`: Required, enum
- `days_change`: Required, integer

## Business Logic

### Automatic Contract Code Generation
```php
// Format: CNT-YYYY-XXXX
// Example: CNT-2026-0001
$code = Contract::generateContractCode();
```

### Contract Duration Calculation
Contract duration is automatically calculated when creating or updating:
```php
$days = commencement_date->diffInDays(completion_date);
```

### Change Order Implementation
When a change order is implemented:
1. Updates `current_contract_value` with value_change
2. Updates `total_change_orders_value`
3. Adjusts `completion_date` if time extension
4. Sets change order status to 'implemented'

### Amendment Approval
When an amendment is approved:
1. Updates contract `current_contract_value`
2. Updates contract `completion_date` if changed
3. Sets amendment status to 'approved'
4. Records approval timestamp and user

## Security & Authorization

### Authorization Checks
- Company-level data isolation (multi-tenant)
- User must belong to the same company as the contract
- Edit restrictions based on contract status
- Delete only allowed for draft contracts

### File Upload Security
- Allowed file types: PDF, DOC, DOCX
- Maximum file size: 10MB
- Files stored in: `storage/app/public/contracts/`
- Old files are deleted when replaced

## Customization

### Adding Custom Contract Types
Edit the enum in migration:
```php
$table->enum('contract_type', [
    'lump_sum', 
    'unit_price', 
    'cost_plus', 
    'design_build', 
    'epc', 
    'bot',
    'your_custom_type' // Add here
]);
```

### Adding Custom Clause Categories
Edit the enum in `contract_clauses` migration:
```php
$table->enum('clause_category', [
    'payment',
    'penalties',
    'warranties',
    'termination',
    'scope',
    'time',
    'quality',
    'safety',
    'other',
    'your_custom_category' // Add here
]);
```

## Troubleshooting

### Issue: Contracts not showing
**Solution**: Ensure user has `company_id` and belongs to a company

### Issue: File upload fails
**Solution**: 
```bash
php artisan storage:link
chmod -R 775 storage/app/public
```

### Issue: Validation errors in Arabic
**Solution**: Translations are defined in FormRequest `attributes()` method

### Issue: Contract code not auto-generating
**Solution**: Check that the `boot()` method in Contract model is working

## Next Steps

### Recommended Enhancements
1. **Permissions**: Implement Spatie permissions for fine-grained access control
2. **API Resources**: Create API resources for JSON responses
3. **Change Order Controller**: Separate controller for change order management
4. **Amendment Controller**: Separate controller for amendment workflow
5. **Email Notifications**: Send notifications on status changes
6. **PDF Generation**: Generate contract PDFs with Laravel DOMPDF
7. **Reports**: Contract register, performance reports, expiring contracts
8. **Dashboard Widgets**: Add contract widgets to main dashboard
9. **Search Enhancement**: Full-text search with Laravel Scout
10. **Audit Trail**: Track all changes with Laravel Auditing

## Support & Documentation

For more information:
- Laravel Documentation: https://laravel.com/docs
- Spatie Permissions: https://spatie.be/docs/laravel-permission
- Carbon Dates: https://carbon.nesbot.com/docs/

## License

This module is part of the CEMS ERP system and follows the same license terms.
