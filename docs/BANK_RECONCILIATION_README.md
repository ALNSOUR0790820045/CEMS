# Bank Reconciliation Module

## Overview
The Bank Reconciliation Module is a comprehensive solution for managing bank accounts, importing bank statements, and reconciling transactions in the CEMS (Company Enterprise Management System).

## Features

### 1. Bank Account Management
- Create and manage multiple bank accounts
- Track current and book balances
- Support for multiple currencies
- Link to GL accounts for integration
- Multi-company support

### 2. Bank Statement Import
- Manual entry via API
- CSV file import
- Auto-generate statement numbers (BS-YYYY-XXXX)
- Support for transaction details including:
  - Transaction date and value date
  - Description and reference numbers
  - Debit and credit amounts
  - Running balance

### 3. Reconciliation Features
- Manual reconciliation of statement lines
- Polymorphic transaction matching (link to any transaction type)
- Track reconciliation status (imported, reconciling, reconciled)
- Audit trail (who reconciled and when)
- Identify unreconciled items

### 4. Reports
- **Reconciliation Report**: Summary of reconciled vs unreconciled items
- **Outstanding Items Report**: List of unreconciled transactions
- **Bank Book Report**: Transaction listing with running balance

## Database Structure

The module includes the following tables:

1. **currencies** - Currency definitions
2. **gl_accounts** - General ledger accounts
3. **bank_accounts** - Bank account details
4. **bank_statements** - Bank statement headers
5. **bank_statement_lines** - Individual transactions

See [API Documentation](./BANK_RECONCILIATION_API.md) for detailed schema.

## Installation

### 1. Run Migrations
```bash
php artisan migrate
```

This will create the following tables:
- currencies
- gl_accounts
- bank_accounts
- bank_statements
- bank_statement_lines

### 2. Seed Initial Data (Optional)
Create seeders for:
- Default currencies (JOD, USD, EUR, etc.)
- GL accounts for banking

### 3. Configure Routes
The API routes are automatically configured in `routes/api.php` and enabled in `bootstrap/app.php`.

## Usage

### Creating a Bank Account

```bash
POST /api/bank-accounts
Content-Type: application/json

{
  "account_number": "1234567890",
  "account_name": "Main Operating Account",
  "bank_name": "Jordan National Bank",
  "branch": "Amman Branch",
  "currency_id": 1,
  "gl_account_id": 1,
  "company_id": 1
}
```

### Importing a Bank Statement (CSV)

```bash
POST /api/bank-statements/import
Content-Type: multipart/form-data

file: [CSV file]
bank_account_id: 1
statement_date: 2026-01-31
opening_balance: 50000.00
company_id: 1
```

**CSV Format:**
```csv
Transaction Date,Value Date,Description,Reference,Debit,Credit,Balance
2026-01-15,2026-01-15,Customer payment,TXN123456,0,2500.00,52500.00
2026-01-20,2026-01-20,Supplier payment,TXN789012,1000.00,0,51500.00
```

### Reconciling Statement Lines

```bash
POST /api/bank-statements/1/reconcile
Content-Type: application/json

{
  "reconciliations": [
    {
      "line_id": 1,
      "matched_transaction_type": "App\\Models\\Invoice",
      "matched_transaction_id": 123
    },
    {
      "line_id": 2,
      "matched_transaction_type": "App\\Models\\Payment",
      "matched_transaction_id": 456
    }
  ]
}
```

### Generating Reports

**Reconciliation Report:**
```bash
GET /api/bank-reconciliation-report?bank_account_id=1&date_from=2026-01-01&date_to=2026-01-31
```

**Outstanding Items:**
```bash
GET /api/bank-reconciliation-report/outstanding-items?bank_account_id=1&as_of_date=2026-01-31
```

**Bank Book:**
```bash
GET /api/bank-reconciliation-report/bank-book?bank_account_id=1&date_from=2026-01-01&date_to=2026-01-31
```

## Models

### Currency
Manages currency definitions with exchange rates.

### GlAccount
General ledger accounts for financial integration.

