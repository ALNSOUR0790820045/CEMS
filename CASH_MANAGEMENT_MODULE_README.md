# Cash Management Module Documentation (إدارة النقدية)

## Overview
A complete cash management system for tracking cash accounts, transactions, transfers, forecasts, and daily cash positions.

## Features Implemented

### 1. Cash Accounts Management
- Multiple account types: cash, bank, petty_cash, safe
- Multi-currency support
- Real-time balance tracking
- Custodian assignment
- Branch-level segregation
- GL account integration

### 2. Cash Transactions
- Transaction types: receipt, payment, transfer_in, transfer_out, adjustment
- Auto-generated transaction numbers (CT-YYYY-XXXX)
- Draft/Posted/Cancelled workflow
- Balance validation for payments
- Counterparty tracking
- GL journal entry linking

### 3. Cash Transfers
- Transfer workflow: pending → approved → completed
- Multi-account transfers
- Transfer fee support
- Exchange rate handling
- Approval process with audit trail
- Automatic balance updates

### 4. Cash Forecasts
- Inflow/Outflow forecasting
- Category-based classification
- Probability weighting
- Variance tracking
- Reference linking

### 5. Daily Cash Position
- Daily reconciliation tracking
- Opening/Closing balance
- Total receipts and payments
- Reconciliation audit trail

### 6. Reports
- Cash flow statement
- Cash position report
- Cash forecast report
- Cash movement report
- Daily cash positions

## Database Schema

### Tables Created
1. `cash_accounts` - Cash account master data
2. `cash_transactions` - All cash movements
3. `cash_transfers` - Inter-account transfers
4. `cash_forecasts` - Cash flow forecasts
5. `daily_cash_positions` - Daily cash position tracking

## API Endpoints

### Cash Accounts
```
GET    /api/cash-accounts                  - List all accounts
POST   /api/cash-accounts                  - Create new account
GET    /api/cash-accounts/{id}             - Get account details
PUT    /api/cash-accounts/{id}             - Update account
DELETE /api/cash-accounts/{id}             - Delete account
GET    /api/cash-accounts/{id}/balance     - Get account balance
GET    /api/cash-accounts/{id}/statement   - Get account statement
GET    /api/cash-accounts/{id}/transactions - Get account transactions
```

### Cash Transactions
```
GET    /api/cash-transactions              - List all transactions
POST   /api/cash-transactions              - Create transaction
GET    /api/cash-transactions/{id}         - Get transaction details
PUT    /api/cash-transactions/{id}         - Update transaction (draft only)
DELETE /api/cash-transactions/{id}         - Delete transaction (draft only)
POST   /api/cash-transactions/{id}/post    - Post transaction
POST   /api/cash-transactions/{id}/cancel  - Cancel transaction
```

### Cash Transfers
```
GET    /api/cash-transfers                 - List all transfers
POST   /api/cash-transfers                 - Create transfer
GET    /api/cash-transfers/{id}            - Get transfer details
PUT    /api/cash-transfers/{id}            - Update transfer (pending only)
DELETE /api/cash-transfers/{id}            - Delete transfer (pending only)
POST   /api/cash-transfers/{id}/approve    - Approve transfer
POST   /api/cash-transfers/{id}/complete   - Complete transfer
POST   /api/cash-transfers/{id}/cancel     - Cancel transfer
```

### Cash Forecasts
```
GET    /api/cash-forecasts                 - List all forecasts
POST   /api/cash-forecasts                 - Create forecast
GET    /api/cash-forecasts/{id}            - Get forecast details
PUT    /api/cash-forecasts/{id}            - Update forecast
DELETE /api/cash-forecasts/{id}            - Delete forecast
GET    /api/cash-forecasts-summary         - Get forecast summary
```

### Reports
```
GET    /api/daily-cash-positions           - List daily positions
POST   /api/daily-cash-positions/reconcile - Reconcile position
GET    /api/reports/cash-flow-statement    - Cash flow statement
GET    /api/reports/cash-position          - Current cash position
GET    /api/reports/cash-forecast          - Cash forecast report
GET    /api/reports/cash-movement          - Cash movement report
```

## Business Rules

### 1. Account Balance Validation
- Payments and transfer-outs cannot exceed available balance
- Balance is updated automatically on transaction posting

### 2. Transfer Workflow
- All transfers require approval before completion
- Transfers can be cancelled before completion
- Completed transfers create corresponding transactions in both accounts

### 3. Transaction Status Flow
- Draft → Posted (with balance update)
- Posted → Cancelled (with balance reversal)
- Draft transactions can be edited or deleted
- Posted/Cancelled transactions cannot be modified

### 4. Auto-Numbering
- Cash Accounts: CA-YYYY-XXXX
- Cash Transactions: CT-YYYY-XXXX
- Cash Transfers: TRF-YYYY-XXXX

## Models

