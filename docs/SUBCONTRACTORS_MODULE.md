# Subcontractors Module Documentation

## Overview
The Subcontractors Module is a comprehensive system for managing subcontractor registration, agreements, work orders, Interim Payment Certificates (IPCs), and performance evaluations within the CEMS ERP system.

## Features

### 1. Subcontractor Registration
- Complete subcontractor profile management
- Multiple contact persons per subcontractor
- Trade category classification (civil, electrical, mechanical, plumbing, finishing, landscaping, other)
- Subcontractor types (specialized, general, labor_only, materials_labor)
- License and insurance tracking with expiry alerts
- Credit limit and payment terms management
- Approval and blacklist functionality

### 2. Agreement Management
- Multiple agreement types: lump sum, unit rate, time & material, cost plus
- Link agreements to projects and contracts
- Retention percentage and advance payment management
- Performance bond tracking
- Multi-level approval workflow
- Document attachment support

### 3. Work Orders
- Issue work orders under agreements
- Track work order status (pending, approved, in_progress, completed, cancelled)
- Multiple work orders per agreement
- Location and timeline management

### 4. IPC (Interim Payment Certificate) Management
- Create interim and final IPCs
- Automatic calculation of:
  - Cumulative amounts
  - Retention deductions
  - Advance payment deductions
  - Back charges
  - Net payable amount
- Multi-level approval process (submitted → under_review → approved → paid)
- Line item details with quantities and unit rates
- Link to AP payments module
- PDF generation support

### 5. Performance Evaluation
- Multi-criteria scoring system (1-5 scale):
  - Quality score
  - Time performance score
  - Safety score
  - Cooperation score
- Overall score calculation
- Strengths, weaknesses, and recommendations tracking
- Project-specific and general evaluations

## Database Schema

### Main Tables
1. **subcontractors** - Core subcontractor information
2. **subcontractor_contacts** - Multiple contacts per subcontractor
3. **subcontractor_agreements** - Agreements with projects
4. **subcontractor_work_orders** - Work orders under agreements
5. **subcontractor_ipcs** - Payment certificates
6. **subcontractor_ipc_items** - Line items for IPCs
7. **subcontractor_evaluations** - Performance evaluations

### Supporting Tables
- countries, cities, currencies
- projects, contracts
- units (measurement units)
- gl_accounts (general ledger)
- ap_payments (accounts payable)

## API Endpoints

### Subcontractors
```
GET    /api/subcontractors              - List all subcontractors
POST   /api/subcontractors              - Create new subcontractor
GET    /api/subcontractors/{id}         - Get subcontractor details
PUT    /api/subcontractors/{id}         - Update subcontractor
DELETE /api/subcontractors/{id}         - Delete subcontractor
POST   /api/subcontractors/{id}/approve - Approve subcontractor
POST   /api/subcontractors/{id}/blacklist - Blacklist subcontractor
```

### Agreements
```
GET    /api/subcontractor-agreements              - List all agreements
POST   /api/subcontractor-agreements              - Create new agreement
GET    /api/subcontractor-agreements/{id}         - Get agreement details
PUT    /api/subcontractor-agreements/{id}         - Update agreement
DELETE /api/subcontractor-agreements/{id}         - Delete agreement
```

### Work Orders
```
GET    /api/subcontractor-work-orders              - List all work orders
POST   /api/subcontractor-work-orders              - Create new work order
GET    /api/subcontractor-work-orders/{id}         - Get work order details
PUT    /api/subcontractor-work-orders/{id}         - Update work order
DELETE /api/subcontractor-work-orders/{id}         - Delete work order
```

### IPCs
```
GET    /api/subcontractor-ipcs              - List all IPCs
POST   /api/subcontractor-ipcs              - Create new IPC
GET    /api/subcontractor-ipcs/{id}         - Get IPC details
PUT    /api/subcontractor-ipcs/{id}         - Update IPC
DELETE /api/subcontractor-ipcs/{id}         - Delete IPC
POST   /api/subcontractor-ipcs/{id}/approve - Approve IPC
GET    /api/subcontractor-ipcs/{id}/pdf     - Generate PDF for IPC
```

### Evaluations
```
GET    /api/subcontractor-evaluations              - List all evaluations
POST   /api/subcontractor-evaluations              - Create new evaluation
GET    /api/subcontractor-evaluations/{id}         - Get evaluation details
PUT    /api/subcontractor-evaluations/{id}         - Update evaluation
DELETE /api/subcontractor-evaluations/{id}         - Delete evaluation
```

## Permissions

The module includes the following permissions:

### Subcontractor Management
- `subcontractors.view` - View subcontractors
- `subcontractors.create` - Create subcontractors
- `subcontractors.edit` - Edit subcontractors
- `subcontractors.delete` - Delete subcontractors
- `subcontractors.approve` - Approve subcontractors
- `subcontractors.blacklist` - Blacklist subcontractors

