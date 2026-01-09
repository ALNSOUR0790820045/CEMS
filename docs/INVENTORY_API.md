# Inventory Management Module - API Documentation

## Overview
This document describes the API endpoints for the Inventory Management Module in the CEMS system.

## Authentication
All API endpoints require authentication using Laravel Sanctum. Include the bearer token in the Authorization header:
```
Authorization: Bearer {token}
```

## Base URL
```
/api
```

---

## Inventory Management

### Get Inventory Balance
Retrieve inventory balances with optional filtering.

**Endpoint:** `GET /api/inventory/balance`

**Query Parameters:**
- `warehouse_id` (optional): Filter by warehouse
- `material_id` (optional): Filter by material
- `low_stock` (optional): Filter items at or below reorder level
- `per_page` (optional): Results per page (default: 15)

**Response:**
```json
{
  "success": true,
  "data": {
    "current_page": 1,
    "data": [
      {
        "id": 1,
        "material_id": 1,
        "warehouse_id": 1,
        "quantity_on_hand": 100.00,
        "quantity_reserved": 10.00,
        "quantity_available": 90.00,
        "last_cost": 10.00,
        "average_cost": 10.50,
        "total_value": 1050.00,
        "last_transaction_date": "2026-01-03",
        "material": {
          "id": 1,
          "code": "MAT001",
          "name": "Material Name"
        },
        "warehouse": {
          "id": 1,
          "code": "WH001",
          "name": "Warehouse Name"
        }
      }
    ],
    "total": 1
  }
}
```

### Get Transaction History
Retrieve inventory transaction history with filtering.

**Endpoint:** `GET /api/inventory/transactions`

**Query Parameters:**
- `material_id` (optional): Filter by material
- `warehouse_id` (optional): Filter by warehouse
- `transaction_type` (optional): Filter by type (receipt, issue, transfer, adjustment, return)
- `date_from` (optional): Start date
- `date_to` (optional): End date
- `per_page` (optional): Results per page (default: 15)

**Response:**
```json
{
  "success": true,
  "data": {
    "current_page": 1,
    "data": [
      {
        "id": 1,
        "transaction_number": "INV-2026-0001",
        "transaction_date": "2026-01-03",
        "transaction_type": "receipt",
        "material_id": 1,
        "warehouse_id": 1,
        "quantity": 100.00,
        "unit_cost": 10.00,
        "total_value": 1000.00,
        "notes": "Initial stock",
        "material": {
          "id": 1,
          "code": "MAT001",
          "name": "Material Name"
        },
        "warehouse": {
          "id": 1,
          "code": "WH001",
          "name": "Warehouse Name"
        },
        "created_by": {
          "id": 1,
          "name": "User Name"
        }
      }
    ],
    "total": 1
  }
}
```

### Create Inventory Transaction
Record a new inventory transaction.

**Endpoint:** `POST /api/inventory/transactions`

**Request Body:**
```json
{
  "transaction_type": "receipt",
  "transaction_date": "2026-01-03",
  "material_id": 1,
  "warehouse_id": 1,
  "quantity": 100.00,
  "unit_cost": 10.00,
  "project_id": 1,
  "reference_type": "purchase_order",
  "reference_id": 123,
  "notes": "Initial stock receipt"
}
```

**Validation Rules:**
- `transaction_type`: required, one of: receipt, issue, adjustment, return
- `transaction_date`: required, valid date
- `material_id`: required, exists in materials table
- `warehouse_id`: required, exists in warehouses table
- `quantity`: required, numeric, min: 0.01
- `unit_cost`: required, numeric, min: 0
- `project_id`: optional, exists in projects table
- `reference_type`: optional, string
- `reference_id`: optional, integer
- `notes`: optional, string

**Response:**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "transaction_number": "INV-2026-0001",
    "transaction_date": "2026-01-03",
    "transaction_type": "receipt",
    "quantity": 100.00,
    "unit_cost": 10.00,
    "total_value": 1000.00,
    "material": {...},
    "warehouse": {...}
  },
  "message": "Transaction recorded successfully"
}
```

---

## Stock Transfers

### List Stock Transfers
Get all stock transfers with optional filtering.

**Endpoint:** `GET /api/stock-transfers`

**Query Parameters:**
- `status` (optional): Filter by status (pending, approved, in_transit, completed, cancelled)
- `from_warehouse_id` (optional): Filter by source warehouse
- `to_warehouse_id` (optional): Filter by destination warehouse
- `date_from` (optional): Start date
- `date_to` (optional): End date
- `per_page` (optional): Results per page (default: 15)

**Response:**
```json
{
  "success": true,
  "data": {
    "current_page": 1,
    "data": [
      {
        "id": 1,
        "transfer_number": "STR-2026-0001",
        "transfer_date": "2026-01-03",
        "from_warehouse_id": 1,
        "to_warehouse_id": 2,
        "status": "completed",
        "items": [
          {
            "id": 1,
            "material_id": 1,
            "requested_quantity": 50.00,
            "transferred_quantity": 50.00,
            "received_quantity": 50.00,
            "unit_cost": 10.00,
            "material": {
              "id": 1,
              "code": "MAT001",
              "name": "Material Name"
            }
          }
        ],
        "from_warehouse": {...},
        "to_warehouse": {...},
        "created_by": {...},
        "approved_by": {...},
        "received_by": {...}
      }
    ],
    "total": 1
  }
}
```

### Create Stock Transfer
Create a new stock transfer request.

**Endpoint:** `POST /api/stock-transfers`

**Request Body:**
```json
{
  "transfer_date": "2026-01-03",
  "from_warehouse_id": 1,
  "to_warehouse_id": 2,
  "notes": "Transfer notes",
  "items": [
    {
      "material_id": 1,
      "requested_quantity": 50.00,
      "unit_cost": 10.00,
      "notes": "Item notes"
    }
  ]
}
```

**Validation Rules:**
- `transfer_date`: required, date
- `from_warehouse_id`: required, exists in warehouses
- `to_warehouse_id`: required, exists in warehouses, different from from_warehouse_id
- `notes`: optional, string
- `items`: required, array, min: 1
- `items.*.material_id`: required, exists in materials
- `items.*.requested_quantity`: required, numeric, min: 0.01
- `items.*.unit_cost`: required, numeric, min: 0
- `items.*.notes`: optional, string

**Response:**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "transfer_number": "STR-2026-0001",
    "status": "pending",
    "items": [...]
  },
  "message": "Stock transfer created successfully"
}
```

