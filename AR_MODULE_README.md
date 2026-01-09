# Accounts Receivable (AR) Module

## Overview
Comprehensive Accounts Receivable module for managing customer invoices, collections, and aging reports.

## Features

### 1. Invoice Management
- Create, read, update, and delete invoices
- Auto-generated invoice numbers (ARI-YYYY-XXXX format)
- Link invoices to projects, contracts, and IPCs
- Support for multiple currencies with exchange rates
- Automatic calculation of totals, taxes, and discounts
- Track invoice status (draft, sent, overdue, partially_paid, paid, cancelled)
- Send invoices to clients
- Attachment support

### 2. Receipt Processing
- Record customer payments
- Auto-generated receipt numbers (ARR-YYYY-XXXX format)
- Multiple payment methods (cash, check, bank_transfer, credit_card)
- Allocate receipts to multiple invoices
- Track unallocated amounts
- Bank reconciliation support

### 3. Collections Management
- Track overdue invoices
- Monitor payment status
- Aging reports for collection follow-up

### 4. Reporting
- **Aging Report**: View receivables by aging buckets (current, 1-30, 31-60, 60+ days)
- **Client Balance**: View outstanding balances by client
- **Collection Forecast**: Forecast expected collections by month
- **DSO (Days Sales Outstanding)**: Calculate average collection period

## API Endpoints

All endpoints require Sanctum authentication.

### Invoice Endpoints

#### List Invoices
```
GET /api/ar-invoices

Query Parameters:
- status: Filter by status (draft, sent, overdue, partially_paid, paid, cancelled)
- client_id: Filter by client
- from_date: Filter by invoice date (start)
- to_date: Filter by invoice date (end)
```

#### Create Invoice
```
POST /api/ar-invoices

Request Body:
{
  "invoice_date": "2026-01-03",
  "due_date": "2026-02-03",
  "client_id": 1,
  "project_id": 1,  // optional
  "contract_id": 1, // optional
  "ipc_id": 1,      // optional
  "currency_id": 1,
  "exchange_rate": 1.0,
  "subtotal": 1000,
  "tax_amount": 150,
  "discount_amount": 0,
  "payment_terms": "net_30",
  "gl_account_id": 1,  // optional
  "notes": "Invoice notes",
  "items": [
    {
      "description": "Service/Product description",
      "quantity": 1,
      "unit_price": 500,
      "gl_account_id": 1  // optional
    }
  ]
}
```

#### Get Invoice
```
GET /api/ar-invoices/{id}
```

#### Update Invoice
```
PUT /api/ar-invoices/{id}

Request Body: Same as create, all fields optional
```

#### Delete Invoice
```
DELETE /api/ar-invoices/{id}

Note: Cannot delete invoices with payments
```

#### Send Invoice
```
POST /api/ar-invoices/{id}/send

Marks invoice as 'sent' and records sent timestamp
```

### Receipt Endpoints

#### List Receipts
```
GET /api/ar-receipts

Query Parameters:
- status: Filter by status (pending, cleared, bounced, cancelled)
- client_id: Filter by client
- from_date: Filter by receipt date (start)
- to_date: Filter by receipt date (end)
```

#### Create Receipt
```
POST /api/ar-receipts

Request Body:
{
  "receipt_date": "2026-01-03",
  "client_id": 1,
  "payment_method": "bank_transfer",
  "amount": 1000,
  "currency_id": 1,
  "exchange_rate": 1.0,
  "bank_account_id": 1,  // optional
  "check_number": "CHK-123",  // optional
  "reference_number": "REF-123",
  "notes": "Payment notes"
}
```

#### Get Receipt
```
GET /api/ar-receipts/{id}
```

#### Update Receipt
```
PUT /api/ar-receipts/{id}

Request Body: Same as create, all fields optional
```

#### Delete Receipt
```
DELETE /api/ar-receipts/{id}

Note: Cannot delete receipts with allocations
```

#### Allocate Receipt to Invoices
```
POST /api/ar-receipts/{id}/allocate

Request Body:
{
  "allocations": [
    {
      "a_r_invoice_id": 1,
      "allocated_amount": 500
    },
    {
      "a_r_invoice_id": 2,
      "allocated_amount": 500
    }
  ]
}

Note: Total allocations cannot exceed receipt amount
```

### Report Endpoints

#### Aging Report
```
GET /api/ar-reports/aging

Query Parameters:
- as_of_date: Date for aging calculation (default: today)

Response:
{
  "as_of_date": "2026-01-03",
  "data": [
    {
      "client_id": 1,
      "client_name": "ABC Company",
      "current": 1000.00,
      "1_30": 500.00,
      "31_60": 200.00,
      "over_60": 100.00,
      "total": 1800.00
    }
  ]
}
```

#### Client Balance Report
```
GET /api/ar-reports/client-balance

Query Parameters:
- client_id: Filter by specific client (optional)

Response:
{
  "data": [
    {
      "client_id": 1,
      "client_name": "ABC Company",
      "total_invoiced": 10000.00,
      "total_received": 8000.00,
      "total_balance": 2000.00
    }
  ]
}
```

#### Collection Forecast
```
GET /api/ar-reports/collection-forecast

Query Parameters:
- months: Number of months to forecast (default: 3)

Response:
{
  "data": [
    {
      "month": "2026-01",
      "expected_collection": 5000.00
    },
    {
      "month": "2026-02",
      "expected_collection": 3000.00
    }
  ]
}
```

#### Days Sales Outstanding (DSO)
```
GET /api/ar-reports/dso

Query Parameters:
- days: Period in days for calculation (default: 90)

Response:
{
  "period_days": 90,
  "total_sales": 50000.00,
  "total_receivables": 15000.00,
  "days_sales_outstanding": 27.00
}
```

## Database Schema

### Main Tables
- `clients` - Customer master data
- `projects` - Project information
- `contracts` - Contract details
- `i_p_c_s` - Interim Payment Certificates
- `currencies` - Currency master data
- `g_l_accounts` - General Ledger accounts
- `bank_accounts` - Bank account information
- `a_r_invoices` - Accounts receivable invoices
- `a_r_invoice_items` - Invoice line items
- `a_r_receipts` - Customer payments
- `a_r_receipt_allocations` - Receipt to invoice allocations

## Payment Terms
- `cod` - Cash on Delivery
- `net_7` - Net 7 days
- `net_15` - Net 15 days
- `net_30` - Net 30 days
- `net_45` - Net 45 days
- `net_60` - Net 60 days

## Invoice Status Flow
1. `draft` - Initial state
2. `sent` - Invoice sent to client
3. `overdue` - Past due date with balance
4. `partially_paid` - Some payment received
5. `paid` - Fully paid
6. `cancelled` - Cancelled invoice

## Receipt Status
- `pending` - Payment pending
- `cleared` - Payment cleared
- `bounced` - Payment bounced
- `cancelled` - Payment cancelled

## Testing

Run tests:
```bash
php artisan test --filter=ARInvoiceTest
php artisan test --filter=ARReceiptTest
```

## Notes

1. All monetary values are stored with 2 decimal precision
2. Exchange rates support 4 decimal precision
3. Invoice and receipt numbers are auto-generated on creation
4. Soft deletes are enabled for invoices, clients, projects, contracts, and IPCs
5. All API endpoints require Sanctum authentication
6. Company context is automatically set from authenticated user
7. Receipt allocations automatically update invoice received amounts and status
