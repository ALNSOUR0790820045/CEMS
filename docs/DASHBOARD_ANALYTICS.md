# Dashboard & Analytics Module Documentation

## Overview
The Dashboard & Analytics Module provides comprehensive business intelligence and data visualization capabilities for the CEMS ERP system. It includes executive dashboards, project tracking, financial analytics, and real-time KPIs.

## Features

### 1. Executive Dashboard
- **Financial KPIs**: Revenue, Profit, Cash Balance, Profit Margin
- **Project KPIs**: Active Projects, Progress, Budget Utilization, EVM Metrics
- **Operational KPIs**: Inventory Value, Procurement Status
- **HR KPIs**: Employee Count, Attendance Rate, Payroll
- **Real-time Data**: All metrics are calculated in real-time from the database

### 2. Project Dashboard
- Project status overview with detailed information
- Budget vs actual cost comparison
- Schedule progress tracking
- Resource utilization metrics
- Earned Value Management (EVM) metrics:
  - **PV (Planned Value)**: Budgeted cost of work scheduled
  - **EV (Earned Value)**: Budgeted cost of work performed
  - **AC (Actual Cost)**: Actual cost of work performed
  - **SPI (Schedule Performance Index)**: EV / PV
  - **CPI (Cost Performance Index)**: EV / AC

### 3. Financial Dashboard
- **P&L Summary**: Income, Expenses, Net Profit
- **Cash Flow Trend**: Cumulative cash flow over 12 months
- **AR/AP Aging**: Outstanding receivables and payables
- **Revenue by Project**: Project-wise revenue breakdown
- **Expense Breakdown**: Category-wise expense analysis

### 4. Charts & Visualizations
- **Line Charts**: Revenue trends, cash flow
- **Bar Charts**: Project budget comparison, revenue by project
- **Pie Charts**: Project status distribution, expense breakdown
- **Real-time Updates**: Charts update dynamically with data changes

## API Endpoints

### Executive Dashboard
```http
GET /api/dashboard/executive
```
Returns comprehensive KPIs across all categories (financial, project, operational, HR).

**Response:**
```json
{
  "success": true,
  "data": {
    "kpis": {
      "financial": { ... },
      "project": { ... },
      "operational": { ... },
      "hr": { ... }
    },
    "timestamp": "2026-01-04T07:29:57Z"
  }
}
```

### Project Dashboard
```http
GET /api/dashboard/project/{id}
```
Returns detailed KPIs and metrics for a specific project.

**Response:**
```json
{
  "success": true,
  "data": {
    "project": { ... },
    "spi": 1.1,
    "cpi": 0.89,
    "progress": 55,
    "budget": 1000000,
    "actual_cost": 600000,
    "budget_remaining": 400000
  }
}
```

### Financial Dashboard
```http
GET /api/dashboard/financial
```
Returns financial KPIs and metrics.

**Response:**
```json
{
  "success": true,
  "data": {
    "financial_kpis": {
      "monthly_revenue": 150000,
      "yearly_revenue": 1800000,
      "monthly_profit": 40000,
      "profit_margin": 25.5
    }
  }
}
```

### All KPIs
```http
GET /api/kpis
```
Returns all KPIs in one response.

### Chart Data
```http
GET /api/charts/{chart_type}
```

**Available chart types:**
- `revenue-trend`: 12-month revenue and expense trend
- `project-status`: Project status distribution (pie chart)
- `project-budget`: Budget vs actual for projects (bar chart)
- `expense-breakdown`: Expense categories (pie chart)
- `revenue-by-project`: Revenue by project (bar chart)
- `cash-flow`: Cumulative cash flow (line chart)

**Response:**
```json
{
  "success": true,
  "data": {
    "labels": ["Jan 2025", "Feb 2025", ...],
    "datasets": [
      {
        "label": "Revenue",
        "data": [100000, 150000, ...],
        "borderColor": "rgb(0, 113, 227)"
      }
    ]
  }
}
```

### Save Dashboard Layout
```http
POST /api/dashboard/save-layout
```

**Request Body:**
```json
{
  "dashboard_type": "executive",
  "layout_config": {
    "widgets": [
      {"id": 1, "position": "top-left"},
      {"id": 2, "position": "top-right"}
    ]
  }
}
```

## Database Schema

### Projects Table
- `id`: Primary key
- `name`: Project name
- `code`: Unique project code
- `company_id`: Foreign key to companies
- `status`: active, completed, on_hold, delayed
- `start_date`, `end_date`: Project timeline
- `budget`: Total project budget
- `planned_value`: PV for EVM
- `earned_value`: EV for EVM
- `actual_cost`: AC for EVM
- `progress`: Completion percentage (0-100)
- `client_name`, `location`: Project details

### Financial Transactions Table
- `id`: Primary key
- `company_id`: Foreign key to companies
- `project_id`: Foreign key to projects (nullable)
- `type`: income or expense
- `category`: Transaction category
- `amount`: Transaction amount
- `date`: Transaction date
- `status`: pending, completed, cancelled

### Inventories Table
- `id`: Primary key
- `company_id`: Foreign key to companies
- `project_id`: Foreign key to projects (nullable)
- `item_name`: Item name
- `category`: Item category
- `quantity`: Quantity in stock
- `unit`: Unit of measurement
- `unit_price`: Price per unit
- `total_value`: Total inventory value

