# CEMS API Documentation

## Authentication

All API requests require authentication using Laravel Sanctum tokens.

### Get Token
```http
POST /api/login
Content-Type: application/json

{
  "email": "user@example.com",
  "password": "password"
}
```

Response:
```json
{
  "success": true,
  "token": "1|abc123...",
  "user": {
    "id": 1,
    "name": "John Doe",
    "email": "user@example.com"
  }
}
```

### Use Token
```http
GET /api/projects
Authorization: Bearer 1|abc123...
```

## Projects

### List Projects
```http
GET /api/projects
```

Query Parameters:
- `status` (string): Filter by status
- `search` (string): Search in name/code
- `page` (int): Page number
- `per_page` (int): Items per page

Response:
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "code": "PROJ-001",
      "name": "New Construction Project",
      "budget": 1000000,
      "start_date": "2026-01-01",
      "end_date": "2026-12-31",
      "status": "active"
    }
  ],
  "meta": {
    "current_page": 1,
    "per_page": 15,
    "total": 50
  }
}
```

### Create Project
```http
POST /api/projects
Content-Type: application/json

{
  "code": "PROJ-001",
  "name": "New Construction Project",
  "client_id": 1,
  "budget": 1000000,
  "start_date": "2026-01-01",
  "end_date": "2026-12-31"
}
```

Response:
```json
{
  "success": true,
  "message": "Project created successfully",
  "data": {
    "id": 1,
    "code": "PROJ-001",
    "name": "New Construction Project"
  }
}
```

### Get Project
```http
GET /api/projects/{id}
```

### Update Project
```http
PUT /api/projects/{id}
Content-Type: application/json

{
  "name": "Updated Project Name",
  "budget": 1500000
}
```

### Delete Project
```http
DELETE /api/projects/{id}
```

## Contracts

### List Contracts
```http
GET /api/contracts
```

Query Parameters:
- `project_id` (int): Filter by project
- `status` (string): Filter by status
- `search` (string): Search in contract number/name

### Create Contract
```http
POST /api/contracts
Content-Type: application/json

{
  "contract_number": "CNT-001",
  "project_id": 1,
  "client_id": 1,
  "contract_value": 2000000,
  "start_date": "2026-01-01",
  "completion_date": "2026-12-31"
}
```

### Get Contract
```http
GET /api/contracts/{id}
```

### Update Contract
```http
PUT /api/contracts/{id}
```

### Delete Contract
```http
DELETE /api/contracts/{id}
```

## Purchase Orders

### List Purchase Orders
```http
GET /api/purchase-orders
```

Query Parameters:
- `project_id` (int): Filter by project
- `supplier_id` (int): Filter by supplier
- `status` (string): Filter by status

### Create Purchase Order
```http
POST /api/purchase-orders
Content-Type: application/json

{
  "po_number": "PO-001",
  "supplier_id": 1,
  "project_id": 1,
  "order_date": "2026-01-01",
  "items": [
    {
      "description": "Steel bars",
      "quantity": 100,
      "unit_price": 50,
      "amount": 5000
    }
  ]
}
```

### Get Purchase Order
```http
GET /api/purchase-orders/{id}
```

### Update Purchase Order
```http
PUT /api/purchase-orders/{id}
```

### Approve Purchase Order
```http
POST /api/purchase-orders/{id}/approve
```

### Delete Purchase Order
```http
DELETE /api/purchase-orders/{id}
```

## Change Orders

### List Change Orders
```http
GET /api/change-orders
```

Query Parameters:
- `project_id` (int): Filter by project
- `contract_id` (int): Filter by contract
- `status` (string): Filter by status

### Create Change Order
```http
POST /api/change-orders
Content-Type: application/json

{
  "co_number": "CO-001",
  "contract_id": 1,
  "description": "Additional scope of work",
  "net_amount": 50000,
  "tax_rate": 15
}
```

### Get Change Order
```http
GET /api/change-orders/{id}
```

### Update Change Order
```http
PUT /api/change-orders/{id}
```

### Approve Change Order
```http
POST /api/change-orders/{id}/approve
```

## IPCs (Interim Payment Certificates)

### List IPCs
```http
GET /api/ipcs
```

Query Parameters:
- `project_id` (int): Filter by project
- `contract_id` (int): Filter by contract
- `status` (string): Filter by status

### Create IPC
```http
POST /api/ipcs
Content-Type: application/json