### Get Stock Transfer
Get details of a specific stock transfer.

**Endpoint:** `GET /api/stock-transfers/{id}`

**Response:**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "transfer_number": "STR-2026-0001",
    "transfer_date": "2026-01-03",
    "status": "pending",
    "items": [...],
    "from_warehouse": {...},
    "to_warehouse": {...}
  }
}
```

### Approve Stock Transfer
Approve a pending stock transfer.

**Endpoint:** `POST /api/stock-transfers/{id}/approve`

**Response:**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "status": "approved",
    "approved_by_id": 1
  },
  "message": "Stock transfer approved successfully"
}
```

**Error Response:**
```json
{
  "success": false,
  "message": "Only pending transfers can be approved"
}
```

### Receive Stock Transfer
Mark a stock transfer as received and update inventory.

**Endpoint:** `POST /api/stock-transfers/{id}/receive`

**Request Body (optional):**
```json
{
  "received_quantities": {
    "1": 45.00,
    "2": 30.00
  }
}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "status": "completed",
    "received_by_id": 1
  },
  "message": "Stock transfer received successfully"
}
```

### Cancel Stock Transfer
Cancel a stock transfer.

**Endpoint:** `POST /api/stock-transfers/{id}/cancel`

**Request Body:**
```json
{
  "reason": "Cancellation reason"
}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "status": "cancelled"
  },
  "message": "Stock transfer cancelled successfully"
}
```

---

## Inventory Reports

### Valuation Report
Get inventory valuation report.

**Endpoint:** `GET /api/inventory/reports/valuation`

**Query Parameters:**
- `warehouse_id` (optional): Filter by warehouse

**Response:**
```json
{
  "success": true,
  "data": {
    "items": [
      {
        "material": {...},
        "warehouse": {...},
        "quantity_on_hand": 100.00,
        "average_cost": 10.50,
        "total_value": 1050.00
      }
    ],
    "total_value": 1050.00,
    "total_quantity": 100.00
  }
}
```

### Stock Status Report
Get current stock status.

**Endpoint:** `GET /api/inventory/reports/stock-status`

**Query Parameters:**
- `warehouse_id` (optional): Filter by warehouse
- `per_page` (optional): Results per page (default: 100)

### Movement Report
Get inventory movement history.

**Endpoint:** `GET /api/inventory/reports/movement`

**Query Parameters:**
- `material_id` (optional): Filter by material
- `warehouse_id` (optional): Filter by warehouse
- `date_from` (optional): Start date
- `date_to` (optional): End date
- `per_page` (optional): Results per page (default: 50)

### Low Stock Alert Report
Get items at or below reorder level.

**Endpoint:** `GET /api/inventory/reports/low-stock`

**Query Parameters:**
- `warehouse_id` (optional): Filter by warehouse
- `per_page` (optional): Results per page (default: 50)

---

## Error Handling

All endpoints return errors in the following format:

```json
{
  "success": false,
  "message": "Error message description"
}
```

Common HTTP status codes:
- `200`: Success
- `201`: Created
- `400`: Bad Request (validation errors, business logic errors)
- `404`: Not Found
- `500`: Internal Server Error

## Transaction Types

### Receipt
Used for receiving materials into inventory:
- From suppliers (GRN)
- Returns from projects
- Positive adjustments

### Issue
Used for issuing materials out of inventory:
- To projects
- To production
- Negative adjustments

### Transfer
Handled through the Stock Transfer workflow:
1. Create transfer (status: pending)
2. Approve transfer (status: approved)
3. Receive transfer (status: completed, updates both warehouses)

### Adjustment
Used for physical count adjustments:
- Can be positive or negative
- Used to correct inventory discrepancies

### Return
Used for returning materials:
- Similar to receipts
- Can reference original issue transaction

## Valuation Methods

The system uses **Average Cost** method for inventory valuation:
- Each receipt updates the average cost
- Formula: `(old_value + new_value) / (old_qty + new_qty)`
- Issues do not change the average cost
- All warehouse-material combinations maintain their own average cost

## Business Rules

1. **Sufficient Stock**: Cannot issue more than available quantity
2. **Transfer Validation**: Cannot transfer between same warehouse
3. **Transfer Approval**: Only pending transfers can be approved
4. **Transfer Completion**: Only approved/in-transit transfers can be received
5. **Transfer Cancellation**: Cannot cancel completed transfers
6. **Auto-numbering**: Transaction and transfer numbers are auto-generated
7. **Company Isolation**: All operations are scoped to the authenticated user's company