### BankAccount
Bank account details with current and book balances.

### BankStatement
Bank statement header with auto-generated statement numbers.

**Auto-numbering:** Statement numbers are automatically generated in the format `BS-YYYY-XXXX` where:
- `BS` = Bank Statement prefix
- `YYYY` = Current year
- `XXXX` = Sequential number (padded to 4 digits)

### BankStatementLine
Individual transaction lines within a bank statement.

## API Endpoints

All endpoints are prefixed with `/api`:

### Bank Accounts
- `GET /bank-accounts` - List all bank accounts
- `POST /bank-accounts` - Create new bank account
- `GET /bank-accounts/{id}` - Get bank account details
- `PUT /bank-accounts/{id}` - Update bank account
- `DELETE /bank-accounts/{id}` - Delete bank account

### Bank Statements
- `GET /bank-statements` - List all bank statements
- `POST /bank-statements` - Create new bank statement
- `GET /bank-statements/{id}` - Get bank statement details
- `POST /bank-statements/import` - Import from CSV/Excel
- `POST /bank-statements/{id}/reconcile` - Reconcile statement

### Reports
- `GET /bank-reconciliation-report` - Reconciliation report
- `GET /bank-reconciliation-report/outstanding-items` - Outstanding items
- `GET /bank-reconciliation-report/bank-book` - Bank book

See [API Documentation](./BANK_RECONCILIATION_API.md) for detailed endpoint specifications.

## Controllers

### BankAccountController
Handles CRUD operations for bank accounts.

### BankStatementController
Manages bank statement creation, import, and reconciliation.

**Key Methods:**
- `import()` - Import bank statements from CSV files
- `reconcile()` - Update reconciliation status for statement lines

### BankReconciliationReportController
Generates various reconciliation reports.

**Reports:**
- `index()` - Main reconciliation report with summary
- `outstandingItems()` - List of unreconciled transactions
- `bankBook()` - Transaction listing with running balance

## Validation

All API endpoints include comprehensive validation:

- Required fields validation
- Data type validation
- Foreign key existence validation
- Unique constraint validation
- File format validation (for imports)

## Error Handling

All controllers use consistent error response format:

**Success Response:**
```json
{
  "success": true,
  "message": "Operation successful",
  "data": {...}
}
```

**Error Response:**
```json
{
  "success": false,
  "errors": {
    "field": ["Error message"]
  }
}
```

## Security Considerations

1. **Authentication Required:** All endpoints should be protected with authentication middleware in production
2. **Authorization:** Implement role-based access control (RBAC) for sensitive operations
3. **Company Isolation:** Ensure users can only access data from their own company
4. **Input Validation:** All inputs are validated before processing
5. **SQL Injection Protection:** Using Eloquent ORM for query building
6. **File Upload Security:** Validate file types and sizes for imports

## Future Enhancements

### Potential Additions:
1. **Auto-matching Service:** 
   - Match by reference number
   - Fuzzy matching algorithm
   - Amount-based matching

2. **Excel Import Support:**
   - Add Maatwebsite/Excel package
   - Support XLSX and XLS formats

3. **Batch Operations:**
   - Bulk reconciliation
   - Bulk import of multiple statements

4. **Advanced Reports:**
   - Reconciliation trends
   - Exception reports
   - Aging analysis

5. **Notifications:**
   - Email alerts for unreconciled items
   - Reconciliation completion notifications

6. **Web UI:**
   - Frontend interface for reconciliation
   - Drag-and-drop matching
   - Visual reconciliation dashboard

## Testing

To test the module:

1. Create test data for currencies and GL accounts
2. Create a bank account via API
3. Import or create a bank statement
4. Reconcile statement lines
5. Generate reports

Example test scenario provided in API documentation.

## Support

For issues or questions about the Bank Reconciliation Module:
- Review the [API Documentation](./BANK_RECONCILIATION_API.md)
- Check the database migrations for schema details
- Examine the model relationships in the code

## License

This module is part of the CEMS application and follows the same license terms.
