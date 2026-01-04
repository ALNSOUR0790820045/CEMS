# Purchase Orders Module Documentation

## Overview
The Purchase Orders module provides complete procurement management functionality with multi-currency support, approval workflows, and receiving tracking.

## Features

### 1. Purchase Order Management
- Create, read, update, and delete purchase orders
- Multi-item purchase orders with line-level calculations
- Auto-generated PO numbers in format: `PO-YYYY-XXXX`
- Multi-currency support with exchange rates
- Project linking for cost tracking

### 2. Approval Workflow
Purchase orders follow this workflow:
```
draft → submitted → approved → sent_to_vendor → acknowledged → partially_received → fully_received
                                                                                      ↓
                                                                                  cancelled
```

**Status Transitions:**
- **draft**: Initial state, can be edited or deleted
- **submitted**: Sent for approval, can be approved or rejected
- **approved**: Approved by authorized user, can be sent to vendor
- **sent_to_vendor**: Sent via email to vendor
- **acknowledged**: Vendor confirmed receipt
- **partially_received**: Some items received (tracked via GRN)
- **fully_received**: All items received
- **cancelled**: Order cancelled at any stage

### 3. Calculations
The module automatically calculates:
- **Line Total** = (Quantity × Unit Price) - Discount + Tax
- **Subtotal** = Sum of all line totals
- **Total Amount** = Subtotal + Tax Amount - Discount Amount

**Formula:**
```
Base Amount = Quantity × Unit Price
Discount Amount = Base Amount × (Discount Rate / 100)
After Discount = Base Amount - Discount Amount
Tax Amount = After Discount × (Tax Rate / 100)
Line Total = After Discount + Tax Amount
```

### 4. Receiving Tracking
- Track received quantities per item
- Calculate remaining quantities automatically
- View receiving status and percentage
- Support for partial and over/under delivery

## Database Schema

### Tables Created
1. **currencies** - Currency definitions (USD, EUR, GBP, etc.)
2. **units** - Units of measurement (PC, KG, M, etc.)
3. **vendors** - Vendor information
4. **materials** - Material/product catalog
5. **projects** - Project definitions
6. **purchase_requisitions** - Purchase requisitions (can be converted to POs)
7. **purchase_orders** - Main PO table
8. **purchase_order_items** - PO line items

### Key Relationships
- PurchaseOrder belongs to: Vendor, Currency, Company, Project, PurchaseRequisition, CreatedBy (User), ApprovedBy (User)
- PurchaseOrder has many: PurchaseOrderItems
- PurchaseOrderItem belongs to: PurchaseOrder, Material, Unit

## API Endpoints

### List Purchase Orders
```http
GET /api/purchase-orders
```

**Query Parameters:**
- `status` - Filter by status (draft, submitted, approved, etc.)
- `vendor_id` - Filter by vendor
- `project_id` - Filter by project
- `per_page` - Results per page (default: 15)

**Response:**
```json
{
  "data": [
    {
      "id": 1,
      "po_number": "PO-2026-0001",
      "po_date": "2026-01-04",
      "vendor": { "id": 1, "name": "ABC Supplies Co." },
      "status": "draft",
      "total_amount": "5775.00",
      ...
    }
  ],
  "links": { ... },
  "meta": { ... }
}
```

### Create Purchase Order
```http
POST /api/purchase-orders
```

**Request Body:**
```json
{
  "po_date": "2026-01-04",
  "vendor_id": 1,
  "delivery_date": "2026-01-18",
  "currency_id": 1,
  "company_id": 1,
  "project_id": 1,
  "payment_terms": "Net 30",
  "items": [
    {
      "material_id": 1,
      "quantity": 10,
      "unit_id": 1,
      "unit_price": 100.00,
      "tax_rate": 10,
      "discount_rate": 5
    }
  ]
}
```

