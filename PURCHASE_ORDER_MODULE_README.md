# Purchase Order Module (نظام أوامر الشراء)

## Overview
Complete Purchase Order management system with receipt tracking, amendments, and comprehensive reporting capabilities.

## Database Tables

### 1. purchase_orders - أوامر الشراء
Main purchase order table with all financial and delivery information.

**Fields:**
- `id` - Primary key
- `po_number` - Auto-generated (PO-YYYY-XXXX)
- `po_date` - Purchase order date
- `vendor_id` - Foreign key to vendors
- `purchase_requisition_id` - Optional FK to purchase requisitions
- `project_id` - Optional FK to projects
- `delivery_address` - Delivery location
- `delivery_date` - Expected delivery date
- `payment_terms_id` - Payment terms reference
- `currency_id` - Foreign key to currencies
- `exchange_rate` - Exchange rate (default 1.0000)
- `subtotal` - Subtotal before discounts and taxes
- `discount_amount` - Total discount amount
- `tax_amount` - Total tax amount
- `total_amount` - Final total amount
- `status` - Order status (draft, pending_approval, approved, sent, partially_received, received, cancelled, closed)
- `approved_by_id` - User who approved
- `approved_at` - Approval timestamp
- `sent_at` - Sent to vendor timestamp
- `notes` - Internal notes
- `terms_and_conditions` - Terms and conditions
- `company_id` - Multi-tenancy support
- `created_by` - User who created
- `timestamps` - Created/updated timestamps
- `soft_deletes` - Soft delete support

### 2. purchase_order_items - بنود أمر الشراء
Line items for purchase orders.

**Fields:**
- `id` - Primary key
- `purchase_order_id` - Foreign key to purchase orders
- `material_id` - Optional FK to materials
- `description` - Item description (required)
- `specifications` - Technical specifications
- `quantity` - Ordered quantity
- `unit_id` - Foreign key to units
- `unit_price` - Price per unit
- `discount_percentage` - Discount percentage
- `discount_amount` - Calculated discount amount
- `tax_percentage` - Tax percentage
- `tax_amount` - Calculated tax amount
- `total_price` - Final line total
- `quantity_received` - Total quantity received
- `quantity_invoiced` - Total quantity invoiced
- `delivery_date` - Expected delivery date
- `notes` - Item-specific notes
- `timestamps` - Created/updated timestamps

### 3. po_receipts - استلامات أوامر الشراء
Goods receipt notes for purchase orders.

**Fields:**
- `id` - Primary key
- `receipt_number` - Auto-generated (RCV-YYYY-XXXX)
- `purchase_order_id` - Foreign key to purchase orders
- `receipt_date` - Date of receipt
- `received_by_id` - User who received
- `warehouse_id` - Foreign key to warehouses
- `delivery_note_number` - Vendor delivery note reference
- `status` - Receipt status (pending_inspection, inspected, accepted, rejected, partial)
- `notes` - Receipt notes
- `company_id` - Multi-tenancy support
- `timestamps` - Created/updated timestamps

### 4. po_receipt_items - بنود الاستلام
Line items for receipts with inspection details.

**Fields:**
- `id` - Primary key
- `po_receipt_id` - Foreign key to receipts
- `po_item_id` - Foreign key to PO items
- `quantity_received` - Quantity received
- `quantity_accepted` - Quantity accepted after inspection
- `quantity_rejected` - Quantity rejected
- `rejection_reason` - Reason for rejection
- `inspection_notes` - Quality inspection notes
- `batch_number` - Batch/lot number
- `expiry_date` - Expiration date
- `timestamps` - Created/updated timestamps

### 5. po_amendments - تعديلات أوامر الشراء
Amendment tracking for purchase orders.

**Fields:**
- `id` - Primary key
- `amendment_number` - Auto-generated (AMD-YYYY-XXXX)
- `purchase_order_id` - Foreign key to purchase orders
- `amendment_date` - Date of amendment
- `amendment_type` - Type (quantity, price, delivery_date, cancel_item, add_item)
- `description` - Amendment description
- `old_value` - Previous value
- `new_value` - New value
- `reason` - Reason for amendment
- `status` - Amendment status (pending, approved, rejected)
- `requested_by_id` - User who requested
- `approved_by_id` - User who approved
- `approved_at` - Approval timestamp
- `timestamps` - Created/updated timestamps

## Models

