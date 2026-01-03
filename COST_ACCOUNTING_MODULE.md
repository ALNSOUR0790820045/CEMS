# Cost Accounting Module

## Overview
The Cost Accounting Module provides comprehensive cost tracking, allocation, and analysis capabilities by project, department, and cost center.

## Features

### 1. Cost Centers
Hierarchical structure for organizing costs by:
- Projects
- Departments
- Activities
- Assets

### 2. Cost Allocation
- Track costs from various sources
- Link to GL accounts and cost centers
- Support multiple currencies
- Polymorphic source tracking (flexible integration)

### 3. Budget Management
- Annual and project-based budgets
- Monthly budget breakdown
- Real-time actual vs budgeted tracking
- Automatic variance calculation

### 4. Reports
- Cost Analysis Report
- Budget Variance Report
- Cost Center Report

## Database Schema

### Tables
- `currencies` - Currency definitions
- `gl_accounts` - General ledger accounts with hierarchical structure
- `projects` - Project tracking
- `cost_centers` - Cost center definitions with hierarchical structure
- `cost_allocations` - Cost allocation transactions
- `budgets` - Budget headers
- `budget_items` - Budget line items with monthly breakdowns

## API Endpoints

### Cost Centers
```
GET    /api/cost-centers              # List all cost centers
POST   /api/cost-centers              # Create new cost center
GET    /api/cost-centers/{id}         # Get cost center details
PUT    /api/cost-centers/{id}         # Update cost center
DELETE /api/cost-centers/{id}         # Delete cost center
```

**Query Parameters:**
- `type` - Filter by type (project, department, activity, asset)
- `is_active` - Filter by active status
- `company_id` - Filter by company

**Example Request:**
```json
POST /api/cost-centers
{
  "code": "CC-001",
  "name": "Marketing Department",
  "type": "department",
  "parent_id": null,
  "is_active": true,
  "company_id": 1
}
```

### Budgets
```
GET    /api/budgets                   # List all budgets
POST   /api/budgets                   # Create new budget
GET    /api/budgets/{id}              # Get budget details
PUT    /api/budgets/{id}              # Update budget
DELETE /api/budgets/{id}              # Delete budget
```

**Query Parameters:**
- `fiscal_year` - Filter by fiscal year
- `budget_type` - Filter by type (operating, capital, project)
- `status` - Filter by status (draft, approved, active, closed)
- `company_id` - Filter by company

**Example Request:**
```json
POST /api/budgets
{
  "budget_name": "2024 Operating Budget",
  "fiscal_year": 2024,
  "budget_type": "operating",
  "status": "draft",
  "total_budget": 500000.00,
  "cost_center_id": 1,
  "project_id": null,
  "company_id": 1
}
```

### Cost Allocations
```
GET    /api/cost-allocations          # List all cost allocations
POST   /api/cost-allocations          # Create new allocation
GET    /api/cost-allocations/{id}     # Get allocation details
PUT    /api/cost-allocations/{id}     # Update allocation
DELETE /api/cost-allocations/{id}     # Delete allocation
```

**Query Parameters:**
- `cost_center_id` - Filter by cost center
- `gl_account_id` - Filter by GL account
- `date_from` - Filter by start date
- `date_to` - Filter by end date
- `company_id` - Filter by company

**Example Request:**
```json
POST /api/cost-allocations
{
  "transaction_date": "2024-01-15",
  "source_type": "Invoice",
  "source_id": 123,
  "cost_center_id": 1,
  "gl_account_id": 5,
  "amount": 1500.00,
  "currency_id": 1,
  "description": "Office supplies purchase",
  "company_id": 1
}
```

### Reports

#### Cost Analysis Report
```
GET /api/reports/cost-analysis
```

**Query Parameters:**
- `company_id` - Filter by company
- `cost_center_id` - Filter by cost center
- `date_from` - Start date
- `date_to` - End date

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "cost_center_id": 1,
      "gl_account_id": 5,
      "currency_id": 1,
      "total_amount": "15000.00",
      "transaction_count": 10,
      "cost_center": {...},
      "gl_account": {...},
      "currency": {...}
    }
  ]
}
```

#### Budget Variance Report
```
GET /api/reports/budget-variance
```

**Query Parameters:**
- `company_id` - Filter by company
- `fiscal_year` - Filter by fiscal year
- `budget_id` - Specific budget ID

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "budget_id": 1,
      "budget_name": "2024 Operating Budget",
      "fiscal_year": 2024,
      "budget_type": "operating",
      "status": "active",
      "cost_center": "Marketing Department",
      "project": null,
      "total_budgeted": "500000.00",
      "total_actual": "475000.00",
      "total_variance": "-25000.00",
      "variance_percentage": -5.0,
      "items": [...]
    }
  ]
}
```

