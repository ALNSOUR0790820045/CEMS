# General Ledger (GL) Module Documentation

## Overview
The General Ledger module is the foundation of the CEMS ERP financial system. It tracks all financial transactions, manages the chart of accounts, and provides the basis for financial reporting.

## Features Implemented

### 1. Database Schema
- **Currencies**: Multi-currency support with exchange rates
- **Departments**: Organizational units for tracking
- **Projects**: Project-based accounting
- **Cost Centers**: Cost allocation centers
- **GL Accounts**: Chart of accounts with hierarchical structure
- **GL Fiscal Years**: Fiscal year management
- **GL Periods**: Monthly periods within fiscal years
- **GL Journal Entries**: Financial transactions
- **GL Journal Entry Lines**: Individual debit/credit lines

### 2. Models & Relationships
All models include proper relationships, scopes, and business logic:
- Currency, Department, Project, CostCenter
- GLAccount (with tree structure support)
- GLFiscalYear, GLPeriod
- GLJournalEntry (with workflow states)
- GLJournalEntryLine

### 3. Journal Entry Workflow
The module implements a complete approval workflow:

```
Draft → Pending Approval → Approved → Posted → (Reversed)
```

**Available Actions:**
- **Draft**: Create, edit, delete, submit for approval
- **Pending Approval**: Approve or reject
- **Approved**: Post to ledger
- **Posted**: Reverse (creates reversal entry)

### 4. Business Services

#### JournalNumberGenerator
Generates unique journal numbers in format: `JE-YYYY-MM-XXXX`
- Example: `JE-2026-01-0001`

#### JournalEntryPostingService
Handles posting entries to the ledger:
- Validates entry is balanced
- Checks period is open
- Updates GL account balances
- Fires `JournalEntryPosted` event

#### JournalEntryReversalService
Creates reversal entries:
- Swaps debit/credit amounts
- Links original and reversal entries
- Auto-approves and posts reversal

### 5. Validation Rules

**Journal Entries:**
- Entry date required and must be in open period
- Description required
- Must have at least 2 lines (1 debit, 1 credit)
- Total debit must equal total credit
- Each line must have either debit OR credit (not both)

**GL Accounts:**
- Account code required and unique
- Account name required
- Account type required
- Parent account must be same type

### 6. Controllers

#### GLAccountController
- Chart of Accounts management
- Tree view of accounts
- Account ledger view
- CRUD operations

#### GLJournalEntryController
- List journal entries with filters
- Create/edit journal entries
- Submit for approval
- Approve/reject entries
- Post to ledger
- Reverse posted entries

### 7. Routes

All GL routes are prefixed with `/gl`:

```php
// Chart of Accounts
GET    /gl/accounts                     - List accounts
GET    /gl/accounts/create              - Create form
POST   /gl/accounts                     - Store account
GET    /gl/accounts/{id}                - Show account
GET    /gl/accounts/{id}/edit           - Edit form
PUT    /gl/accounts/{id}                - Update account
DELETE /gl/accounts/{id}                - Delete account
GET    /gl/accounts/{id}/ledger         - Account ledger

// Journal Entries
GET    /gl/journal-entries              - List entries
GET    /gl/journal-entries/create       - Create form
POST   /gl/journal-entries              - Store entry
GET    /gl/journal-entries/{id}         - Show entry
GET    /gl/journal-entries/{id}/edit    - Edit form
PUT    /gl/journal-entries/{id}         - Update entry
DELETE /gl/journal-entries/{id}         - Delete entry
POST   /gl/journal-entries/{id}/submit  - Submit for approval
POST   /gl/journal-entries/{id}/approve - Approve entry
POST   /gl/journal-entries/{id}/reject  - Reject entry
POST   /gl/journal-entries/{id}/post    - Post to ledger
POST   /gl/journal-entries/{id}/reverse - Reverse entry
```

### 8. Views Created
- `gl/journal-entries/index.blade.php` - List with filters
- `gl/journal-entries/show.blade.php` - Detailed view with actions
- `gl/accounts/index.blade.php` - Chart of accounts tree view

## Usage Examples

### Creating a Journal Entry

1. Navigate to `/gl/journal-entries/create`
2. Fill in header information:
   - Entry date
   - Journal type (general, opening_balance, etc.)
   - Description
   - Currency
3. Add journal lines (minimum 2):
   - Select GL account
   - Enter debit OR credit amount
   - Add description (optional)
4. Ensure total debit = total credit
5. Save as draft

### Posting Workflow

1. **Draft**: Entry is created and can be edited
2. **Submit**: User submits entry for approval
3. **Approve**: Manager/supervisor approves the entry
4. **Post**: Entry is posted to the ledger, updating account balances
5. **Reverse** (if needed): Create a reversal entry

### Account Balance Updates

When a journal entry is posted:
- **Assets & Expenses**: Debit increases balance, Credit decreases
- **Liabilities, Equity & Revenue**: Credit increases balance, Debit decreases

## Technical Details

### Database Precision
- All monetary amounts use `decimal(18,2)` for precision
- Exchange rates use `decimal(10,4)`

### Multi-Currency Support
- Each entry has a currency and exchange rate
- Lines store both foreign and base currency amounts
- `base_currency_debit/credit` used for GL account balance updates

### Soft Deletes
- Draft journal entries can be soft deleted
- Accounts can be soft deleted (if no transactions exist)

### Events
- `JournalEntryPosted`: Fired when entry is posted
- `JournalEntryReversed`: Fired when entry is reversed

## Next Steps (Not Yet Implemented)

### Missing Features:
1. **Views**: Create, edit forms for journal entries and accounts
2. **Fiscal Year Controller**: Full fiscal year management
3. **API Resources**: JSON API responses
4. **Reports**: Trial Balance, General Ledger Report
5. **Permissions**: Role-based access control
6. **Tests**: Unit and feature tests
7. **Account Ledger View**: Complete ledger view
8. **Period Management**: Open/close periods

### Recommended Order of Implementation:
1. Create remaining views (create/edit forms)
2. Add fiscal year management
3. Implement period management
4. Add reporting features
5. Add permissions and middleware
6. Write comprehensive tests

## Database Migration

Before using the module, run migrations:

```bash
php artisan migrate
```

This will create all necessary tables for the GL module.

## Dependencies

The GL module depends on:
- Laravel 11+
- PHP 8.2+
- PostgreSQL (or MySQL/SQLite)
- Existing: Users, Companies tables

## Security Considerations

- All monetary operations use BC Math for precision
- Transactions use database ACID properties
- Only draft entries can be edited/deleted
- Posted entries cannot be modified (only reversed)
- Period locking prevents posting to closed periods

## Support

For issues or questions, please refer to the main CEMS documentation or create an issue in the repository.
