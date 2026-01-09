# Dashboard & Analytics Module Documentation

## Overview
Complete implementation of a Dashboard & Analytics module for the CEMS (Construction Enterprise Management System) that provides comprehensive dashboards, widgets, KPI tracking, and business analytics.

## Database Tables

### 1. `dashboards` - Dashboard Management
Stores user-created dashboards with customizable layouts and types.

**Columns:**
- `id` - Primary key
- `name` - Dashboard name (Arabic)
- `name_en` - Dashboard name (English)
- `description` - Dashboard description
- `type` - Dashboard type (executive, project, financial, hr, operations)
- `layout` - JSON field for widget layout configuration
- `is_default` - Boolean flag for default dashboard
- `is_public` - Boolean flag for public access
- `created_by_id` - Foreign key to users table
- `company_id` - Foreign key to companies table
- `timestamps` - Created at, Updated at
- `soft_deletes` - Deleted at (soft delete support)

### 2. `dashboard_widgets` - Widget Components
Stores individual widgets that can be added to dashboards.

**Columns:**
- `id` - Primary key
- `dashboard_id` - Foreign key to dashboards table
- `widget_type` - Type of widget (chart, kpi, table, counter, gauge)
- `title` - Widget title
- `data_source` - Data source identifier
- `config` - JSON field for widget configuration
- `position_x` - X position in grid layout
- `position_y` - Y position in grid layout
- `width` - Widget width (1-12 grid units)
- `height` - Widget height in grid units
- `refresh_interval` - Auto-refresh interval in seconds
- `is_visible` - Boolean visibility flag
- `timestamps` - Created at, Updated at

### 3. `kpi_definitions` - KPI Metrics Definition
Defines the structure and rules for Key Performance Indicators.

**Columns:**
- `id` - Primary key
- `code` - Unique KPI code
- `name` - KPI name (Arabic)
- `name_en` - KPI name (English)
- `description` - KPI description
- `category` - Category (financial, operational, hr, project)
- `calculation_formula` - Formula for calculation
- `unit` - Unit of measurement (percentage, currency, number, days)
- `target_value` - Target value to achieve
- `warning_threshold` - Warning threshold value
- `critical_threshold` - Critical threshold value
- `frequency` - Measurement frequency (daily, weekly, monthly, quarterly)
- `is_active` - Boolean active status
- `company_id` - Foreign key to companies table
- `timestamps` - Created at, Updated at

### 4. `kpi_values` - KPI Measurements
Stores actual KPI values and calculations over time.

**Columns:**
- `id` - Primary key
- `kpi_definition_id` - Foreign key to kpi_definitions table
- `period_date` - Date of measurement period
- `actual_value` - Actual measured value
- `target_value` - Target value for this period
- `variance` - Difference between actual and target
- `variance_percentage` - Variance as percentage
- `status` - Status (on_track, warning, critical)
- `project_id` - Optional foreign key to projects table
- `department_id` - Optional foreign key to departments table
- `company_id` - Foreign key to companies table
- `timestamps` - Created at, Updated at

## Models

### Dashboard Model
**Location:** `app/Models/Dashboard.php`

**Relationships:**
- `createdBy()` - BelongsTo User
- `company()` - BelongsTo Company
- `widgets()` - HasMany DashboardWidget

**Scopes:**
- `scopePublic()` - Filter public dashboards
- `scopeDefault()` - Filter default dashboards
- `scopeByType($type)` - Filter by dashboard type

### DashboardWidget Model
**Location:** `app/Models/DashboardWidget.php`

**Relationships:**
- `dashboard()` - BelongsTo Dashboard

**Scopes:**
- `scopeVisible()` - Filter visible widgets
- `scopeByType($type)` - Filter by widget type

### KpiDefinition Model
**Location:** `app/Models/KpiDefinition.php`

**Relationships:**
- `company()` - BelongsTo Company
- `values()` - HasMany KpiValue

**Scopes:**
- `scopeActive()` - Filter active KPIs
- `scopeByCategory($category)` - Filter by category
- `scopeByFrequency($frequency)` - Filter by frequency

### KpiValue Model
**Location:** `app/Models/KpiValue.php`

**Relationships:**
- `kpiDefinition()` - BelongsTo KpiDefinition
- `project()` - BelongsTo Project
- `department()` - BelongsTo Department
- `company()` - BelongsTo Company

**Scopes:**
- `scopeByStatus($status)` - Filter by status
- `scopeByPeriod($startDate, $endDate)` - Filter by date range

## API Controllers

### 1. DashboardController
**Location:** `app/Http/Controllers/Api/DashboardController.php`

