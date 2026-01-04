# Dashboard & Analytics API

## Quick Reference

All endpoints require authentication using Laravel Sanctum.

### Base URL
```
/api
```

### Authentication
Include the CSRF token in all requests:
```javascript
headers: {
  'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
  'Accept': 'application/json'
}
```

## Endpoints

### 📊 GET /dashboard/executive
Get comprehensive executive dashboard data.

**Response:**
```json
{
  "success": true,
  "data": {
    "kpis": {
      "financial": {
        "monthly_revenue": 150000.00,
        "yearly_revenue": 1800000.00,
        "monthly_expenses": 90000.00,
        "yearly_expenses": 1200000.00,
        "monthly_profit": 60000.00,
        "yearly_profit": 600000.00,
        "profit_margin": 33.33,
        "cash_balance": 600000.00,
        "accounts_receivable": 50000.00,
        "accounts_payable": 30000.00
      },
      "project": {
        "active_projects": 5,
        "completed_projects": 12,
        "total_projects": 17,
        "average_progress": 65.50,
        "total_budget": 10000000.00,
        "total_actual_cost": 7500000.00,
        "budget_variance": 2500000.00,
        "budget_utilization": 75.00,
        "overall_spi": 1.05,
        "overall_cpi": 0.98
      },
      "operational": {
        "inventory_value": 250000.00,
        "inventory_items": 150,
        "pending_procurement": 8
      },
      "hr": {
        "employee_count": 45,
        "attendance_rate": 92.50,
        "monthly_payroll": 180000.00
      }
    },
    "timestamp": "2026-01-04T07:29:57Z"
  }
}
```

### 📁 GET /dashboard/project/{id}
Get detailed project dashboard data.

**Parameters:**
- `id` (path): Project ID

**Response:**
```json
{
  "success": true,
  "data": {
    "project": {
      "id": 1,
      "name": "مشروع برج الأعمال",
      "code": "PRJ-2024-001",
      "status": "active",
      "progress": 65,
      "client_name": "شركة التطوير العقاري",
      "location": "الرياض"
    },
    "spi": 1.07,
    "cpi": 1.07,
    "progress": 65,
    "budget": 5500000.00,
    "actual_cost": 3000000.00,
    "earned_value": 3200000.00,
    "planned_value": 3000000.00,
    "budget_remaining": 2500000.00,
    "project_revenue": 3500000.00,
    "project_expenses": 3000000.00,
    "project_profit": 500000.00
  }
}
```

### 💰 GET /dashboard/financial
Get financial dashboard data.

**Response:**
```json
{
  "success": true,
  "data": {
    "financial_kpis": {
      "monthly_revenue": 150000.00,
      "yearly_revenue": 1800000.00,
      "monthly_expenses": 90000.00,
      "yearly_expenses": 1200000.00,
      "monthly_profit": 60000.00,
      "yearly_profit": 600000.00,
      "profit_margin": 33.33,
      "cash_balance": 600000.00,
      "accounts_receivable": 50000.00,
      "accounts_payable": 30000.00
    },
    "timestamp": "2026-01-04T07:29:57Z"
  }
}
```

### 📈 GET /kpis
Get all KPIs in one request.

**Response:**
```json
{
  "success": true,
  "data": {
    "financial": { ... },
    "project": { ... },
    "operational": { ... },
    "hr": { ... }
  },
  "timestamp": "2026-01-04T07:29:57Z"
}
```

### 📊 GET /charts/{chart_type}
Get chart data for visualization.

**Parameters:**
- `chart_type` (path): Type of chart

**Available Types:**
- `revenue-trend`: 12-month revenue and expense trend (line chart)
- `project-status`: Project status distribution (pie chart)
- `project-budget`: Budget vs actual comparison (bar chart)
- `expense-breakdown`: Expense by category (pie chart)
- `revenue-by-project`: Revenue by project (bar chart)
- `cash-flow`: Cumulative cash flow (line chart)

**Response (Line Chart):**
```json
{
  "success": true,
  "data": {
    "labels": ["Jan 2025", "Feb 2025", "Mar 2025", ...],
    "datasets": [
      {
        "label": "Revenue",
        "data": [100000, 150000, 180000, ...],
        "borderColor": "rgb(0, 113, 227)",
        "backgroundColor": "rgba(0, 113, 227, 0.1)"
      },
      {
        "label": "Expenses",
        "data": [80000, 90000, 95000, ...],
        "borderColor": "rgb(255, 59, 48)",
        "backgroundColor": "rgba(255, 59, 48, 0.1)"
      }
    ]
  }
}
```