### Agreement Management
- `subcontractors.manage_agreements` - Full agreement management
- `subcontractors.view_agreements` - View agreements
- `subcontractors.create_agreements` - Create agreements
- `subcontractors.edit_agreements` - Edit agreements
- `subcontractors.delete_agreements` - Delete agreements

### Work Order Management
- `subcontractors.manage_work_orders` - Full work order management
- `subcontractors.view_work_orders` - View work orders
- `subcontractors.create_work_orders` - Create work orders
- `subcontractors.edit_work_orders` - Edit work orders
- `subcontractors.delete_work_orders` - Delete work orders

### IPC Management
- `subcontractors.manage_ipcs` - Full IPC management
- `subcontractors.view_ipcs` - View IPCs
- `subcontractors.create_ipcs` - Create IPCs
- `subcontractors.edit_ipcs` - Edit IPCs
- `subcontractors.delete_ipcs` - Delete IPCs
- `subcontractors.approve_ipcs` - Approve IPCs
- `subcontractors.review_ipcs` - Review IPCs

### Evaluation Management
- `subcontractors.evaluate` - Full evaluation management
- `subcontractors.view_evaluations` - View evaluations
- `subcontractors.create_evaluations` - Create evaluations
- `subcontractors.edit_evaluations` - Edit evaluations
- `subcontractors.delete_evaluations` - Delete evaluations

## Usage Examples

### Creating a Subcontractor
```php
POST /api/subcontractors
{
    "name": "ABC Construction Ltd",
    "name_en": "ABC Construction Ltd",
    "subcontractor_type": "specialized",
    "trade_category": "electrical",
    "email": "info@abc-construction.com",
    "phone": "+962791234567",
    "payment_terms": "30_days",
    "retention_percentage": 5.00,
    "credit_limit": 500000.00
}
```

### Creating an IPC with Items
```php
POST /api/subcontractor-ipcs
{
    "ipc_date": "2026-01-03",
    "period_from": "2025-12-01",
    "period_to": "2025-12-31",
    "subcontractor_agreement_id": 1,
    "subcontractor_id": 1,
    "project_id": 1,
    "ipc_type": "interim",
    "current_work_value": 100000.00,
    "retention_percentage": 5.00,
    "currency_id": 1,
    "items": [
        {
            "description": "Electrical installation - Phase 1",
            "unit_id": 1,
            "current_quantity": 100,
            "unit_rate": 1000.00
        }
    ]
}
```

### Creating an Evaluation
```php
POST /api/subcontractor-evaluations
{
    "subcontractor_id": 1,
    "project_id": 1,
    "evaluation_date": "2026-01-03",
    "quality_score": 4,
    "time_performance_score": 5,
    "safety_score": 4,
    "cooperation_score": 5,
    "strengths": "Excellent work quality and timely delivery",
    "weaknesses": "Could improve on documentation",
    "recommendations": "Recommended for future projects"
}
```

## IPC Calculations

The system automatically calculates the following:

1. **Cumulative to Date**: Current work value + Previous cumulative
2. **Gross Amount**: Current work value + Materials on site
3. **Cumulative Advance Deduction**: Current advance deduction + Previous advance payment
4. **Current Retention**: Current work value × (Retention percentage / 100)
5. **Cumulative Retention**: Sum of all retention amounts
6. **Cumulative Back Charges**: Current back charges + Previous back charges
7. **Net Amount**: Gross amount - Advance deduction - Retention - Back charges

## Auto-Generated Codes

The system automatically generates unique codes for:
- **Subcontractors**: `SUB-YYYY-XXXX` (e.g., SUB-2026-0001)
- **Agreements**: `SCA-YYYY-XXXX` (e.g., SCA-2026-0001)
- **Work Orders**: `SWO-YYYY-XXXX` (e.g., SWO-2026-0001)
- **IPCs**: `SIPC-YYYY-XXXX` (e.g., SIPC-2026-0001)

## Security

- All endpoints require authentication via Sanctum
- Multi-tenancy support with company-level data isolation
- Row-level security checks in all controller methods
- Soft delete support for data retention
- Audit trail with created_by and approved_by tracking

## Testing

Run the subcontractor module tests:

```bash
php artisan test --filter SubcontractorTest
```

## Seeding Permissions

Seed the subcontractor permissions:

```bash
php artisan db:seed --class=SubcontractorPermissionsSeeder
```

## Future Enhancements

- PDF generation for IPCs and agreements
- Email notifications for approvals and status changes
- Document management system integration
- Advanced reporting and analytics
- Dashboard widgets for key metrics
- Mobile app support
- Integration with procurement module
- Automated payment scheduling
