# Cash Management Module

## Overview
The Cash Management Module provides comprehensive cash flow management capabilities including receipts, payments, transfers, and cash forecasting.

## Features

### 1. Cash Accounts Management
- Support for multiple account types: Cash, Bank, Petty Cash
- Multi-currency support
- Integration with General Ledger (GL) accounts
- Real-time balance tracking

### 2. Cash Transactions
- **Receipts**: Record incoming payments from customers and other sources
- **Payments**: Track outgoing payments to vendors and expenses
- **Transfers**: Move funds between cash accounts
- Auto-generated transaction numbers (CT-YYYY-XXXX format)
- Transaction status tracking (draft, posted, cancelled)

### 3. Cash Flow Forecasting
- Period-based cash flow analysis
- Daily breakdown of receipts and payments
- Payment method categorization
- Opening and closing balance tracking

## Database Schema

### Tables
- **currencies**: Currency definitions and exchange rates
- **gl_accounts**: General Ledger accounts
- **gl_journal_entries**: GL journal entries for financial transactions
- **cash_accounts**: Cash and bank account definitions
- **cash_transactions**: All cash transaction records

## API Endpoints

### Cash Accounts
```
GET    /api/cash-accounts           - List all cash accounts
POST   /api/cash-accounts           - Create new cash account
GET    /api/cash-accounts/{id}      - Get specific cash account
PUT    /api/cash-accounts/{id}      - Update cash account
DELETE /api/cash-accounts/{id}      - Delete cash account
```

### Cash Transactions
```
GET    /api/cash-transactions       - List all transactions
POST   /api/cash-transactions       - Create new transaction (receipt or payment only)
GET    /api/cash-transactions/{id}  - Get specific transaction
PUT    /api/cash-transactions/{id}  - Update transaction (draft transactions only)
DELETE /api/cash-transactions/{id}  - Delete transaction (draft transactions only)
```

**Note:** The generic POST endpoint only supports 'receipt' and 'payment' transaction types. For transfers between accounts, use the specialized transfer endpoint below.

### Specialized Transaction Endpoints
```
POST   /api/cash-transactions/receipt   - Create receipt transaction (auto-posted)
POST   /api/cash-transactions/payment   - Create payment transaction (auto-posted)
POST   /api/cash-transactions/transfer  - Create transfer transaction (requires from/to accounts)
```

### Cash Flow
```
GET    /api/cash-flow-forecast     - Get cash flow forecast
GET    /api/cash-flow-summary      - Get cash accounts summary
```

## Usage Examples

### Create Cash Account
```json
POST /api/cash-accounts
{
  "account_code": "CA-001",
  "account_name": "Main Cash Account",
  "account_type": "cash",
  "currency_id": 1,
  "current_balance": 10000.00,
  "gl_account_id": 1,
  "is_active": true
}
```

### Create Receipt Transaction
```json
POST /api/cash-transactions/receipt
{
  "transaction_date": "2026-01-04",
  "cash_account_id": 1,
  "amount": 5000.00,
  "payment_method": "cash",
  "reference_number": "REF-001",
  "payee_payer": "Customer A",
  "description": "Payment received from Customer A"
}
```

### Create Payment Transaction
```json
POST /api/cash-transactions/payment
{
  "transaction_date": "2026-01-04",
  "cash_account_id": 1,
  "amount": 2000.00,
  "payment_method": "bank_transfer",
  "reference_number": "PAY-001",
  "payee_payer": "Vendor B",
  "description": "Payment to Vendor B for supplies"
}
```

### Create Transfer Transaction
```json
POST /api/cash-transactions/transfer
{
  "transaction_date": "2026-01-04",
  "from_account_id": 1,
  "to_account_id": 2,
  "amount": 1000.00,
  "description": "Transfer from cash to bank",
  "reference_number": "TRF-001"
}
```

### Get Cash Flow Forecast
```json
GET /api/cash-flow-forecast?from_date=2026-01-01&to_date=2026-01-31&cash_account_id=1
```

## Business Rules

1. **Balance Validation**: Payments and transfers check for sufficient balance before processing
2. **Posted Transactions**: Cannot be modified or deleted (must be cancelled first)
3. **Transaction Numbering**: Auto-generated in format CT-YYYY-XXXX (sequential per year)
4. **Account Protection**: Cash accounts with transactions cannot be deleted
5. **Multi-Company**: All data is scoped to the authenticated user's company

## Models

### Currency
- Stores currency definitions and exchange rates
- Fields: code, name, symbol, exchange_rate, is_active

### GLAccount
- General Ledger account structure
- Supports hierarchical account structure (parent-child)
- Fields: account_code, account_name, account_type, parent_id, balance

### CashAccount
- Cash and bank account definitions
- Fields: account_code, account_name, account_type, currency_id, current_balance

### CashTransaction
- All cash transaction records
- Fields: transaction_number, transaction_date, transaction_type, amount, payment_method
- Auto-generates transaction numbers on creation

## Security

- All API endpoints require authentication (Sanctum)
- Company-level data isolation
- User permissions integration ready
- Balance validation prevents overdrafts

## Testing

### Seed Test Data
```bash
php artisan db:seed --class=CurrencySeeder
php artisan db:seed --class=TestDataSeeder
```

Test credentials:
- Email: test@example.com
- Password: password

## Future Enhancements

- Invoice/Bill linking for auto-reconciliation
- Bank reconciliation module
- Recurring transactions
- Multi-currency exchange handling
- Advanced reporting and analytics
- Cash flow predictions using historical data
