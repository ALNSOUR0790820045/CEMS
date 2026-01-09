# Cost Accounting Module Documentation

## Overview
A comprehensive Cost Accounting Module (محاسبة التكاليف) for tracking expenses and costs across projects in the CEMS ERP system.

## Database Schema

### 1. cost_categories - فئات التكاليف
Categorizes different types of costs.

**Fields:**
- `id` - Primary key
- `code` - Unique category code
- `name` - Arabic name
- `name_en` - English name
- `type` - Category type (direct_material, direct_labor, subcontractor, equipment, overhead, other)
- `is_active` - Active status
- `company_id` - Foreign key to companies
- `timestamps` - Created/updated timestamps

### 2. cost_allocations - توزيع التكاليف
Tracks cost allocations to projects and cost centers.

**Fields:**
- `id` - Primary key
- `allocation_number` - Auto-generated (CA-YYYY-XXXX format)
- `allocation_date` - Date of allocation
- `cost_center_id` - Foreign key to cost_centers
- `cost_category_id` - Foreign key to cost_categories
- `project_id` - Foreign key to projects (optional)
- `gl_account_id` - Foreign key to gl_accounts
- `amount` - Allocation amount
- `currency_id` - Foreign key to currencies
- `exchange_rate` - Exchange rate (default 1.0000)
- `description` - Allocation description
- `reference_type` - Type (invoice, payroll, journal, manual)
- `reference_id` - Reference to source document
- `status` - Status (draft, posted, reversed)
- `posted_by_id` - User who posted
- `posted_at` - Posting timestamp
- `company_id` - Foreign key to companies
- `timestamps` - Created/updated timestamps
- `soft_deletes` - Soft delete support

### 3. budgets - الميزانيات
Annual, project, or department budgets.

**Fields:**
- `id` - Primary key
- `budget_number` - Auto-generated (BDG-YYYY-XXXX format)
- `budget_name` - Budget name
- `fiscal_year` - Fiscal year
- `budget_type` - Type (annual, project, department)
- `cost_center_id` - Foreign key to cost_centers (optional)
- `project_id` - Foreign key to projects (optional)
- `total_amount` - Total budget amount
- `status` - Status (draft, approved, active, closed)
- `approved_by_id` - User who approved
- `approved_at` - Approval timestamp
- `company_id` - Foreign key to companies
- `timestamps` - Created/updated timestamps
- `soft_deletes` - Soft delete support

### 4. budget_items - بنود الميزانية
Individual line items within budgets.

**Fields:**
- `id` - Primary key
- `budget_id` - Foreign key to budgets
- `cost_category_id` - Foreign key to cost_categories
- `gl_account_id` - Foreign key to gl_accounts (optional)
- `month` - Month (1-12) for monthly budgets (optional)
- `budgeted_amount` - Budgeted amount
- `actual_amount` - Actual spent (computed)
- `variance` - Variance (computed)
- `notes` - Notes
- `timestamps` - Created/updated timestamps

### 5. cost_centers (Updated) - مراكز التكلفة
Added `type` field to existing table.

**New Field:**
- `type` - Cost center type (project, department, overhead, administrative)

## Models

### CostCategory
- Relationships: company, costAllocations, budgetItems
- Scopes: active, byType, byCompany

### CostAllocation
- Relationships: company, costCenter, costCategory, project, glAccount, currency, postedBy
- Scopes: byStatus, byProject, byCostCenter, byCompany
- Methods: `generateAllocationNumber()` - Auto-generates allocation numbers

### Budget
- Relationships: company, costCenter, project, approvedBy, items
- Scopes: byStatus, byType, byFiscalYear, byCompany
- Methods: `generateBudgetNumber($fiscalYear)` - Auto-generates budget numbers

### BudgetItem
- Relationships: budget, costCategory, glAccount
- Methods: 
  - `calculateVariance()` - Calculates variance
  - `getVariancePercentageAttribute()` - Returns variance as percentage

### CostCenter (Updated)
- New relationship: costAllocations
- New scope: byType

## API Endpoints

### Cost Centers
```
GET    /api/cost-centers              - List cost centers
POST   /api/cost-centers              - Create cost center
GET    /api/cost-centers/{id}         - Get cost center details
PUT    /api/cost-centers/{id}         - Update cost center
DELETE /api/cost-centers/{id}         - Delete cost center
```

