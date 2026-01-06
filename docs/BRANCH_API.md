# Branch Module API Documentation

## Overview
The Branch module provides RESTful API endpoints for managing company branches. This includes creating, reading, updating, and deleting branch records, as well as managing user assignments to branches.

## Authentication
All API endpoints require authentication using Laravel Sanctum. Include the API token in the Authorization header:

```
Authorization: Bearer {your-api-token}
```

## Base URL
```
/api/branches
```

## Endpoints

### 1. List All Branches
Get a paginated list of all branches with optional filtering.

**Endpoint:** `GET /api/branches`

**Query Parameters:**
- `company_id` (optional) - Filter branches by company ID
- `is_active` (optional) - Filter by active status (true/false)
- `search` (optional) - Search by name, code, or city
- `per_page` (optional) - Number of results per page (default: 15)

**Response:**
```json
{
  "success": true,
  "data": {
    "current_page": 1,
    "data": [
      {
        "id": 1,
        "company_id": 1,
        "code": "HQ-001",
        "name": "المقر الرئيسي",
        "name_en": "Headquarters",
        "region": "العاصمة",
        "city": "عمان",
        "country": "JO",
        "address": "شارع المدينة المنورة، عمان",
        "phone": "+962791234567",
        "email": "hq@example.com",
        "manager_id": 1,
        "is_active": true,
        "is_headquarters": true,
        "settings": null,
        "created_at": "2026-01-04T10:00:00.000000Z",
        "updated_at": "2026-01-04T10:00:00.000000Z",
        "deleted_at": null,
        "company": {
          "id": 1,
          "name": "شركة الأمثلة"
        },
        "manager": {
          "id": 1,
          "name": "John Doe"
        }
      }
    ],
    "per_page": 15,
    "total": 5
  }
}
```

### 2. Create a Branch
Create a new branch.

**Endpoint:** `POST /api/branches`

**Request Body:**
```json
{
  "company_id": 1,
  "code": "AMM-002",
  "name": "فرع عمان",
  "name_en": "Amman Branch",
  "region": "العاصمة",
  "city": "عمان",
  "country": "JO",
  "address": "شارع الجامعة",
  "phone": "+962791234568",
  "email": "amman@example.com",
  "manager_id": 2,
  "is_active": true,
  "is_headquarters": false
}
```

**Validation Rules:**
- `company_id`: required, must exist in companies table
- `code`: required, unique, max 50 characters
- `name`: required, max 255 characters
- `name_en`: optional, max 255 characters
- `region`: optional, max 100 characters
- `city`: optional, max 100 characters
- `country`: optional, max 2 characters
- `address`: optional, text
- `phone`: optional, max 20 characters
- `email`: optional, valid email, max 255 characters
- `manager_id`: optional, must exist in users table
- `is_active`: boolean
- `is_headquarters`: boolean

**Response:** (201 Created)
```json
{
  "success": true,
  "message": "Branch created successfully",
  "data": {
    "id": 2,
    "company_id": 1,
    "code": "AMM-002",
    "name": "فرع عمان",
    "name_en": "Amman Branch",
    "city": "عمان",
    "is_active": true,
    "created_at": "2026-01-04T10:00:00.000000Z",
    "company": {
      "id": 1,
      "name": "شركة الأمثلة"
    },
    "manager": {
      "id": 2,
      "name": "Jane Doe"
    }
  }
}
```

### 3. Get a Specific Branch
Retrieve details of a specific branch including related company, manager, and users.

**Endpoint:** `GET /api/branches/{id}`

