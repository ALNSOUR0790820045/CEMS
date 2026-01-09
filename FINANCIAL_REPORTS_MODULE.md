# Financial Reports Module - Documentation

## Overview
The Financial Reports Module provides a comprehensive financial reporting system with dynamic report generation, drill-down capabilities, and export functionality. This module supports 15 different financial reports with export options in PDF, Excel, and CSV formats.

## Features

### ✅ Implemented Features
- **15 Financial Reports**: Trial Balance, Balance Sheet, Income Statement, Cash Flow, General Ledger, Account Transactions, AP/AR Aging, Vendor/Customer Statements, Project Profitability, Cost Center, Budget vs Actual, Payment Analysis, and Tax Report
- **Export Functionality**: PDF (DomPDF), Excel (PhpSpreadsheet), CSV
- **Report Scheduling**: Schedule automatic report generation with email delivery
- **Report History**: Track and download previously generated reports
- **Caching**: Redis-based caching for performance optimization
- **API-First Design**: RESTful API endpoints for all reports
- **Web Dashboard**: User-friendly interface for accessing reports

## Database Schema

### Tables Created

#### `financial_report_configs`
Stores custom report configurations
- `id` - Primary key
- `report_name` - Unique report name
- `report_type` - Type of report (enum)
- `config_json` - Dynamic configuration (JSON)
- `is_active` - Active status
- `company_id` - Foreign key to companies
- `created_by_id` - Foreign key to users
- `timestamps`

#### `report_schedules`
Manages scheduled report generation
- `id` - Primary key
- `report_type` - Type of report
- `frequency` - Schedule frequency (daily, weekly, monthly, quarterly, yearly)
- `schedule_time` - Time to run
- `schedule_day` - Day of month (for monthly/quarterly/yearly)
- `email_recipients` - JSON array of email addresses
- `last_run_at` - Last execution timestamp
- `next_run_at` - Next scheduled execution
- `is_active` - Active status
- `company_id` - Foreign key to companies
- `created_by_id` - Foreign key to users
- `timestamps`

#### `report_history`
Tracks generated reports
- `id` - Primary key
- `report_type` - Type of report
- `report_parameters` - Parameters used (JSON)
- `file_path` - Storage path
- `file_format` - Export format (pdf, excel, csv)
- `generated_by_id` - Foreign key to users
- `generated_at` - Generation timestamp
- `company_id` - Foreign key to companies
- `timestamps`

## API Endpoints

### Report Generation

```http
POST /api/reports/trial-balance
POST /api/reports/balance-sheet
POST /api/reports/income-statement
POST /api/reports/cash-flow
POST /api/reports/general-ledger
POST /api/reports/account-transactions
POST /api/reports/ap-aging
POST /api/reports/ar-aging
POST /api/reports/vendor-statement
POST /api/reports/customer-statement
POST /api/reports/project-profitability
POST /api/reports/cost-center
POST /api/reports/budget-vs-actual
POST /api/reports/payment-analysis
POST /api/reports/tax-report
```

### Export & History

```http
POST /api/reports/export
GET  /api/reports/{reportId}/export?format=pdf
GET  /api/report-history
GET  /api/report-history/{id}/download
GET  /api/reports/drill-down?account={accountId}&from={date}&to={date}
```

### Report Schedules

```http
GET    /api/report-schedules
POST   /api/report-schedules
GET    /api/report-schedules/{id}
PUT    /api/report-schedules/{id}
DELETE /api/report-schedules/{id}
```

## Usage Examples

### 1. Generate Trial Balance Report

```bash
curl -X POST http://your-domain/api/reports/trial-balance \
  -H "Content-Type: application/json" \
  -d '{
    "from_date": "2026-01-01",
    "to_date": "2026-01-31",
    "account_type": "asset",
    "cost_center": "101"
  }'
```

### 2. Export Report to PDF

```bash
curl -X POST http://your-domain/api/reports/export \
  -H "Content-Type: application/json" \
  -d '{
    "report_type": "trial_balance",
    "format": "pdf",
    "parameters": {
      "from_date": "2026-01-01",
      "to_date": "2026-01-31"
    }
  }'
```

### 3. Schedule Monthly Report

```bash
curl -X POST http://your-domain/api/report-schedules \
  -H "Content-Type: application/json" \
  -d '{
    "report_type": "income_statement",
    "frequency": "monthly",
    "schedule_day": 1,
    "schedule_time": "08:00",
    "email_recipients": ["finance@company.com"],
    "is_active": true
  }'
```

## Report Types & Parameters

### 1. Trial Balance
**Parameters:**
- `from_date` (required): Start date
- `to_date` (required): End date
- `account_type` (optional): Filter by account type
- `cost_center` (optional): Filter by cost center

### 2. Balance Sheet
**Parameters:**
- `as_of_date` (required): Report date
- `comparative` (optional): Include comparative period

### 3. Income Statement (P&L)
**Parameters:**
- `from_date` (required): Start date
- `to_date` (required): End date
- `breakdown` (optional): total, monthly, quarterly, yearly