**Endpoints:**
- `GET /api/dashboards` - List all dashboards
- `POST /api/dashboards` - Create new dashboard
- `GET /api/dashboards/{id}` - Show specific dashboard
- `PUT /api/dashboards/{id}` - Update dashboard
- `DELETE /api/dashboards/{id}` - Delete dashboard (soft delete)
- `GET /api/dashboards/{id}/widgets` - Get dashboard widgets
- `POST /api/dashboards/{id}/widgets` - Add widget to dashboard
- `PUT /api/dashboards/{id}/layout` - Update dashboard layout

**Features:**
- Automatic handling of default dashboard (only one default per type)
- Company-scoped queries
- Widget management through dashboard

### 2. WidgetController
**Location:** `app/Http/Controllers/Api/WidgetController.php`

**Endpoints:**
- `GET /api/widgets` - List all widgets
- `POST /api/widgets` - Create new widget
- `GET /api/widgets/{id}` - Show specific widget
- `PUT /api/widgets/{id}` - Update widget
- `DELETE /api/widgets/{id}` - Delete widget
- `GET /api/widgets/{id}/data` - Get widget data
- `POST /api/widgets/{id}/refresh` - Refresh widget data

**Features:**
- Support for multiple widget types
- Flexible positioning with grid system
- Configurable auto-refresh intervals

### 3. KpiController
**Location:** `app/Http/Controllers/Api/KpiController.php`

**Endpoints:**
- `GET /api/kpi-definitions` - List KPI definitions
- `POST /api/kpi-definitions` - Create KPI definition
- `GET /api/kpi-definitions/{id}` - Show KPI definition
- `PUT /api/kpi-definitions/{id}` - Update KPI definition
- `DELETE /api/kpi-definitions/{id}` - Delete KPI definition
- `GET /api/kpi-values` - Get KPI values with filters
- `POST /api/kpi-values/calculate` - Calculate and store KPI value

**Features:**
- Automatic variance calculation
- Status determination based on thresholds
- Support for project and department-specific KPIs
- Date range filtering

### 4. AnalyticsController
**Location:** `app/Http/Controllers/Api/AnalyticsController.php`

**Endpoints:**
- `GET /api/analytics/project-summary` - Project statistics summary
- `GET /api/analytics/financial-overview` - Financial metrics overview
- `GET /api/analytics/revenue-trend` - Revenue trend over time
- `GET /api/analytics/expense-breakdown` - Expense breakdown by vendor
- `GET /api/analytics/cash-position` - Current cash flow position
- `GET /api/analytics/project-performance` - Project performance metrics
- `GET /api/analytics/hr-metrics` - HR and employee metrics

**Analytics Provided:**

1. **Project Summary:**
   - Total projects count
   - Active projects count
   - Completed projects count
   - Total budget across all projects
   - Total spent across all projects

2. **Financial Overview:**
   - Total revenue (paid invoices)
   - Total expenses (paid bills)
   - Accounts receivable
   - Accounts payable
   - Net profit calculation

3. **Revenue Trend:**
   - Monthly revenue data
   - Configurable time period (default 12 months)
   - Chart-ready format (labels and data arrays)

4. **Expense Breakdown:**
   - Top 10 vendors by expense
   - Total expense per vendor
   - Chart-ready format

5. **Cash Position:**
   - Cash inflow (current month)
   - Cash outflow (current month)
   - Net cash flow
   - Bank account balances

6. **Project Performance:**
   - Top 10 projects by budget
   - Budget vs actual cost
   - Progress percentage
   - Variance calculations

7. **HR Metrics:**
   - Total employees
   - Active employees
   - Department count
   - Employee distribution by department

## API Routes
All routes are defined in `routes/api.php` under the `auth:sanctum` middleware group.

```php
// Dashboards & Analytics
Route::apiResource('dashboards', DashboardController::class);
Route::get('dashboards/{dashboard}/widgets', [DashboardController::class, 'widgets']);
Route::post('dashboards/{dashboard}/widgets', [DashboardController::class, 'addWidget']);
Route::put('dashboards/{dashboard}/layout', [DashboardController::class, 'updateLayout']);

// Widgets
Route::apiResource('widgets', WidgetController::class);
Route::get('widgets/{widget}/data', [WidgetController::class, 'getData']);
Route::post('widgets/{widget}/refresh', [WidgetController::class, 'refresh']);

// KPIs
Route::apiResource('kpi-definitions', KpiController::class);
Route::get('kpi-values', [KpiController::class, 'getValues']);
Route::post('kpi-values/calculate', [KpiController::class, 'calculate']);

// Analytics
Route::get('analytics/project-summary', [AnalyticsController::class, 'projectSummary']);
Route::get('analytics/financial-overview', [AnalyticsController::class, 'financialOverview']);
Route::get('analytics/revenue-trend', [AnalyticsController::class, 'revenueTrend']);
Route::get('analytics/expense-breakdown', [AnalyticsController::class, 'expenseBreakdown']);
Route::get('analytics/cash-position', [AnalyticsController::class, 'cashPosition']);
Route::get('analytics/project-performance', [AnalyticsController::class, 'projectPerformance']);
Route::get('analytics/hr-metrics', [AnalyticsController::class, 'hrMetrics']);
```