#### Cost Center Report
```
GET /api/reports/cost-center-report
```

**Query Parameters:**
- `company_id` - Filter by company
- `type` - Filter by cost center type
- `date_from` - Start date
- `date_to` - End date

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "cost_center_id": 1,
      "code": "CC-001",
      "name": "Marketing Department",
      "type": "department",
      "is_active": true,
      "total_cost": "125000.00",
      "transaction_count": 45
    }
  ]
}
```

## Models

### CostCenter
**Relationships:**
- `company()` - belongsTo Company
- `parent()` - belongsTo CostCenter (self-referential)
- `children()` - hasMany CostCenter
- `costAllocations()` - hasMany CostAllocation
- `budgets()` - hasMany Budget

### Budget
**Relationships:**
- `company()` - belongsTo Company
- `project()` - belongsTo Project
- `costCenter()` - belongsTo CostCenter
- `budgetItems()` - hasMany BudgetItem

**Methods:**
- `getTotalActualAmount()` - Calculate total actual spending
- `getTotalVariance()` - Calculate total variance

### CostAllocation
**Relationships:**
- `company()` - belongsTo Company
- `costCenter()` - belongsTo CostCenter
- `glAccount()` - belongsTo GlAccount
- `currency()` - belongsTo Currency
- `source()` - morphTo (polymorphic)

### BudgetItem
**Relationships:**
- `budget()` - belongsTo Budget
- `glAccount()` - belongsTo GlAccount

**Computed Fields:**
- `variance` - Automatically calculated as (actual_amount - budgeted_amount)

## Testing

Run the Cost Accounting Module tests:
```bash
php artisan test --filter="CostCenterTest|BudgetTest|CostAllocationTest|ReportTest"
```

## Usage Examples

### Creating a Cost Center Hierarchy
```php
// Create parent department
$department = CostCenter::create([
    'code' => 'DEPT-001',
    'name' => 'Sales Department',
    'type' => 'department',
    'company_id' => 1,
]);

// Create child project under department
$project = CostCenter::create([
    'code' => 'PROJ-001',
    'name' => 'Q1 Campaign',
    'type' => 'project',
    'parent_id' => $department->id,
    'company_id' => 1,
]);
```

### Recording a Cost Allocation
```php
$allocation = CostAllocation::create([
    'transaction_date' => now(),
    'source_type' => 'App\Models\Invoice',
    'source_id' => $invoice->id,
    'cost_center_id' => $costCenter->id,
    'gl_account_id' => $glAccount->id,
    'amount' => 1500.00,
    'currency_id' => $currency->id,
    'description' => 'Office supplies',
    'company_id' => 1,
]);
```

### Creating a Budget with Items
```php
$budget = Budget::create([
    'budget_name' => '2024 Marketing Budget',
    'fiscal_year' => 2024,
    'budget_type' => 'operating',
    'status' => 'draft',
    'total_budget' => 120000,
    'cost_center_id' => $costCenter->id,
    'company_id' => 1,
]);

// Add monthly budget items
for ($month = 1; $month <= 12; $month++) {
    BudgetItem::create([
        'budget_id' => $budget->id,
        'gl_account_id' => $glAccount->id,
        'month' => $month,
        'budgeted_amount' => 10000,
        'actual_amount' => 0,
    ]);
}
```

## Authentication

All API endpoints require authentication using Laravel Sanctum:
```
Authorization: Bearer {token}
```

## Error Handling

The API returns consistent error responses:

**Validation Error (422):**
```json
{
  "success": false,
  "errors": {
    "field_name": ["Error message"]
  }
}
```

**Not Found (404):**
```json
{
  "message": "Resource not found"
}
```

**Success Response:**
```json
{
  "success": true,
  "message": "Operation completed successfully",
  "data": {...}
}
```

## Best Practices

1. **Cost Center Hierarchy**: Organize cost centers logically (Department → Project → Activity)
2. **Budget Approval**: Follow workflow: draft → approved → active → closed
3. **Regular Updates**: Update actual amounts in budget items regularly
4. **Variance Analysis**: Review variance reports monthly
5. **Data Integrity**: Always link cost allocations to valid cost centers and GL accounts

## Support

For issues or questions, please refer to the main CEMS documentation or contact the development team.
