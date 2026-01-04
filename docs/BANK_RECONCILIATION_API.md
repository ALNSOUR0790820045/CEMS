# Bank Reconciliation Module API Documentation

## Overview
This document describes the Bank Reconciliation Module API endpoints for the CEMS application.

## Base URL
All API endpoints are prefixed with `/api`

---

## Bank Accounts

### 1. List Bank Accounts
**Endpoint:** `GET /api/bank-accounts`

**Query Parameters:**
- `company_id` (optional): Filter by company ID
- `is_active` (optional): Filter by active status (true/false)

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "account_number": "1234567890",
      "account_name": "Main Operating Account",
      "bank_name": "Jordan National Bank",
      "branch": "Amman Branch",
      "swift_code": "JNBLJOA1",
      "iban": "JO12JNBL1234567890123456",
      "currency_id": 1,
      "current_balance": "50000.00",
      "book_balance": "50000.00",
      "gl_account_id": 1,
      "is_active": true,
      "company_id": 1,
      "currency": {...},
      "gl_account": {...},
      "company": {...}
    }
  ]
}
```

### 2. Create Bank Account
**Endpoint:** `POST /api/bank-accounts`

**Request Body:**
```json
{
  "account_number": "1234567890",
  "account_name": "Main Operating Account",
  "bank_name": "Jordan National Bank",
  "branch": "Amman Branch",
  "swift_code": "JNBLJOA1",
  "iban": "JO12JNBL1234567890123456",
  "currency_id": 1,
  "current_balance": 50000.00,
  "book_balance": 50000.00,
  "gl_account_id": 1,
  "is_active": true,
  "company_id": 1
}
```

**Response:**
```json
{
  "success": true,
  "message": "Bank account created successfully",
  "data": {...}
}
```

### 3. Get Bank Account
**Endpoint:** `GET /api/bank-accounts/{id}`

**Response:**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "account_number": "1234567890",
    ...
    "bank_statements": [...]
  }
}
```

### 4. Update Bank Account
**Endpoint:** `PUT /api/bank-accounts/{id}`

**Request Body:** Same as Create Bank Account

**Response:**
```json
{
  "success": true,
  "message": "Bank account updated successfully",
  "data": {...}
}
```

### 5. Delete Bank Account
**Endpoint:** `DELETE /api/bank-accounts/{id}`

**Response:**
```json
{
  "success": true,
  "message": "Bank account deleted successfully"
}
```

---

## Bank Statements

### 1. List Bank Statements
**Endpoint:** `GET /api/bank-statements`

**Query Parameters:**
- `bank_account_id` (optional): Filter by bank account
- `status` (optional): Filter by status (imported, reconciling, reconciled)
- `company_id` (optional): Filter by company

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "statement_number": "BS-2026-0001",
      "bank_account_id": 1,
      "statement_date": "2026-01-31",
      "opening_balance": "50000.00",
      "closing_balance": "52500.00",
      "status": "imported",
      "reconciled_by_id": null,
      "reconciled_at": null,
      "company_id": 1,
      "bank_account": {...},
      "company": {...}
    }
  ]
}
```

### 2. Create Bank Statement
**Endpoint:** `POST /api/bank-statements`

**Request Body:**
```json
{
  "bank_account_id": 1,
  "statement_date": "2026-01-31",
  "opening_balance": 50000.00,
  "closing_balance": 52500.00,
  "company_id": 1,
  "lines": [
    {
      "transaction_date": "2026-01-15",
      "value_date": "2026-01-15",
      "description": "Customer payment",
      "reference_number": "TXN123456",
      "debit_amount": 0,
      "credit_amount": 2500.00,
      "balance": 52500.00
    }
  ]
}
```

**Response:**
```json
{
  "success": true,
  "message": "Bank statement created successfully",
  "data": {
    "id": 1,
    "statement_number": "BS-2026-0001",
    ...
    "lines": [...]
  }
}
```

### 3. Get Bank Statement
**Endpoint:** `GET /api/bank-statements/{id}`

**Response:**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "statement_number": "BS-2026-0001",
    ...
    "lines": [...]
  }
}
```

### 4. Import Bank Statement
**Endpoint:** `POST /api/bank-statements/import`

**Request:** Multipart form data
- `file` (required): CSV/Excel file
- `bank_account_id` (required): Bank account ID
- `statement_date` (required): Statement date
- `opening_balance` (required): Opening balance
- `company_id` (required): Company ID

**CSV Format:**
```
Transaction Date,Value Date,Description,Reference,Debit,Credit,Balance
2026-01-15,2026-01-15,Customer payment,TXN123456,0,2500.00,52500.00
```

**Response:**
```json
{
  "success": true,
  "message": "Bank statement imported successfully",
  "data": {...}
}
```

### 5. Reconcile Bank Statement
**Endpoint:** `POST /api/bank-statements/{id}/reconcile`

**Request Body:**
```json
{
  "reconciliations": [
    {
      "line_id": 1,
      "matched_transaction_type": "Invoice",
      "matched_transaction_id": 123
    },
    {
      "line_id": 2,
      "matched_transaction_type": null,
      "matched_transaction_id": null
    }
  ]
}
```

**Response:**
```json
{
  "success": true,
  "message": "Bank statement reconciliation updated successfully",
  "data": {...}
}
```

---

## Bank Reconciliation Reports

### 1. Reconciliation Report
**Endpoint:** `GET /api/bank-reconciliation-report`

**Query Parameters:**
- `bank_account_id` (required): Bank account ID
- `statement_id` (optional): Specific statement ID
- `date_from` (optional): Start date
- `date_to` (optional): End date