## Testing

### Test Files
1. `tests/Feature/DashboardApiTest.php` - Dashboard CRUD and widget management tests
2. `tests/Feature/KpiApiTest.php` - KPI definition and value calculation tests
3. `tests/Feature/AnalyticsApiTest.php` - Analytics endpoint tests

### Factory Files
1. `database/factories/DashboardFactory.php` - Dashboard test data
2. `database/factories/DashboardWidgetFactory.php` - Widget test data
3. `database/factories/KpiDefinitionFactory.php` - KPI definition test data
4. `database/factories/KpiValueFactory.php` - KPI value test data

### Test Coverage
- Dashboard CRUD operations (11 tests)
- Widget management (included in dashboard tests)
- KPI creation and management (11 tests)
- KPI value calculation and status determination (3 tests)
- All analytics endpoints (8 tests)

**Total: 30+ comprehensive feature tests**

## Chart Types Supported

1. **Line Chart** - Revenue vs Expenses, Trends over time
2. **Bar Chart** - Project comparison, Top projects by revenue
3. **Pie Chart** - Expense distribution, Category breakdown
4. **Grouped Bar** - Monthly comparisons, Multi-series data
5. **Area Chart** - Cash flow visualization
6. **Progress Bars** - Project completion status
7. **Gauge Charts** - KPI performance indicators
8. **Counter Widgets** - Single value metrics
9. **Table Widgets** - Detailed data display

## Usage Examples

### Creating a Dashboard
```bash
POST /api/dashboards
{
  "name": "لوحة التحكم التنفيذية",
  "name_en": "Executive Dashboard",
  "description": "Dashboard for executive overview",
  "type": "executive",
  "is_default": true,
  "is_public": false
}
```

### Adding a Widget
```bash
POST /api/dashboards/1/widgets
{
  "widget_type": "chart",
  "title": "Monthly Revenue",
  "data_source": "revenue_trend",
  "position_x": 0,
  "position_y": 0,
  "width": 6,
  "height": 4,
  "refresh_interval": 300
}
```

### Creating a KPI
```bash
POST /api/kpi-definitions
{
  "code": "REV-GROWTH",
  "name": "نمو الإيرادات",
  "name_en": "Revenue Growth",
  "category": "financial",
  "unit": "percentage",
  "target_value": 10.0,
  "warning_threshold": 5.0,
  "critical_threshold": 2.0,
  "frequency": "monthly"
}
```

### Calculating KPI Value
```bash
POST /api/kpi-values/calculate
{
  "kpi_definition_id": 1,
  "period_date": "2026-01-01",
  "actual_value": 12.5
}
```

### Getting Analytics
```bash
GET /api/analytics/financial-overview
GET /api/analytics/revenue-trend?months=6
GET /api/analytics/project-performance
```

## Security Features
- All endpoints protected by `auth:sanctum` middleware
- Company-scoped queries to ensure data isolation
- User authentication required for all operations
- Soft deletes for dashboards to preserve history

## Performance Considerations
- Widget refresh intervals to reduce server load
- Efficient eager loading of relationships
- Indexed foreign keys for faster queries
- Configurable pagination for large datasets

## Future Enhancements
- Real-time dashboard updates via WebSockets
- Export dashboard/analytics to PDF/Excel
- Dashboard templates and marketplace
- Advanced filtering and drill-down capabilities
- Custom calculation formulas for KPIs
- Dashboard sharing and collaboration features
- Mobile-optimized dashboard views
- Scheduled email reports

## Migration Commands
```bash
# Run migrations
php artisan migrate

# Fresh migration (with caution)
php artisan migrate:fresh

# Rollback specific migration
php artisan migrate:rollback --step=4
```

## Related Modules
- Projects Module - For project performance analytics
- Financial Module (AR/AP) - For financial analytics
- HR Module - For employee metrics
- Inventory Module - For stock analytics

## Support
For issues or questions, please refer to the main CEMS documentation or contact the development team.