### Attendances Table
- `id`: Primary key
- `company_id`: Foreign key to companies
- `user_id`: Foreign key to users
- `date`: Attendance date
- `check_in`, `check_out`: Timestamps
- `status`: present, absent, late, leave
- `hours_worked`: Total hours

### Dashboard Layouts Table
- `id`: Primary key
- `user_id`: Foreign key to users
- `dashboard_type`: Dashboard identifier
- `layout_config`: JSON configuration

## Services

### KpiService
Handles all KPI calculations.

**Methods:**
- `getAllKpis()`: Returns all KPI categories
- `getFinancialKpis()`: Financial metrics
- `getProjectKpis()`: Project metrics
- `getOperationalKpis()`: Operational metrics
- `getHrKpis()`: HR metrics
- `getProjectSpecificKpis($projectId)`: Project-specific metrics

### ChartService
Generates chart data for visualizations.

**Methods:**
- `getRevenueTrend()`: 12-month revenue/expense trend
- `getProjectStatusDistribution()`: Project status pie chart
- `getProjectBudgetComparison()`: Budget vs actual bar chart
- `getExpenseBreakdown()`: Expense categories pie chart
- `getRevenueByProject()`: Revenue by project bar chart
- `getCashFlowTrend()`: Cumulative cash flow line chart
- `getChartByType($type)`: Get any chart by type

## Frontend Views

### Executive Dashboard (`/dashboards/executive`)
Displays comprehensive KPIs with interactive charts:
- 8 KPI cards (revenue, profit, cash, projects, inventory, employees)
- Revenue trend chart
- Project status pie chart
- Expense breakdown
- Cash flow trend

### Project Dashboard (`/dashboards/project`)
Project-specific analytics:
- Project selector dropdown
- Project information card
- EVM metrics (PV, EV, AC, SPI, CPI)
- Budget comparison

### Financial Dashboard (`/dashboards/financial`)
Financial analytics and reports:
- Financial KPI cards
- P&L summary
- Revenue trend chart
- Revenue by project
- Expense breakdown
- Cash flow trend
- AR/AP summary

## Usage Examples

### Accessing Dashboards
1. Login to CEMS ERP
2. Navigate to "الإدارة العليا" (Management) menu
3. Select desired dashboard:
   - لوحة التحكم التنفيذية (Executive Dashboard)
   - لوحة التحكم المالية (Financial Dashboard)
   - لوحة تحكم المشاريع (Project Dashboard)

### API Usage (JavaScript)
```javascript
// Fetch executive KPIs
const response = await fetch('/api/kpis', {
  headers: {
    'Accept': 'application/json',
    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
  }
});
const data = await response.json();

// Fetch chart data
const chartResponse = await fetch('/api/charts/revenue-trend', {
  headers: {
    'Accept': 'application/json',
    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
  }
});
const chartData = await chartResponse.json();

// Render with Chart.js
const ctx = document.getElementById('myChart').getContext('2d');
new Chart(ctx, {
  type: 'line',
  data: chartData.data,
  options: {
    responsive: true
  }
});
```

## Testing

### Running Tests
```bash
# Run all tests
php artisan test

# Run specific test suite
php artisan test --filter DashboardApiTest
php artisan test --filter KpiServiceTest
php artisan test --filter ChartServiceTest
```

### Test Coverage
- **DashboardApiTest**: Tests all API endpoints
- **KpiServiceTest**: Tests KPI calculations
- **ChartServiceTest**: Tests chart data generation

## Seeding Sample Data

To populate the database with sample dashboard data:

```bash
php artisan db:seed --class=DashboardDataSeeder
```

This will create:
- 4 sample projects (various statuses)
- 50 financial transactions
- 5 inventory items
- 30 days of attendance records

## Performance Considerations

1. **Database Indexes**: Ensure indexes on:
   - `financial_transactions.date`
   - `financial_transactions.company_id`
   - `projects.status`
   - `projects.company_id`

2. **Caching**: Consider caching KPI results for frequently accessed data

3. **Query Optimization**: Use eager loading for relationships

## Future Enhancements

1. **Custom Widgets**: Drag-and-drop dashboard customization
2. **Export Functionality**: PDF/Excel export of dashboards
3. **Real-time Updates**: WebSocket integration for live data
4. **Advanced Filtering**: Date range and custom filters
5. **Heat Maps**: Project status heat maps
6. **Predictive Analytics**: Forecasting based on historical data
7. **Mobile Optimization**: Mobile-responsive dashboard layouts

## Security

- All API endpoints require authentication (Sanctum)
- User can only access data for their assigned company
- CSRF protection on all state-changing requests
- Input validation on all API endpoints

## Troubleshooting

### No Data Showing
1. Ensure database is migrated: `php artisan migrate`
2. Run seeder: `php artisan db:seed --class=DashboardDataSeeder`
3. Check user authentication

### Charts Not Rendering
1. Ensure Chart.js is loaded
2. Check browser console for errors
3. Verify API endpoints are returning data

### API Returns 401
1. Ensure user is authenticated
2. Check Sanctum configuration
3. Verify CSRF token is included

## Support

For issues or questions, please contact the development team or refer to the Laravel documentation at https://laravel.com/docs
