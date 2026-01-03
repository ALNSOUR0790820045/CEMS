# Financial Reports Module

## Overview
Comprehensive financial reporting system with trial balance, P&L, balance sheet, and custom reports.

## Features

### 1. Core Financial Reports
- **Trial Balance** - Summary of all account balances showing debits and credits
- **Balance Sheet** - Statement of financial position showing assets, liabilities, and equity
- **Income Statement (P&L)** - Profit and Loss statement showing revenue and expenses
- **Cash Flow Statement** - Statement of cash flows by operating, investing, and financing activities
- **General Ledger Report** - Detailed transaction listing for all or specific accounts
- **Account Statement** - Transaction history and running balance for a specific account

### 2. Aged Reports
- **Accounts Payable Aging** - Outstanding vendor invoices by age (30/60/90/120+ days)
- **Accounts Receivable Aging** - Outstanding customer invoices by age (30/60/90/120+ days)
- **Vendor Outstanding** - Total amounts owed to each vendor
- **Customer Outstanding** - Total amounts owed by each customer

### 3. Project Financial Reports
- **Project P&L** - Profit and loss by project
- **Project Cost Analysis** - Detailed cost breakdown by project
- **Budget vs Actual** - Comparison of budgeted vs actual costs per project
- **Project Cash Flow** - Cash inflows and outflows by project
- **Cost Performance Index (CPI)** - Earned value vs actual cost analysis

### 4. Management Reports
- **Executive Dashboard** - High-level financial summary and KPIs
- **KPI Metrics** - Key performance indicators (ratios, margins, etc.)
- **Revenue Analysis** - Revenue breakdown and trends
- **Expense Analysis** - Expense breakdown and trends
- **Profitability Analysis** - Profitability by project and department

### 5. Tax & Compliance
- **VAT Report** - Output VAT, Input VAT, and net VAT payable
- **Withholding Tax Report** - Summary of withholding tax transactions
- **Audit Trail** - Complete history of all journal entries with user tracking

### 6. Custom Report Builder
- Dynamic filters (date range, company, project, department, account range)
- Multiple period options (daily, weekly, monthly, quarterly, yearly)
- Custom grouping (by account, project, department, date)
- Export formats (JSON, PDF, Excel)

## API Endpoints

All endpoints require authentication using Laravel Sanctum (`Authorization: Bearer {token}`).

### Core Financial Reports
```
GET /api/reports/trial-balance
GET /api/reports/balance-sheet
GET /api/reports/income-statement
GET /api/reports/cash-flow
GET /api/reports/general-ledger
GET /api/reports/account-statement
```

### Aged Reports
```
GET /api/reports/ap-aging
GET /api/reports/ar-aging
GET /api/reports/vendor-outstanding
GET /api/reports/customer-outstanding
```

### Project Reports
```
GET /api/reports/project-profitability
GET /api/reports/project-cost-analysis
GET /api/reports/budget-vs-actual
GET /api/reports/project-cash-flow
GET /api/reports/cost-performance-index
```

### Management Reports
```
GET /api/reports/executive-dashboard
GET /api/reports/kpi-metrics
GET /api/reports/revenue-analysis
GET /api/reports/expense-analysis
GET /api/reports/profitability-analysis
```

### Tax & Compliance
```
GET /api/reports/vat-report
GET /api/reports/withholding-tax-report
GET /api/reports/audit-trail
```

### Custom Report Builder
```
POST /api/reports/custom
```

## Request Parameters

### Common Parameters
- `date_from` (optional) - Start date (YYYY-MM-DD), defaults to start of current month
- `date_to` (optional) - End date (YYYY-MM-DD), defaults to end of current month
- `company_id` (optional) - Filter by company
- `department_id` (optional) - Filter by department
- `project_id` (optional) - Filter by project