### Cost Categories
```
GET    /api/cost-categories           - List cost categories
POST   /api/cost-categories           - Create cost category
GET    /api/cost-categories/{id}      - Get cost category details
PUT    /api/cost-categories/{id}      - Update cost category
DELETE /api/cost-categories/{id}      - Delete cost category
```

### Budgets
```
GET    /api/budgets                   - List budgets
POST   /api/budgets                   - Create budget with items
GET    /api/budgets/{id}              - Get budget details
PUT    /api/budgets/{id}              - Update budget (draft only)
DELETE /api/budgets/{id}              - Delete budget (draft only)
POST   /api/budgets/{id}/approve      - Approve budget
```

### Cost Allocations
```
GET    /api/cost-allocations          - List cost allocations
POST   /api/cost-allocations          - Create cost allocation
GET    /api/cost-allocations/{id}     - Get allocation details
PUT    /api/cost-allocations/{id}     - Update allocation (draft only)
DELETE /api/cost-allocations/{id}     - Delete allocation (draft only)
POST   /api/cost-allocations/{id}/post    - Post allocation
POST   /api/cost-allocations/{id}/reverse - Reverse allocation
```

### Cost Reports
```
GET /api/reports/cost-analysis         - Cost analysis by category and cost center
GET /api/reports/budget-variance       - Budget variance report
GET /api/reports/cost-center-report    - Cost center detailed report
GET /api/reports/project-cost-summary  - Project cost summary report
```

## Business Rules

1. **Budgets**
   - Cannot update or delete approved budgets
   - Only draft budgets can be approved
   - Approval records the approver and timestamp

2. **Cost Allocations**
   - Cannot update or delete posted allocations
   - Only draft allocations can be posted
   - Only posted allocations can be reversed
   - Posting records the poster and timestamp

3. **Auto-Numbering**
   - Budget numbers: BDG-YYYY-XXXX (e.g., BDG-2026-0001)
   - Allocation numbers: CA-YYYY-XXXX (e.g., CA-2026-0001)

## Usage Examples

### Creating a Budget
```json
POST /api/budgets
{
  "budget_name": "Annual Budget 2026",
  "fiscal_year": 2026,
  "budget_type": "annual",
  "cost_center_id": 1,
  "total_amount": 100000,
  "items": [
    {
      "cost_category_id": 1,
      "budgeted_amount": 50000,
      "month": 1,
      "notes": "Q1 Budget"
    }
  ]
}
```

### Creating a Cost Allocation
```json
POST /api/cost-allocations
{
  "allocation_date": "2026-01-07",
  "cost_center_id": 1,
  "cost_category_id": 1,
  "project_id": 1,
  "gl_account_id": 1,
  "amount": 5000,
  "currency_id": 1,
  "exchange_rate": 1.0000,
  "description": "Material costs for Project X",
  "reference_type": "invoice",
  "reference_id": 123
}
```

### Getting Budget Variance Report
```
GET /api/reports/budget-variance?budget_id=1
```

Response includes:
- Budget details
- Items with actual amounts calculated from allocations
- Variance and variance percentage for each item
- Summary totals

## Testing

Comprehensive test suite includes:
- Cost center CRUD operations
- Cost category CRUD operations
- Budget creation and approval workflow
- Cost allocation posting and reversal
- Variance calculations
- Business rule validations

**Note:** Tests require resolution of pre-existing duplicate migration files in the repository.

## Security & Permissions

All endpoints are protected by `auth:sanctum` middleware and require:
- User authentication
- Company context (company_id)
- Appropriate permissions (to be configured in Spatie Permission system)

## Multi-Currency Support

- Cost allocations support multiple currencies
- Exchange rate is recorded with each allocation
- Base currency defined in company settings

## Audit Trail

- All models include timestamps
- Soft deletes enabled for cost_allocations and budgets
- Approval and posting actions record user and timestamp

## Integration Points

- **Projects Module**: Cost allocations can be linked to projects
- **GL Module**: All allocations reference GL accounts
- **AP/AR Modules**: Cost allocations can reference invoices
- **Payroll Module**: Cost allocations can reference payroll entries

## Reporting Features

1. **Cost Analysis**: Breakdown by category and cost center
2. **Budget Variance**: Compare budgeted vs actual with variance analysis
3. **Cost Center Report**: Detailed costs for a specific cost center
4. **Project Cost Summary**: Complete cost analysis for a project

All reports support date range filtering and provide both detailed data and summary totals.