{
  "ipc_number": "IPC-001",
  "contract_id": 1,
  "period_from": "2026-01-01",
  "period_to": "2026-01-31",
  "work_done_amount": 100000,
  "materials_on_site": 20000
}
```

### Get IPC
```http
GET /api/ipcs/{id}
```

### Update IPC
```http
PUT /api/ipcs/{id}
```

### Submit IPC
```http
POST /api/ipcs/{id}/submit
```

### Approve IPC
```http
POST /api/ipcs/{id}/approve
```

## Invoices

### List AR Invoices
```http
GET /api/ar-invoices
```

Query Parameters:
- `client_id` (int): Filter by client
- `status` (string): Filter by status
- `date_from` (date): Filter from date
- `date_to` (date): Filter to date

### Create AR Invoice
```http
POST /api/ar-invoices
Content-Type: application/json

{
  "invoice_number": "INV-001",
  "client_id": 1,
  "invoice_date": "2026-01-15",
  "due_date": "2026-02-15",
  "items": [
    {
      "description": "Construction services",
      "quantity": 1,
      "unit_price": 50000,
      "amount": 50000
    }
  ]
}
```

### List AP Invoices
```http
GET /api/ap-invoices
```

### Create AP Invoice
```http
POST /api/ap-invoices
Content-Type: application/json

{
  "invoice_number": "VINV-001",
  "supplier_id": 1,
  "invoice_date": "2026-01-15",
  "due_date": "2026-02-15",
  "items": [
    {
      "description": "Materials",
      "quantity": 100,
      "unit_price": 50,
      "amount": 5000
    }
  ]
}
```

## Users & Authentication

### Register User
```http
POST /api/register
Content-Type: application/json

{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "password",
  "password_confirmation": "password"
}
```

### Login
```http
POST /api/login
Content-Type: application/json

{
  "email": "john@example.com",
  "password": "password"
}
```

### Logout
```http
POST /api/logout
Authorization: Bearer {token}
```

### Get Current User
```http
GET /api/user
Authorization: Bearer {token}
```

### Update Profile
```http
PUT /api/profile
Authorization: Bearer {token}
Content-Type: application/json

{
  "name": "John Updated",
  "email": "john.updated@example.com"
}
```

## Reports

### Project Cost Report
```http
GET /api/reports/project-cost/{project_id}
```

### Financial Summary
```http
GET /api/reports/financial-summary
```

Query Parameters:
- `date_from` (date): Start date
- `date_to` (date): End date

### Purchase Order Report
```http
GET /api/reports/purchase-orders
```

Query Parameters:
- `date_from` (date): Start date
- `date_to` (date): End date
- `project_id` (int): Filter by project

---

## Error Responses

All errors return consistent format:

```json
{
  "success": false,
  "message": "Error description",
  "errors": {
    "field": ["Validation error message"]
  }
}
```

HTTP Status Codes:
- 200: Success
- 201: Created
- 400: Bad Request
- 401: Unauthorized
- 403: Forbidden
- 404: Not Found
- 422: Validation Error
- 500: Server Error

## Pagination

List endpoints support pagination:

```json
{
  "data": [...],
  "meta": {
    "current_page": 1,
    "per_page": 15,
    "total": 100,
    "last_page": 7
  },
  "links": {
    "first": "http://api.example.com/api/projects?page=1",
    "last": "http://api.example.com/api/projects?page=7",
    "prev": null,
    "next": "http://api.example.com/api/projects?page=2"
  }
}
```

## Rate Limiting

API requests are rate limited to:
- 60 requests per minute for authenticated users
- 10 requests per minute for unauthenticated users

Rate limit headers:
```
X-RateLimit-Limit: 60
X-RateLimit-Remaining: 59
X-RateLimit-Reset: 1704916800
```