### 1. PurchaseOrder
Main purchase order model with relationships to:
- `vendor` - Vendor relationship
- `project` - Project relationship (optional)
- `purchaseRequisition` - Purchase requisition relationship (optional)
- `currency` - Currency relationship
- `creator` - Created by user
- `approvedBy` - Approved by user
- `items` - PO items (hasMany)
- `receipts` - Receipts (hasMany)
- `amendments` - Amendments (hasMany)
- `grns` - GRNs (hasMany)
- `apInvoices` - AP Invoices (hasMany)

### 2. PurchaseOrderItem
Line item model with relationships to:
- `purchaseOrder` - Parent PO
- `material` - Material reference
- `unit` - Unit of measure
- `receiptItems` - Receipt items (hasMany)
- `grnItems` - GRN items (hasMany)

### 3. PoReceipt
Receipt model with relationships to:
- `purchaseOrder` - Parent PO
- `receivedBy` - User who received
- `warehouse` - Warehouse location
- `company` - Company reference
- `items` - Receipt items (hasMany)

### 4. PoReceiptItem
Receipt line item with relationships to:
- `receipt` - Parent receipt
- `purchaseOrderItem` - Original PO item

### 5. PoAmendment
Amendment model with relationships to:
- `purchaseOrder` - Parent PO
- `requestedBy` - User who requested
- `approvedBy` - User who approved

## Controllers

### 1. PurchaseOrderController
**Endpoints:**
- `GET /api/purchase-orders` - List all purchase orders with filters
- `POST /api/purchase-orders` - Create new purchase order
- `GET /api/purchase-orders/{id}` - Get purchase order details
- `PUT /api/purchase-orders/{id}` - Update purchase order
- `DELETE /api/purchase-orders/{id}` - Delete purchase order (draft only)
- `POST /api/purchase-orders/{id}/approve` - Approve purchase order
- `POST /api/purchase-orders/{id}/reject` - Reject purchase order
- `POST /api/purchase-orders/{id}/send` - Send to vendor
- `POST /api/purchase-orders/{id}/cancel` - Cancel purchase order
- `POST /api/purchase-orders/{id}/close` - Close purchase order
- `GET /api/purchase-orders/{id}/print` - Print purchase order
- `GET /api/purchase-orders/{id}/pdf` - Generate PDF
- `POST /api/purchase-orders/create-from-pr/{prId}` - Create from PR

**Features:**
- Automatic PO number generation (PO-YYYY-XXXX)
- Automatic calculation of totals, discounts, and taxes
- Status workflow management
- Multi-item support
- Comprehensive validation

### 2. PoReceiptController
**Endpoints:**
- `GET /api/po-receipts` - List all receipts with filters
- `POST /api/po-receipts` - Create new receipt
- `GET /api/po-receipts/{id}` - Get receipt details
- `PUT /api/po-receipts/{id}` - Update receipt
- `DELETE /api/po-receipts/{id}` - Delete receipt
- `POST /api/po-receipts/{id}/inspect` - Perform inspection
- `POST /api/po-receipts/{id}/accept` - Accept receipt

**Features:**
- Automatic receipt number generation (RCV-YYYY-XXXX)
- Partial receipt support
- Quantity validation (cannot exceed ordered quantity)
- Automatic PO status updates (partially_received, received)
- Quality inspection workflow
- Batch and expiry tracking

### 3. PoAmendmentController
**Endpoints:**
- `GET /api/po-amendments` - List all amendments
- `POST /api/po-amendments` - Create new amendment
- `GET /api/po-amendments/{id}` - Get amendment details
- `PUT /api/po-amendments/{id}` - Update amendment
- `DELETE /api/po-amendments/{id}` - Delete amendment
- `POST /api/po-amendments/{id}/approve` - Approve amendment
- `POST /api/po-amendments/{id}/reject` - Reject amendment

**Features:**
- Automatic amendment number generation (AMD-YYYY-XXXX)
- Amendment type tracking
- Approval workflow
- Change history tracking

### 4. PoReportController
**Endpoints:**
- `GET /api/reports/po-status` - Purchase order status report
- `GET /api/reports/po-by-vendor` - PO statistics by vendor
- `GET /api/reports/pending-deliveries` - Pending deliveries report
- `GET /api/reports/po-variance` - Price variance report

**Features:**
- Status breakdown with counts and values
- Vendor performance analysis
- Delivery tracking
- Price variance analysis (actual vs standard cost)

## Workflow

### 1. Create Purchase Order
```
Draft → Pending Approval → Approved → Sent → Partially Received → Received → Closed
                                  ↘
                                   Cancelled
```