### Custom Report Parameters
```json
{
  "report_type": "transaction|account|summary",
  "date_from": "2024-01-01",
  "date_to": "2024-12-31",
  "period": "daily|weekly|monthly|quarterly|yearly",
  "company_id": 1,
  "project_id": 1,
  "department_id": 1,
  "account_range": [1, 2, 3],
  "account_type": "asset|liability|equity|revenue|expense",
  "currency": "SAR",
  "group_by": ["account", "project", "department", "date"],
  "export_format": "json|pdf|excel"
}
```

## Response Format

All endpoints return JSON responses with the following structure:

```json
{
  "status": "success",
  "data": {
    // Report-specific data
  }
}
```

### Example: Trial Balance Response
```json
{
  "status": "success",
  "data": {
    "period": {
      "from": "2024-01-01",
      "to": "2024-01-31"
    },
    "accounts": [
      {
        "account_code": "1000",
        "account_name": "Cash",
        "account_type": "asset",
        "debit": 50000.00,
        "credit": 0.00,
        "balance": 50000.00,
        "debit_formatted": "50,000.00",
        "credit_formatted": "0.00",
        "balance_formatted": "50,000.00"
      }
    ],
    "totals": {
      "debit": "50,000.00",
      "credit": "50,000.00",
      "difference": "0.00"
    }
  }
}
```

## Database Schema

### Accounts Table
- Chart of accounts with hierarchical structure
- Account types: asset, liability, equity, revenue, expense
- Categories: current, non_current, operating, non_operating
- Multi-currency support

### Transactions Table
- Double-entry bookkeeping (debit/credit)
- Links to accounts, journal entries, projects, and departments
- Exchange rate support for multi-currency transactions

### Journal Entries Table
- Container for grouped transactions
- Entry types: manual, system, adjustment, closing
- Status workflow: draft → posted → approved
- Approval tracking with user and timestamp

### Projects Table
- Project tracking with budget and actual costs
- Status: planning, active, on_hold, completed, cancelled
- Billable/non-billable flag

### Departments Table
- Hierarchical department structure
- Links to managers and cost centers

## Usage Examples

### Get Trial Balance for Current Month
```bash
curl -X GET "https://your-domain.com/api/reports/trial-balance" \
  -H "Authorization: Bearer {token}" \
  -H "Accept: application/json"
```

### Get Balance Sheet for Specific Date
```bash
curl -X GET "https://your-domain.com/api/reports/balance-sheet?date=2024-12-31" \
  -H "Authorization: Bearer {token}" \
  -H "Accept: application/json"
```

### Get Project Profitability Report
```bash
curl -X GET "https://your-domain.com/api/reports/project-profitability?date_from=2024-01-01&date_to=2024-12-31&project_id=5" \
  -H "Authorization: Bearer {token}" \
  -H "Accept: application/json"
```

### Generate Custom Report
```bash
curl -X POST "https://your-domain.com/api/reports/custom" \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{
    "report_type": "transaction",
    "date_from": "2024-01-01",
    "date_to": "2024-12-31",
    "project_id": 5,
    "group_by": ["account", "department"],
    "export_format": "json"
  }'
```

## Installation

1. Run migrations to create the database tables:
```bash
php artisan migrate
```

2. The API routes are automatically registered and available at `/api/reports/*`

3. All endpoints are protected by Sanctum authentication middleware

## Implementation Notes

### Multi-Tenancy Support
- Account codes are unique per company (not globally unique)
- All queries respect company boundaries for multi-tenant systems

### Performance Considerations
- Indexes are created on frequently queried columns
- Large reports should use pagination
- Consider caching for frequently accessed reports

### Future Enhancements
- Add project completion percentage tracking for accurate CPI calculations
- Implement actual cash flow statement calculations
- Add support for consolidated reports across multiple companies
- Implement scheduled report generation and email delivery
- Add PDF and Excel export functionality

## Security

- All endpoints require authentication via Laravel Sanctum
- Input validation on all parameters
- SQL injection protection through Eloquent ORM
- XSS protection through Laravel's output escaping

## License

This module is part of the CEMS (Construction Enterprise Management System) application.
