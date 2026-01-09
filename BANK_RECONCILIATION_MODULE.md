# Bank Reconciliation Module (تسوية البنوك)

## Overview
This module provides a comprehensive bank reconciliation system that allows matching bank statements with accounting records and identifying discrepancies.

## Database Tables

### 1. bank_accounts
Enhanced existing table with:
- `current_balance` (decimal) - رصيد الدفاتر
- `bank_balance` (decimal) - رصيد البنك
- Soft deletes support

### 2. bank_statements
Stores imported bank statements:
- `statement_number` (auto: BS-YYYY-XXXX)
- `bank_account_id` (FK)
- `statement_date`
- `period_from` / `period_to`
- `opening_balance` / `closing_balance`
- `total_deposits` / `total_withdrawals`
- `status` (imported, reconciling, reconciled)

### 3. bank_statement_lines
Individual transactions in bank statements:
- `bank_statement_id` (FK)
- `transaction_date` / `value_date`
- `description` / `reference_number`
- `debit_amount` / `credit_amount`
- `balance`
- `is_matched` flag
- `matched_transaction_type` / `matched_transaction_id`

### 4. bank_reconciliations
Reconciliation documents:
- `reconciliation_number` (auto: BR-YYYY-XXXX)
- `bank_account_id` (FK)
- `reconciliation_date`
- `period_from` / `period_to`
- `book_balance` / `bank_balance`
- `adjusted_book_balance` / `adjusted_bank_balance`
- `difference`
- `status` (draft, in_progress, completed, approved)
- `prepared_by_id` / `approved_by_id`

### 5. reconciliation_items
Reconciliation adjustments:
- `bank_reconciliation_id` (FK)
- `item_type` (outstanding_check, deposit_in_transit, bank_charge, bank_interest, error, other)
- `description` / `amount`
- `transaction_date` / `reference_number`
- `is_cleared` / `cleared_date`

## API Endpoints

### Bank Accounts
```
GET    /api/bank-accounts                      - List all bank accounts
POST   /api/bank-accounts                      - Create new bank account
GET    /api/bank-accounts/{id}                 - Get bank account details
PUT    /api/bank-accounts/{id}                 - Update bank account
DELETE /api/bank-accounts/{id}                 - Delete bank account
GET    /api/bank-accounts/{id}/balance         - Get balance information
GET    /api/bank-accounts/{id}/transactions    - Get transactions
```

### Bank Statements
```
GET    /api/bank-statements                    - List all statements
POST   /api/bank-statements                    - Create new statement
GET    /api/bank-statements/{id}               - Get statement details
PUT    /api/bank-statements/{id}               - Update statement
DELETE /api/bank-statements/{id}               - Delete statement
POST   /api/bank-statements/import             - Import CSV file
POST   /api/bank-statements/{id}/auto-match    - Auto-match transactions
```

### Bank Reconciliations
```
GET    /api/bank-reconciliations               - List all reconciliations
POST   /api/bank-reconciliations               - Create new reconciliation
GET    /api/bank-reconciliations/{id}          - Get reconciliation details
PUT    /api/bank-reconciliations/{id}          - Update reconciliation
DELETE /api/bank-reconciliations/{id}          - Delete reconciliation
POST   /api/bank-reconciliations/{id}/match-item    - Add reconciliation item
POST   /api/bank-reconciliations/{id}/unmatch-item  - Remove reconciliation item
POST   /api/bank-reconciliations/{id}/complete      - Complete reconciliation
POST   /api/bank-reconciliations/{id}/approve       - Approve reconciliation
```

### Reports
```
GET /api/reports/bank-reconciliation-report    - Reconciliation report
GET /api/reports/outstanding-checks            - Outstanding checks report
GET /api/reports/deposits-in-transit           - Deposits in transit report
GET /api/reports/bank-book                     - Bank book report
```

## Features

### 1. CSV Import
Import bank statements from CSV files with automatic line parsing:
- Date, Description, Reference, Debit, Credit
- Running balance calculation
- Total deposits and withdrawals

