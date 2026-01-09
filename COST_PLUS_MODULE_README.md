# Cost Plus Management Module

## Overview
This module provides comprehensive Cost Plus contract management with Open Book Accounting principles, supporting transparent cost tracking, documentation requirements, and GMP (Guaranteed Maximum Price) monitoring.

## Features

### ✅ Core Capabilities
- **Open Book Accounting (100%)**: Complete transparency in cost management
- **4-Document Mandatory Verification**: 
  - Original Invoice
  - Payment Receipt
  - Goods Receipt Note (GRN)
  - Photo Evidence with GPS + Timestamp
- **Multiple Fee Types**: Percentage, Fixed Fee, Incentive, Hybrid
- **GMP Tracking**: Real-time monitoring of Guaranteed Maximum Price
- **Overhead Allocation**: Distribute indirect costs across projects
- **Automatic Invoice Generation**: Smart invoicing based on approved transactions
- **RTL Support**: Full right-to-left Arabic interface

## Database Structure

### Tables Created
1. `projects` - Project management
2. `contracts` - Base contract information
3. `goods_receipt_notes` - Material receipt tracking
4. `cost_plus_contracts` - Cost Plus specific configurations
5. `cost_plus_transactions` - Individual cost transactions
6. `cost_plus_invoices` - Generated invoices
7. `cost_plus_invoice_items` - Invoice line items
8. `cost_plus_overhead_allocations` - Overhead distribution

## API Endpoints

### Contracts
- `GET /cost-plus/contracts` - List all contracts
- `POST /cost-plus/contracts` - Create new contract
- `GET /cost-plus/contracts/{id}` - View contract details
- `PUT /cost-plus/contracts/{id}` - Update contract

### Transactions
- `GET /cost-plus/transactions` - List all transactions
- `POST /cost-plus/transactions` - Create new transaction
- `GET /cost-plus/transactions/{id}` - View transaction details
- `POST /cost-plus/transactions/{id}/approve` - Approve transaction
- `POST /cost-plus/transactions/{id}/upload-documents` - Upload documentation

### Invoices
- `GET /cost-plus/invoices` - List all invoices
- `POST /cost-plus/invoices/generate` - Generate new invoice
- `GET /cost-plus/invoices/{id}` - View invoice details
- `POST /cost-plus/invoices/{id}/approve` - Approve invoice
- `GET /cost-plus/invoices/{id}/export` - Export invoice (PDF)

### Overhead
- `GET /cost-plus/overhead` - List overhead allocations
- `POST /cost-plus/overhead/allocate` - Allocate overhead costs

### Reports
- `GET /cost-plus/dashboard` - Main dashboard with statistics
- `GET /cost-plus/gmp-status` - GMP tracking report
- `GET /cost-plus/open-book-report` - Open book accounting report
- `GET /cost-plus/reports` - Comprehensive reports

## Models

### CostPlusContract
Main contract configuration with fee structure and GMP settings.

**Key Methods:**
- `calculateFee($costs)` - Calculate fee based on contract type
- `checkGMPStatus()` - Verify GMP compliance

### CostPlusTransaction
Individual cost transactions with 4-document verification.

**Key Methods:**
- `checkDocumentation()` - Verify all 4 documents are present
- `approve($userId)` - Approve transaction after verification

### CostPlusInvoice
Generated invoices with automatic calculations.

**Key Methods:**
- `calculateTotals()` - Calculate subtotals, VAT, and total amount

## Usage Example

### Creating a Cost Plus Contract

```php
$contract = CostPlusContract::create([
    'contract_id' => 1,
    'project_id' => 1,
    'fee_type' => 'percentage',
    'fee_percentage' => 10.00,
    'has_gmp' => true,
    'guaranteed_maximum_price' => 1000000.00,
    'gmp_savings_share' => 50.00,
    'overhead_reimbursable' => true,
    'overhead_percentage' => 15.00,
    'overhead_method' => 'percentage',
    'reimbursable_costs' => ['material', 'labor', 'equipment'],
    'non_reimbursable_costs' => ['penalties', 'fines'],
    'currency' => 'JOD'
]);
```

### Recording a Transaction

```php
$transaction = CostPlusTransaction::create([
    'transaction_number' => 'TRX-2024-001',
    'cost_plus_contract_id' => 1,
    'project_id' => 1,
    'transaction_date' => '2024-01-15',
    'cost_type' => 'material',
    'description' => 'Steel reinforcement bars',
    'vendor_name' => 'Steel Co.',
    'gross_amount' => 10000.00,
    'discount' => 500.00,
    'net_amount' => 9500.00,
    'recorded_by' => auth()->id()
]);
```

### Generating an Invoice

```php
// Via Controller
POST /cost-plus/invoices/generate
{
    "cost_plus_contract_id": 1,
    "project_id": 1,
    "invoice_number": "INV-2024-001",
    "period_from": "2024-01-01",
    "period_to": "2024-01-31",
    "vat_percentage": 16
}
```

## Installation

1. Run migrations:
```bash
php artisan migrate
```

2. Ensure storage directory is writable for file uploads:
```bash
php artisan storage:link
```

## Configuration

### File Upload Storage
Documents are stored in:
- Invoices: `storage/app/public/cost-plus/invoices/`
- Receipts: `storage/app/public/cost-plus/receipts/`
- Photos: `storage/app/public/cost-plus/photos/`

### Currency Support
Default: JOD (Jordanian Dinar)
Supported: USD, EUR, JOD

### VAT Default
16% (configurable per invoice)

## Security Considerations

- All file uploads are validated for type and size
- GPS coordinates are stored with photo evidence
- Timestamps are immutable once set
- Approval workflow prevents unauthorized modifications
- Documentation completeness is enforced before approval

## Future Enhancements

- PDF invoice generation with custom templates
- Email notifications for approvals
- Advanced analytics dashboard
- Integration with accounting systems
- Multi-currency exchange rates
- Mobile app for photo capture with GPS

## Support

For issues or questions, please contact the development team or refer to the main CEMS documentation.
