# Project Cost Control Module Documentation

## Overview
This module provides comprehensive project cost control and management capabilities with earned value management (EVM) analysis, variance tracking, and forecasting.

## Features Implemented

### 1. Database Structure
- **project_budgets**: Main budget table for projects
- **project_budget_items**: Detailed budget line items
- **cost_codes**: Hierarchical cost code structure
- **actual_costs**: Track actual costs from invoices, payroll, etc.
- **committed_costs**: Track commitments (POs, subcontracts)
- **cost_forecasts**: Cost projections and forecasts
- **variance_analysis**: Automatic variance detection and analysis
- **cost_reports**: Cost performance reports with EVM metrics

### 2. Models with Relationships
All models include:
- Proper relationships (belongsTo, hasMany)
- Query scopes for filtering
- Helper methods for calculations
- Soft deletes where appropriate

### 3. API Controllers
#### ProjectBudgetController
- CRUD operations
- Budget approval workflow
- Revision creation
- BOQ import
- Budget items management

#### CostCodeController
- Hierarchical cost code management
- Tree view support
- Active/inactive filtering

#### ActualCostController
- Cost recording from various sources
- Automatic budget item updates
- Project-level tracking

#### CommittedCostController
- Commitment tracking
- Invoice allocation
- Change order management
- Sync with POs and subcontracts

#### CostForecastController
- Manual and automatic forecasting
- Trend-based projections
- Period-based analysis

#### VarianceAnalysisController
- Automatic variance detection
- Threshold-based alerting (>5%)
- Status tracking workflow
- Corrective action tracking

#### ProjectCostReportController
- Cost summary reports
- Budget vs. actual analysis
- EVM calculations (CPI, SPI, EAC, VAC, TCPI)
- Cost trends
- Commitment status
- Cost breakdown by code

### 4. API Routes
All routes are protected with `auth:sanctum` middleware:

```
POST   /api/project-budgets
GET    /api/project-budgets
GET    /api/project-budgets/{id}
PUT    /api/project-budgets/{id}
DELETE /api/project-budgets/{id}
GET    /api/project-budgets/project/{projectId}
POST   /api/project-budgets/{id}/approve
POST   /api/project-budgets/{id}/revise
GET    /api/project-budgets/{id}/items
POST   /api/project-budgets/{id}/items
POST   /api/project-budgets/{id}/import-boq

GET    /api/cost-codes/tree
(+ standard CRUD routes)

POST   /api/actual-costs/import
(+ standard CRUD routes)

POST   /api/committed-costs/sync-pos
POST   /api/committed-costs/sync-subcontracts
(+ standard CRUD routes)

POST   /api/cost-forecasts/generate/{projectId}
(+ standard CRUD routes)

POST   /api/variance-analysis/analyze/{projectId}
(+ standard CRUD routes)

GET    /api/project-cost-reports/cost-summary/{projectId}
GET    /api/project-cost-reports/budget-vs-actual/{projectId}
GET    /api/project-cost-reports/cost-breakdown/{projectId}
GET    /api/project-cost-reports/commitment-status/{projectId}
GET    /api/project-cost-reports/cost-trend/{projectId}
GET    /api/project-cost-reports/evm-analysis/{projectId}
GET    /api/project-cost-reports/variance-report/{projectId}
GET    /api/project-cost-reports/forecast-report/{projectId}
GET    /api/project-cost-reports/cost-to-complete/{projectId}
POST   /api/project-cost-reports/generate-monthly/{projectId}
(+ standard CRUD routes)
```

### 5. EVM Formulas Implemented
- **CPI (Cost Performance Index)**: EV / AC
- **SPI (Schedule Performance Index)**: EV / PV
- **EAC (Estimate at Completion)**: BAC / CPI
- **VAC (Variance at Completion)**: BAC - EAC
- **TCPI (To Complete Performance Index)**: (BAC - EV) / (BAC - AC)
- **Cost Variance**: EV - AC
- **Schedule Variance**: EV - PV

### 6. Business Rules Implemented
- Draft budgets can be edited
- Approved/active budgets cannot be modified
- Budget revisions create new versions
- Variance analysis triggers when variance > 5%
- Automatic budget item updates from actual costs
- Committed cost status updates based on invoicing

### 7. Testing
Comprehensive test coverage including:
- ProjectBudget CRUD operations
- Budget approval workflow
- Cost code hierarchy
- Variance calculations
- EVM metric calculations
- Favorable/unfavorable variance detection

## Installation & Setup

1. Run migrations:
```bash
php artisan migrate
```

2. The module will automatically create the following tables:
   - project_budgets
   - project_budget_items
   - cost_codes
   - actual_costs
   - committed_costs
   - cost_forecasts
   - variance_analysis
   - cost_reports

## Usage Examples

### Creating a Project Budget
```json
POST /api/project-budgets
{
  "project_id": 1,
  "budget_type": "original",
  "budget_date": "2026-01-15",
  "direct_costs": 700000,
  "indirect_costs": 200000,
  "contingency_percentage": 10,
  "profit_margin_percentage": 15,
  "currency_id": 1
}
```

### Recording Actual Costs
```json
POST /api/actual-costs
{
  "project_id": 1,
  "cost_code_id": 5,
  "transaction_date": "2026-01-15",
  "reference_type": "invoice",
  "reference_id": 123,
  "reference_number": "INV-001",
  "description": "Construction materials",
  "amount": 50000,
  "currency_id": 1
}
```

### Analyzing Variances
```json
POST /api/variance-analysis/analyze/1
{
  "period_month": 1,
  "period_year": 2026
}
```

### Getting EVM Analysis
```json
GET /api/project-cost-reports/evm-analysis/1?percentage_complete=50
```

## Dependencies
- Laravel 12.x
- PHP 8.2+
- Existing models: Project, Contract, Currency, Unit, Vendor, User, Company

## Notes
- All monetary values are stored with 2 decimal precision
- Quantities support 3 decimal places
- Budget numbers auto-generate as BUD-YYYY-XXXX
- Cost report numbers auto-generate as CR-YYYY-XXX
- Multi-currency support with exchange rates
- Soft deletes enabled on main entities

## Future Enhancements
- Automatic sync from AP invoices
- Automatic sync from payroll
- Real-time variance alerts
- Dashboard widgets
- Excel export capabilities
- Mobile app support