**Response (Pie Chart):**
```json
{
  "success": true,
  "data": {
    "labels": ["Active", "Completed", "On_hold", "Delayed"],
    "datasets": [
      {
        "data": [5, 12, 2, 1],
        "backgroundColor": [
          "rgb(52, 199, 89)",
          "rgb(0, 113, 227)",
          "rgb(255, 204, 0)",
          "rgb(255, 59, 48)"
        ]
      }
    ]
  }
}
```

### 💾 POST /dashboard/save-layout
Save custom dashboard layout configuration.

**Request Body:**
```json
{
  "dashboard_type": "executive",
  "layout_config": {
    "widgets": [
      {
        "id": 1,
        "type": "kpi",
        "position": "top-left",
        "size": "small"
      },
      {
        "id": 2,
        "type": "chart",
        "position": "center",
        "size": "large"
      }
    ]
  }
}
```

**Response:**
```json
{
  "success": true,
  "message": "Dashboard layout saved successfully",
  "data": {
    "id": 1,
    "user_id": 1,
    "dashboard_type": "executive",
    "layout_config": { ... },
    "created_at": "2026-01-04T07:29:57Z",
    "updated_at": "2026-01-04T07:29:57Z"
  }
}
```

## Error Responses

### 401 Unauthorized
```json
{
  "message": "Unauthenticated."
}
```

### 404 Not Found
```json
{
  "success": false,
  "message": "Project not found"
}
```

### 400 Bad Request
```json
{
  "success": false,
  "message": "Invalid chart type"
}
```

### 422 Validation Error
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "dashboard_type": ["The dashboard type field is required."],
    "layout_config": ["The layout config field is required."]
  }
}
```

## Usage Examples

### JavaScript (Fetch API)
```javascript
// Get executive dashboard
async function loadExecutiveDashboard() {
  const response = await fetch('/api/dashboard/executive', {
    headers: {
      'Accept': 'application/json',
      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
    }
  });
  const data = await response.json();
  console.log(data);
}

// Get project dashboard
async function loadProjectDashboard(projectId) {
  const response = await fetch(`/api/dashboard/project/${projectId}`, {
    headers: {
      'Accept': 'application/json',
      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
    }
  });
  const data = await response.json();
  console.log(data);
}

// Get chart data
async function loadChart(chartType) {
  const response = await fetch(`/api/charts/${chartType}`, {
    headers: {
      'Accept': 'application/json',
      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
    }
  });
  const data = await response.json();
  
  // Render with Chart.js
  const ctx = document.getElementById('myChart').getContext('2d');
  new Chart(ctx, {
    type: 'line',
    data: data.data,
    options: { responsive: true }
  });
}

// Save dashboard layout
async function saveLayout(layoutData) {
  const response = await fetch('/api/dashboard/save-layout', {
    method: 'POST',
    headers: {
      'Accept': 'application/json',
      'Content-Type': 'application/json',
      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
    },
    body: JSON.stringify(layoutData)
  });
  const data = await response.json();
  console.log(data);
}
```

### Axios
```javascript
// Configure axios defaults
axios.defaults.headers.common['X-CSRF-TOKEN'] = 
  document.querySelector('meta[name="csrf-token"]').content;

// Get KPIs
axios.get('/api/kpis')
  .then(response => {
    console.log(response.data);
  })
  .catch(error => {
    console.error(error);
  });

// Save layout
axios.post('/api/dashboard/save-layout', {
  dashboard_type: 'executive',
  layout_config: { /* config */ }
})
  .then(response => {
    console.log(response.data);
  })
  .catch(error => {
    console.error(error);
  });
```

## Rate Limiting

API endpoints are subject to Laravel's default rate limiting:
- 60 requests per minute for authenticated users
- Exceeding the limit returns a 429 Too Many Requests response

## Best Practices

1. **Cache responses**: Cache KPI data on the client side to reduce API calls
2. **Batch requests**: Use the `/api/kpis` endpoint to get all KPIs at once
3. **Error handling**: Always handle errors gracefully
4. **CSRF token**: Always include CSRF token in requests
5. **Loading states**: Show loading indicators while fetching data

## Support

For API issues or questions:
- Check the main documentation: `/docs/DASHBOARD_ANALYTICS.md`
- Review Laravel Sanctum documentation
- Contact the development team