**Response:**
```json
{
  "message": "Purchase order created successfully",
  "data": {
    "id": 1,
    "po_number": "PO-2026-0001",
    "status": "draft",
    ...
  }
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

**Note:** Only draft and submitted orders can be updated.

### Delete Purchase Order
```http
DELETE /api/purchase-orders/{id}
```

**Note:** Only draft orders can be deleted.

### Approve Purchase Order
```http
POST /api/purchase-orders/{id}/approve
```

**Requirements:**
- Order must be in "submitted" status
- User must be authenticated

**Response:**
```json
{
  "message": "Purchase order approved successfully",
  "data": {
    "id": 1,
    "status": "approved",
    "approved_by_id": 1,
    "approved_at": "2026-01-04T10:30:00.000000Z"
  }
}
```

### Send to Vendor
```http
POST /api/purchase-orders/{id}/send-to-vendor
```

**Requirements:**
- Order must be in "approved" status

### Amend Purchase Order
```http
POST /api/purchase-orders/{id}/amend
```

**Purpose:** Allows modification of approved orders by reverting to "submitted" status for re-approval.

**Requirements:**
- Order must be in approved, sent_to_vendor, acknowledged, or partially_received status
- Cannot amend fully_received or cancelled orders

### Get Receiving Status
```http
GET /api/purchase-orders/{id}/receiving-status
```

**Response:**
```json
{
  "purchase_order_id": 1,
  "po_number": "PO-2026-0001",
  "status": "partially_received",
  "total_quantity": 100,
  "received_quantity": 50,
  "remaining_quantity": 50,
  "receiving_percentage": 50.00,
  "items": [
    {
      "id": 1,
      "material": "Steel Beam - 6m",
      "quantity": 100,
      "received_quantity": 50,
      "remaining_quantity": 50,
      "unit": "PC"
    }
  ]
}
```

## Authentication

All API endpoints require authentication using Laravel Sanctum:

```http
Authorization: Bearer {token}
```

## Usage Examples

### Creating a Purchase Order with Multiple Items

```javascript
const response = await fetch('/api/purchase-orders', {
  method: 'POST',
  headers: {
    'Authorization': 'Bearer ' + token,
    'Content-Type': 'application/json',
  },
  body: JSON.stringify({
    po_date: '2026-01-04',
    vendor_id: 1,
    delivery_date: '2026-01-18',
    delivery_location: 'Main Warehouse',
    payment_terms: 'Net 30',
    currency_id: 1,
    exchange_rate: 1.0000,
    company_id: 1,
    project_id: 1,
    notes: 'Urgent order for project',
    items: [
      {
        material_id: 1,
        quantity: 20,
        unit_id: 1,
        unit_price: 250.00,
        tax_rate: 10,
        discount_rate: 5,
        specifications: 'Grade A steel'
      },
      {
        material_id: 2,
        quantity: 100,
        unit_id: 2,
        unit_price: 8.50,
        tax_rate: 10,
        discount_rate: 0
      }
    ]
  })
});
```

### Approval Workflow Example

```javascript
// 1. Create draft PO
const po = await createPurchaseOrder(poData);

// 2. Submit for approval (update status)
await fetch(`/api/purchase-orders/${po.id}`, {
  method: 'PUT',
  body: JSON.stringify({ status: 'submitted' })
});

// 3. Approve the PO
await fetch(`/api/purchase-orders/${po.id}/approve`, {
  method: 'POST'
});

// 4. Send to vendor
await fetch(`/api/purchase-orders/${po.id}/send-to-vendor`, {
  method: 'POST'
});
```

## Testing

Run the test suite:

```bash
php artisan test --filter=PurchaseOrderTest
```

All 8 tests should pass:
- ✓ can create purchase order
- ✓ can list purchase orders
- ✓ can show purchase order
- ✓ can approve purchase order
- ✓ calculates totals correctly
- ✓ po number is auto generated
- ✓ cannot delete non draft purchase order
- ✓ can get receiving status

## Sample Data

Run the seeder to populate sample data:

```bash
php artisan db:seed --class=PurchaseOrderSeeder
```

This creates:
- 3 currencies (USD, EUR, GBP)
- 5 units (PC, KG, M, L, BOX)
- 2 vendors
- 2 projects
- 3 materials
- 3 sample purchase orders in different statuses

## Models

### PurchaseOrder

**Key Methods:**
- `calculateTotals()` - Recalculates subtotal and total amount
- `generatePoNumber()` - Generates next PO number
- `canBeApproved()` - Checks if PO can be approved
- `approve(User $user)` - Approves the PO

**Computed Fields:**
- `subtotal` - Sum of all item line totals
- `total_amount` - Subtotal + tax - discount

### PurchaseOrderItem

**Key Methods:**
- `calculateLineTotal()` - Calculates the line total with tax and discount

**Automatic Calculations:**
- Line total calculated on save
- Remaining quantity = quantity - received_quantity
- Parent PO totals updated automatically

## Future Enhancements

1. **Version Control** - Track PO amendments with full history
2. **Email Notifications** - Send PO to vendor via email
3. **Budget Validation** - Check against project/department budgets
4. **Multi-level Approval** - Support approval hierarchies
5. **GRN Integration** - Link to Goods Receipt Notes for receiving
6. **PDF Generation** - Generate printable PO documents
7. **Vendor Portal** - Allow vendors to acknowledge and update orders
8. **Analytics** - Dashboard with PO metrics and KPIs

## Support

For issues or questions, please refer to the main CEMS documentation or contact the development team.