### 4. Cash Flow Statement
**Parameters:**
- `from_date` (required): Start date
- `to_date` (required): End date
- `method` (optional): direct, indirect

### 5. General Ledger
**Parameters:**
- `account_id` (required): Account to report
- `from_date` (required): Start date
- `to_date` (required): End date

### 6. Account Transactions
**Parameters:**
- `account_id` (required): Account to report
- `from_date` (required): Start date
- `to_date` (required): End date
- `transaction_type` (optional): Filter by transaction type

### 7. AP Aging
**Parameters:**
- `as_of_date` (required): Report date

### 8. AR Aging
**Parameters:**
- `as_of_date` (required): Report date

### 9. Vendor Statement
**Parameters:**
- `vendor_id` (required): Vendor to report
- `from_date` (required): Start date
- `to_date` (required): End date

### 10. Customer Statement
**Parameters:**
- `customer_id` (required): Customer to report
- `from_date` (required): Start date
- `to_date` (required): End date

### 11. Project Profitability
**Parameters:**
- `project_id` (required): Project to report
- `from_date` (optional): Start date
- `to_date` (optional): End date

### 12. Cost Center Report
**Parameters:**
- `cost_center_id` (required): Cost center to report
- `from_date` (required): Start date
- `to_date` (required): End date

### 13. Budget vs Actual
**Parameters:**
- `from_date` (required): Start date
- `to_date` (required): End date
- `breakdown` (optional): total, monthly, quarterly, yearly
- `account_id` (optional): Specific account
- `cost_center_id` (optional): Specific cost center

### 14. Payment Analysis
**Parameters:**
- `from_date` (required): Start date
- `to_date` (required): End date
- `payment_method` (optional): Filter by payment method
- `party_type` (optional): vendor, customer

### 15. Tax Report (VAT)
**Parameters:**
- `from_date` (required): Start date
- `to_date` (required): End date
- `tax_type` (optional): Tax type

## Architecture

### Service Layer
All reports are implemented as service classes extending `BaseReportService`:

```php
use App\Services\Reports\TrialBalanceReportService;

$company = Company::find(1);
$parameters = [
    'from_date' => '2026-01-01',
    'to_date' => '2026-01-31'
];

$service = new TrialBalanceReportService($company, $parameters);
$report = $service->getReport(); // Returns cached result if available
```

### Export Services
Three export services are available:

1. **PdfExportService** - Uses DomPDF for PDF generation
2. **ExcelExportService** - Uses PhpSpreadsheet for Excel generation
3. **CsvExportService** - Native CSV generation

```php
use App\Services\Exports\PdfExportService;

$exporter = new PdfExportService();
$filePath = $exporter->export($reportData, 'trial_balance');
```

## Web Interface

Access the reports dashboard at: `/reports`

The dashboard provides:
- Quick access to all 15 financial reports
- Recent reports history
- Download links for previously generated reports
- Visual cards for each report type

## Permissions

The following permissions are available:

```php
- reports.view_financial      // View financial reports
- reports.view_ap_ar          // View AP/AR reports
- reports.view_project        // View project reports
- reports.export              // Export reports
- reports.schedule            // Schedule reports
- reports.view_all_companies  // Super admin access
```

## Performance Optimization

### Caching
Reports are cached for 30 minutes by default using Redis:

```php
Cache::remember($cacheKey, now()->addMinutes(30), function () {
    return $this->generate();
});
```

### Database Indexes
Recommended indexes for optimal performance:
- `date` columns in transaction tables
- `account_id` in GL entries
- `company_id` in all relevant tables
- `vendor_id` and `customer_id` in AP/AR tables

## Background Jobs

For large reports, use Laravel Queue:

```php
dispatch(new GenerateReportJob($reportType, $parameters, $company));
```

## Integration with Other Modules

This module integrates with:
- **GL Module**: General Ledger accounts and entries
- **AP Module**: Accounts Payable data
- **AR Module**: Accounts Receivable data
- **Projects Module**: Project revenue and costs
- **Cost Centers Module**: Cost center allocations

## Future Enhancements

Planned features:
- [ ] Custom report builder (drag-and-drop)
- [ ] Advanced drill-down with breadcrumb navigation
- [ ] Graphical visualizations (Chart.js integration)
- [ ] Comparative analysis (year-over-year)
- [ ] Report templates management
- [ ] Multi-currency support
- [ ] Real-time report preview

## Dependencies

```json
{
  "barryvdh/laravel-dompdf": "^3.1",
  "phpoffice/phpspreadsheet": "^5.3",
  "laravel/framework": "^12.0"
}
```

## Troubleshooting

### Reports not generating
- Check database connection
- Verify company exists
- Ensure required parameters are provided

### Export failing
- Check storage permissions
- Verify DomPDF/PhpSpreadsheet installation
- Check memory limits for large reports

### Caching issues
- Clear cache: `php artisan cache:clear`
- Check Redis connection
- Verify cache driver configuration

## Support

For issues or questions:
- Check the documentation
- Review API endpoint examples
- Contact the development team

---

**Version:** 1.0.0  
**Last Updated:** January 3, 2026