### CashAccount
**Fillable Fields:**
- account_code, account_name, account_name_en, account_type
- currency_id, opening_balance, current_balance
- gl_account_id, custodian_id, branch_id, is_active, company_id

**Relationships:**
- currency, glAccount, custodian, branch, company
- transactions, transfersFrom, transfersTo, dailyPositions

**Scopes:**
- active(), byType(), byBranch(), byCurrency()

### CashTransaction
**Fillable Fields:**
- transaction_number, transaction_date, cash_account_id
- transaction_type, amount, currency_id, exchange_rate
- reference_type, reference_id, counterparty_type, counterparty_id
- counterparty_name, description, status, posted_by_id, posted_at
- gl_journal_entry_id, company_id

**Relationships:**
- cashAccount, currency, postedBy, company, reference (morph), counterparty (morph)

**Scopes:**
- byStatus(), byType(), byAccount(), dateRange(), posted(), draft()

### CashTransfer
**Fillable Fields:**
- transfer_number, transfer_date, from_account_id, to_account_id
- amount, from_currency_id, to_currency_id, exchange_rate, fees
- status, requested_by_id, approved_by_id, approved_at, completed_at
- notes, company_id

**Relationships:**
- fromAccount, toAccount, fromCurrency, toCurrency
- requestedBy, approvedBy, company

**Scopes:**
- byStatus(), pending(), approved(), completed(), dateRange()

### CashForecast
**Fillable Fields:**
- forecast_date, forecast_type, category, expected_amount
- actual_amount, variance, reference_type, reference_id
- probability_percentage, notes, company_id

**Relationships:**
- company, reference (morph)

**Scopes:**
- byType(), byCategory(), dateRange(), inflows(), outflows()

### DailyCashPosition
**Fillable Fields:**
- position_date, cash_account_id, opening_balance
- total_receipts, total_payments, closing_balance
- is_reconciled, reconciled_by_id, reconciled_at, notes, company_id

**Relationships:**
- cashAccount, reconciledBy, company

**Scopes:**
- reconciled(), unreconciled(), byAccount(), dateRange()

## Testing

### Test Files Created
1. `CashAccountTest` - Tests for cash account CRUD operations
2. `CashTransactionTest` - Tests for transaction posting and cancellation
3. `CashTransferTest` - Tests for transfer workflow

### Factory Files Created
1. `CashAccountFactory` - Factory for creating test cash accounts
2. `CurrencyFactory` - Factory for creating test currencies

### Test Coverage
- CRUD operations for all entities
- Business rule validation
- Balance updates
- Transfer workflows
- Auto-numbering
- Status transitions

## Usage Examples

### Creating a Cash Account
```php
POST /api/cash-accounts
{
    "account_name": "Main Cash Account",
    "account_type": "cash",
    "currency_id": 1,
    "opening_balance": 10000,
    "custodian_id": 5,
    "branch_id": 2,
    "is_active": true
}
```

### Recording a Receipt
```php
POST /api/cash-transactions
{
    "transaction_date": "2026-01-07",
    "cash_account_id": 1,
    "transaction_type": "receipt",
    "amount": 5000,
    "currency_id": 1,
    "counterparty_type": "customer",
    "counterparty_id": 10,
    "description": "Payment from customer"
}

// Then post it
POST /api/cash-transactions/1/post
```

### Creating a Transfer
```php
POST /api/cash-transfers
{
    "transfer_date": "2026-01-07",
    "from_account_id": 1,
    "to_account_id": 2,
    "amount": 10000,
    "notes": "Transfer to branch office"
}

// Approve it
POST /api/cash-transfers/1/approve

// Complete it
POST /api/cash-transfers/1/complete
```

### Cash Forecast
```php
POST /api/cash-forecasts
{
    "forecast_date": "2026-01-15",
    "forecast_type": "inflow",
    "category": "receivables",
    "expected_amount": 50000,
    "probability_percentage": 80,
    "notes": "Expected customer payments"
}
```

## Security Considerations

1. All routes require authentication via Sanctum
2. Company-level data segregation
3. User audit trails for approvals and postings
4. Soft deletes for data retention
5. Status-based operation restrictions

## Future Enhancements

1. GL integration for automatic journal entries
2. Budget vs actual comparison
3. Cash flow projections
4. Bank reconciliation
5. Multi-level approval workflows
6. Automated cash position updates
7. Email notifications for approvals
8. Cash shortage/surplus alerts

## Dependencies

- Laravel 12.0
- Sanctum for API authentication
- Soft deletes for data retention
- Required tables: companies, currencies, gl_accounts, users, branches

## Installation

1. Run migrations:
```bash
php artisan migrate
```

2. Seed base data (currencies, accounts):
```bash
php artisan db:seed --class=CurrencySeeder
```

3. Test the API endpoints using the provided routes

## Notes

- All monetary values use decimal(15,2) precision
- Transaction numbers are auto-generated with year prefix
- Balance updates are atomic within database transactions
- Failed transactions are rolled back automatically
- Soft deletes preserve audit history