### 2. Auto-Matching
Automatically match statement lines with:
- AR Receipts (by date and amount)
- AP Payments (by date and amount)
- Tracks matched transactions

### 3. Reconciliation Process
Four-stage workflow:
1. **Draft** - Initial creation
2. **In Progress** - Adding reconciliation items
3. **Completed** - Ready for approval
4. **Approved** - Final approved state

### 4. Reconciliation Items
Track differences between books and bank:
- **Outstanding Checks** - Checks written but not cleared
- **Deposits in Transit** - Deposits made but not recorded by bank
- **Bank Charges** - Fees not yet recorded in books
- **Bank Interest** - Interest earned not yet recorded
- **Errors** - Mistakes requiring correction
- **Other** - Other adjustments

### 5. Balance Calculations
Automatic calculation of:
- Adjusted book balance
- Adjusted bank balance
- Difference (should be zero when reconciled)

### 6. Reports
- **Reconciliation Report** - Full reconciliation details with all adjustments
- **Outstanding Checks** - All uncleared checks
- **Deposits in Transit** - All uncleared deposits
- **Bank Book** - Transaction history for a period

## Models

### BankAccount
```php
- Relationships: company, currency, glAccount, bankStatements, bankReconciliations
- Scopes: active(), primary()
- Soft deletes enabled
```

### BankStatement
```php
- Auto-generates statement number: BS-YYYY-XXXX
- Relationships: bankAccount, company, reconciledBy, lines
- Scopes: byStatus(), byBankAccount()
```

### BankStatementLine
```php
- Relationships: bankStatement
- Scopes: matched(), unmatched()
- Computed: amount (credit - debit)
```

### BankReconciliation
```php
- Auto-generates reconciliation number: BR-YYYY-XXXX
- Relationships: bankAccount, company, preparedBy, approvedBy, items
- Scopes: byStatus(), byBankAccount()
```

### ReconciliationItem
```php
- Relationships: bankReconciliation
- Scopes: byType(), cleared(), uncleared()
```

## Request Validation

All API requests are validated with FormRequest classes:
- `StoreBankAccountRequest`
- `UpdateBankAccountRequest`
- `StoreBankStatementRequest`
- `StoreBankReconciliationRequest`
- `UpdateBankReconciliationRequest`

## Testing

Comprehensive test coverage:
- `BankAccountTest` - CRUD and balance operations
- `BankStatementTest` - Statement creation and line management
- `BankReconciliationTest` - Full workflow including approval restrictions

## Usage Examples

### Creating a Bank Account
```http
POST /api/bank-accounts
Content-Type: application/json

{
  "account_number": "ACC-001",
  "account_name": "Main Operating Account",
  "bank_name": "Al Rajhi Bank",
  "branch": "Main Branch",
  "currency_id": 1,
  "company_id": 1
}
```

### Importing Bank Statement
```http
POST /api/bank-statements/import
Content-Type: multipart/form-data

file: [CSV file]
bank_account_id: 1
statement_date: 2026-01-31
company_id: 1
```

### Creating Reconciliation
```http
POST /api/bank-reconciliations
Content-Type: application/json

{
  "bank_account_id": 1,
  "reconciliation_date": "2026-01-31",
  "period_from": "2026-01-01",
  "period_to": "2026-01-31",
  "book_balance": 10000,
  "bank_balance": 9500,
  "company_id": 1
}
```

### Adding Reconciliation Item
```http
POST /api/bank-reconciliations/1/match-item
Content-Type: application/json

{
  "item_type": "outstanding_check",
  "description": "Check #123",
  "amount": 500,
  "transaction_date": "2026-01-15",
  "reference_number": "CHK-123"
}
```

## Security

- All endpoints require authentication via Sanctum
- Input validation on all requests
- No SQL injection vulnerabilities (using Eloquent ORM)
- Proper access control for approval operations
- Cannot delete approved reconciliations

## Notes

- Statement numbers and reconciliation numbers are auto-generated
- Approved reconciliations cannot be deleted
- Only completed reconciliations can be approved
- Auto-matching uses simple date and amount matching logic
- CSV format: Date, Description, Reference, Debit, Credit