### 2. Receipt Process
```
Create Receipt → Pending Inspection → Inspected → Accepted
                                              ↘
                                               Rejected/Partial
```

### 3. Amendment Process
```
Request Amendment → Pending → Approved
                          ↘
                           Rejected
```

## Business Rules

1. **Receipt Validation**
   - Cannot receive more than ordered quantity
   - Can create multiple partial receipts
   - Must sum up to total ordered quantity

2. **Status Management**
   - Draft POs can be edited/deleted
   - Approved POs can be sent
   - Sent POs can receive items
   - Received POs can be closed
   - Cancelled POs are final

3. **Amendment Control**
   - Only approved/sent POs can be amended
   - Amendments require approval
   - Track old and new values

4. **Automatic Updates**
   - PO items track received quantities
   - PO status updates based on receipt progress
   - Calculations handle discounts and taxes

## API Examples

### Create Purchase Order
```json
POST /api/purchase-orders
{
  "po_date": "2026-01-07",
  "vendor_id": 1,
  "project_id": 5,
  "delivery_address": "Site Office, Building A",
  "delivery_date": "2026-01-20",
  "currency_id": 1,
  "notes": "Urgent order",
  "items": [
    {
      "material_id": 10,
      "description": "Cement 50kg bags",
      "quantity": 100,
      "unit_price": 25.50,
      "discount_percentage": 5,
      "tax_percentage": 5
    }
  ]
}
```

### Create Receipt (Partial)
```json
POST /api/po-receipts
{
  "purchase_order_id": 123,
  "receipt_date": "2026-01-15",
  "warehouse_id": 2,
  "delivery_note_number": "DN-12345",
  "items": [
    {
      "po_item_id": 456,
      "quantity_received": 50,
      "batch_number": "BATCH-001"
    }
  ]
}
```

### Inspect Receipt
```json
POST /api/po-receipts/{id}/inspect
{
  "items": [
    {
      "id": 789,
      "quantity_accepted": 48,
      "quantity_rejected": 2,
      "rejection_reason": "Damaged packaging",
      "inspection_notes": "2 bags torn"
    }
  ]
}
```

### Create Amendment
```json
POST /api/po-amendments
{
  "purchase_order_id": 123,
  "amendment_date": "2026-01-10",
  "amendment_type": "quantity",
  "description": "Increase cement quantity",
  "old_value": "100",
  "new_value": "150",
  "reason": "Additional requirement from site"
}
```

## Tests

Comprehensive test suite includes:

1. **Purchase Order Tests**
   - Create purchase order
   - List purchase orders
   - Approve purchase order
   - Update purchase order
   - Delete purchase order

2. **Receipt Tests**
   - Create partial receipt
   - Validate quantity limits
   - Inspect receipt
   - Accept/reject items

3. **Amendment Tests**
   - Create amendment
   - Approve amendment
   - Reject amendment

4. **Workflow Tests**
   - Status transitions
   - Approval workflows
   - Quantity tracking

## Integration Points

### Inventory Management
- Receipts can trigger inventory updates
- Links with warehouse stock
- Material tracking

### Accounts Payable
- POs link to AP invoices
- Vendor payment tracking
- Three-way matching (PO, Receipt, Invoice)

### Project Management
- Optional project linking
- Project cost tracking
- Budget control

### Purchase Requisitions
- Can create PO from PR
- PR fulfillment tracking
- Approval chain

## Future Enhancements

1. **Automatic GRN Creation**
   - Generate GRN automatically upon receipt acceptance
   - Update inventory balances
   - Create accounting entries

2. **Email Notifications**
   - Send PO to vendor via email
   - Notify stakeholders on approval
   - Alert on pending deliveries

3. **PDF Generation**
   - Professional PO templates
   - Multi-language support
   - Digital signatures

4. **Advanced Analytics**
   - Spend analysis
   - Vendor scorecards
   - Delivery performance metrics
   - Budget vs actual reports

5. **Mobile Support**
   - Mobile receipt capture
   - Barcode scanning
   - Photo attachments for inspections

## Security Considerations

1. **Authentication**
   - All endpoints require Sanctum authentication
   - Company-level data isolation

2. **Authorization**
   - Role-based access control ready
   - Permission checks can be added
   - Approval hierarchies

3. **Audit Trail**
   - Soft deletes preserve history
   - Timestamps track changes
   - Amendment history maintained

## Support

For issues or questions:
1. Check migration compatibility
2. Verify model relationships
3. Review API documentation
4. Check test examples

## License

Part of CEMS ERP System
