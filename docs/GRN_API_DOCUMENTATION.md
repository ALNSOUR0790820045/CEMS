# GRN (Goods Receipt Notes) Module - API Documentation

## Overview
The GRN module provides complete warehouse receiving functionality with purchase order matching, quality inspection, and automatic inventory updates.

## Database Schema

### Tables Created
- `vendors` - Vendor/supplier information
- `warehouses` - Warehouse locations
- `materials` - Material/product catalog
- `projects` - Project tracking
- `purchase_orders` - Purchase order headers
- `purchase_order_items` - Purchase order line items
- `grns` - Goods receipt note headers
- `grn_items` - Goods receipt note line items
- `inventory_transactions` - Inventory movement tracking

## API Endpoints

### List GRNs
```
GET /api/grns
```
Query Parameters:
- `status` (optional) - Filter by status (draft, received, inspected, accepted, rejected, partial)
- `vendor_id` (optional) - Filter by vendor
- `warehouse_id` (optional) - Filter by warehouse
- `per_page` (optional) - Results per page (default: 15)

### Create GRN
```
POST /api/grns
```
Request Body:
```json
{
  "grn_date": "2026-01-03",
  "purchase_order_id": 1,
  "vendor_id": 1,
  "warehouse_id": 1,
  "project_id": 1,
  "delivery_note_number": "DN001",
  "vehicle_number": "ABC123",
  "driver_name": "John Doe",
  "notes": "Notes here",
  "items": [
    {
      "material_id": 1,
      "purchase_order_item_id": 1,
      "ordered_quantity": 100,
      "received_quantity": 95,
      "unit_price": 10.50,
      "batch_number": "BATCH001",
      "expiry_date": "2027-01-03",
      "notes": "Item notes"
    }
  ]
}
```

### Show GRN
```
GET /api/grns/{id}
```

### Update GRN
```
PATCH /api/grns/{id}
```
Request Body:
```json
{
  "grn_date": "2026-01-03",
  "delivery_note_number": "DN002",
  "vehicle_number": "XYZ789",
  "driver_name": "Jane Smith",
  "notes": "Updated notes"
}
```
Note: Can only update GRNs in 'draft' or 'received' status.

### Delete GRN
```
DELETE /api/grns/{id}
```
Note: Can only delete GRNs in 'draft' status.

### Inspect GRN
```
POST /api/grns/{id}/inspect
```
Request Body:
```json
{
  "inspection_notes": "Quality inspection completed",
  "items": [
    {
      "grn_item_id": 1,
      "accepted_quantity": 90,
      "rejected_quantity": 5,
      "inspection_status": "partial",
      "rejection_reason": "Damaged items"
    }
  ]
}
```
Note: GRN must be in 'received' status for inspection.

### Accept GRN
```
POST /api/grns/{id}/accept
```
Note: GRN must be in 'inspected' status. This endpoint creates inventory transactions for all accepted items.

### Get Pending Inspections
```
GET /api/grns/pending-inspection
```
Returns all GRNs in 'received' status waiting for quality inspection.

## GRN Status Workflow

1. **draft** - Initial creation, editable
2. **received** - Items received, ready for inspection
3. **inspected** - Quality inspection completed
4. **accepted** - All items accepted, inventory updated
5. **rejected** - All items rejected
6. **partial** - Some items accepted, some rejected

## Features

### Auto-Generation
- GRN numbers automatically generated in format: `GRN-YYYY-XXXX`
- Example: `GRN-2026-0001`

### Authorization
- All endpoints are protected by `auth:sanctum` middleware
- Users can only access GRNs belonging to their company
- Company-level data isolation enforced

### Inventory Integration
- Automatic inventory transaction creation upon GRN acceptance
- Tracks material, warehouse, quantity, price, batch, and expiry date
- Transaction type: 'in' for receipts

### Three-Way Matching
- Link GRN to Purchase Order for matching
- Track ordered vs received quantities
- Variance reporting capability

## Testing

Run tests with:
```bash
php artisan test --filter=GRNTest
```

Tests cover:
- GRN creation
- Listing and filtering
- Show single GRN
- Quality inspection
- Acceptance and inventory updates
- Pending inspection list
- Authorization checks

## Models and Relationships

### GRN
- belongsTo: Vendor, Warehouse, Project, PurchaseOrder, Company, receivedBy (User), inspectedBy (User)
- hasMany: GRNItems

### GRNItem
- belongsTo: GRN, Material, PurchaseOrderItem

### InventoryTransaction
- belongsTo: Material, Warehouse, Company, creator (User)

## Security

- Company-level authorization on all endpoints
- Proper error handling without exposing internal details
- Logging of errors for debugging
- Input validation via Form Requests
- SQL injection prevention via Eloquent ORM
- XSS prevention via JSON responses