**Response:**
```json
{
  "success": true,
  "data": {
    "bank_account": {...},
    "summary": {
      "total_statements": 5,
      "reconciled_statements": 3,
      "reconciliation_rate": 60.00,
      "total_debit": 10000.00,
      "total_credit": 15000.00,
      "unreconciled_debit": 2000.00,
      "unreconciled_credit": 3000.00,
      "unreconciled_count": 5
    },
    "statements": [...],
    "unreconciled_items": [...]
  }
}
```

### 2. Outstanding Items Report
**Endpoint:** `GET /api/bank-reconciliation-report/outstanding-items`

**Query Parameters:**
- `bank_account_id` (required): Bank account ID
- `as_of_date` (optional): Report date (default: today)

**Response:**
```json
{
  "success": true,
  "data": {
    "bank_account": {...},
    "as_of_date": "2026-01-31",
    "outstanding_items": [...],
    "summary": {
      "total_items": 5,
      "total_debit": 2000.00,
      "total_credit": 3000.00,
      "net_amount": 1000.00
    }
  }
}
```

### 3. Bank Book Report
**Endpoint:** `GET /api/bank-reconciliation-report/bank-book`

**Query Parameters:**
- `bank_account_id` (required): Bank account ID
- `date_from` (required): Start date
- `date_to` (required): End date

**Response:**
```json
{
  "success": true,
  "data": {
    "bank_account": {...},
    "period": {
      "from": "2026-01-01",
      "to": "2026-01-31"
    },
    "opening_balance": 50000.00,
    "closing_balance": 52500.00,
    "transactions": [
      {
        "id": 1,
        "transaction_date": "2026-01-15",
        "description": "Customer payment",
        "reference_number": "TXN123456",
        "debit_amount": 0,
        "credit_amount": 2500.00,
        "running_balance": 52500.00,
        "is_reconciled": false
      }
    ],
    "summary": {
      "total_transactions": 10,
      "total_debit": 5000.00,
      "total_credit": 7500.00,
      "net_movement": 2500.00
    }
  }
}
```

---

## Error Responses

All endpoints may return error responses in the following format:

**Validation Error (422):**
```json
{
  "success": false,
  "errors": {
    "field_name": ["Error message"]
  }
}
```

**Server Error (500):**
```json
{
  "success": false,
  "message": "Error description",
  "error": "Detailed error message"
}
```

**Not Found (404):**
```json
{
  "message": "No query results for model..."
}
```

---

## Database Schema

### currencies
- id (PK)
- code (string, unique)
- name (string)
- symbol (string, nullable)
- exchange_rate (decimal 15,6)
- is_active (boolean)
- timestamps

### gl_accounts
- id (PK)
- account_number (string, unique)
- account_name (string)
- account_type (enum: asset, liability, equity, revenue, expense)
- balance (decimal 15,2)
- is_active (boolean)
- company_id (FK -> companies)
- timestamps

### bank_accounts
- id (PK)
- account_number (string, unique)
- account_name (string)
- bank_name (string)
- branch (string, nullable)
- swift_code (string, nullable)
- iban (string, nullable)
- currency_id (FK -> currencies)
- current_balance (decimal 15,2)
- book_balance (decimal 15,2)
- gl_account_id (FK -> gl_accounts)
- is_active (boolean)
- company_id (FK -> companies)
- timestamps

### bank_statements
- id (PK)
- statement_number (string, unique, auto: BS-YYYY-XXXX)
- bank_account_id (FK -> bank_accounts)
- statement_date (date)
- opening_balance (decimal 15,2)
- closing_balance (decimal 15,2)
- status (enum: imported, reconciling, reconciled)
- reconciled_by_id (FK -> users, nullable)
- reconciled_at (timestamp, nullable)
- company_id (FK -> companies)
- timestamps

### bank_statement_lines
- id (PK)
- bank_statement_id (FK -> bank_statements)
- transaction_date (date)
- value_date (date, nullable)
- description (text)
- reference_number (string, nullable)
- debit_amount (decimal 15,2)
- credit_amount (decimal 15,2)
- balance (decimal 15,2, nullable)
- is_reconciled (boolean)
- matched_transaction_type (string, nullable)
- matched_transaction_id (bigint, nullable)
- timestamps

---

## Features Implemented

### 1. Bank Statement Import
- ✅ CSV import support
- ✅ Manual entry via API
- ✅ Auto-parse CSV format

### 2. Matching Rules
- ✅ Manual matching via reconcile endpoint
- ✅ Support for polymorphic matching (matched_transaction_type/id)

### 3. Reconciliation
- ✅ Track unmatched items
- ✅ Manual reconciliation
- ✅ Final reconciliation status tracking
- ✅ Reconciliation audit trail (reconciled_by, reconciled_at)

### 4. Reports
- ✅ Reconciliation report with summary
- ✅ Outstanding items report
- ✅ Bank book report with running balance

### 5. API Endpoints
- ✅ GET/POST /api/bank-accounts
- ✅ POST /api/bank-statements/import
- ✅ GET/POST /api/bank-statements
- ✅ POST /api/bank-statements/{id}/reconcile
- ✅ GET /api/bank-reconciliation-report

---

## Notes

1. **Authentication:** All endpoints should be protected with authentication middleware in production.

2. **Statement Number:** The statement number is auto-generated in the format `BS-YYYY-XXXX` when creating a new bank statement.

3. **Reconciliation Status:** 
   - `imported`: Initial status when statement is created
   - `reconciling`: When some lines are reconciled but not all
   - `reconciled`: When all lines are reconciled

4. **Polymorphic Relations:** The `matched_transaction_type` and `matched_transaction_id` fields support polymorphic relations to match with any transaction type (invoices, payments, etc.)