**Response:**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "company_id": 1,
    "code": "HQ-001",
    "name": "المقر الرئيسي",
    "name_en": "Headquarters",
    "region": "العاصمة",
    "city": "عمان",
    "country": "JO",
    "address": "شارع المدينة المنورة، عمان",
    "phone": "+962791234567",
    "email": "hq@example.com",
    "manager_id": 1,
    "is_active": true,
    "is_headquarters": true,
    "created_at": "2026-01-04T10:00:00.000000Z",
    "company": {
      "id": 1,
      "name": "شركة الأمثلة"
    },
    "manager": {
      "id": 1,
      "name": "John Doe"
    },
    "users": [
      {
        "id": 3,
        "name": "Employee 1",
        "email": "employee1@example.com"
      }
    ]
  }
}
```

### 4. Update a Branch
Update an existing branch. All fields are optional (partial updates allowed).

**Endpoint:** `PUT /api/branches/{id}`

**Request Body:**
```json
{
  "name": "Updated Branch Name",
  "city": "إربد",
  "is_active": false
}
```

**Response:**
```json
{
  "success": true,
  "message": "Branch updated successfully",
  "data": {
    "id": 1,
    "name": "Updated Branch Name",
    "city": "إربد",
    "is_active": false,
    "updated_at": "2026-01-04T11:00:00.000000Z"
  }
}
```

### 5. Delete a Branch
Soft delete a branch.

**Endpoint:** `DELETE /api/branches/{id}`

**Response:**
```json
{
  "success": true,
  "message": "Branch deleted successfully"
}
```

### 6. Get Branch Users
Get a paginated list of users assigned to a specific branch.

**Endpoint:** `GET /api/branches/{id}/users`

**Response:**
```json
{
  "success": true,
  "data": {
    "current_page": 1,
    "data": [
      {
        "id": 3,
        "name": "Employee 1",
        "email": "employee1@example.com",
        "job_title": "Manager",
        "employee_id": "EMP001",
        "is_active": true,
        "company": {
          "id": 1,
          "name": "شركة الأمثلة"
        }
      }
    ],
    "per_page": 15,
    "total": 10
  }
}
```

## Error Responses

### Validation Error (422)
```json
{
  "message": "The code field is required. (and 1 more error)",
  "errors": {
    "code": ["The code field is required."],
    "name": ["The name field is required."]
  }
}
```

### Unauthorized (401)
```json
{
  "message": "Unauthenticated."
}
```

### Not Found (404)
```json
{
  "message": "Branch not found."
}
```

## Model Relationships

### Branch Model
- **Belongs To:** Company (company_id)
- **Belongs To:** User as Manager (manager_id)
- **Has Many:** Users (users assigned to the branch)

### Database Schema
```sql
branches:
  - id: bigint (primary key)
  - company_id: bigint (foreign key -> companies.id)
  - code: varchar(255) (unique)
  - name: varchar(255)
  - name_en: varchar(255) nullable
  - region: varchar(255) nullable
  - city: varchar(255) nullable
  - country: varchar(255) default 'JO'
  - address: text nullable
  - phone: varchar(255) nullable
  - email: varchar(255) nullable
  - manager_id: bigint nullable (foreign key -> users.id)
  - is_active: boolean default true
  - is_headquarters: boolean default false
  - settings: json nullable
  - created_at: timestamp
  - updated_at: timestamp
  - deleted_at: timestamp nullable (soft deletes)
```

## Usage Examples

### Example 1: Get all active branches for a company
```bash
curl -X GET "http://yourdomain.com/api/branches?company_id=1&is_active=1" \
  -H "Authorization: Bearer {token}" \
  -H "Accept: application/json"
```

### Example 2: Create a new branch
```bash
curl -X POST "http://yourdomain.com/api/branches" \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "company_id": 1,
    "code": "ZRQ-005",
    "name": "فرع الزرقاء",
    "city": "الزرقاء",
    "is_active": true
  }'
```

### Example 3: Update branch status
```bash
curl -X PUT "http://yourdomain.com/api/branches/1" \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "is_active": false
  }'
```

### Example 4: Get users assigned to a branch
```bash
curl -X GET "http://yourdomain.com/api/branches/1/users" \
  -H "Authorization: Bearer {token}" \
  -H "Accept: application/json"
```

## Notes
- All dates are in ISO 8601 format
- The API uses soft deletes, so deleted branches can be restored if needed
- Manager assignment is optional but recommended
- Branch code must be unique across all branches
- The `is_headquarters` flag indicates the main branch of a company
