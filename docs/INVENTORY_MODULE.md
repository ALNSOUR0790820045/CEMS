# Inventory Management Module

## Overview
Complete inventory control system with stock movements, valuation, and warehouse management for the CEMS (Construction ERP Management System).

## Features

### ✅ Implemented Features

#### 1. Stock Movements
- ✅ Receipts (from GRN)
- ✅ Issues (to projects)
- ✅ Transfers between warehouses
- ✅ Adjustments
- ✅ Returns

#### 2. Valuation Methods
- ✅ Average Cost (implemented)
- 🔲 FIFO (not implemented)
- 🔲 LIFO (not implemented)
- 🔲 Standard Cost (not implemented)

#### 3. Stock Control
- ✅ Reorder alerts (low stock filtering)
- ✅ Stock levels by warehouse
- ✅ Reserved quantities (field available)
- ✅ Available stock (computed field)

#### 4. Stock Transfers
- ✅ Create transfer requests
- ✅ Approval workflow
- ✅ In-transit status
- ✅ Receive and complete transfers
- ✅ Cancel transfers
- ✅ Partial receipts support

#### 5. Reports
- ✅ Stock status
- ✅ Movement history
- ✅ Valuation report
- ✅ Low stock alerts
- 🔲 Slow-moving items (not implemented)
- 🔲 Stock aging (not implemented)

#### 6. API Endpoints
- ✅ `GET /api/inventory/balance`
- ✅ `POST /api/inventory/transactions`
- ✅ `GET /api/inventory/transactions`
- ✅ `GET /api/stock-transfers`
- ✅ `POST /api/stock-transfers`
- ✅ `GET /api/stock-transfers/{id}`
- ✅ `POST /api/stock-transfers/{id}/approve`
- ✅ `POST /api/stock-transfers/{id}/receive`
- ✅ `POST /api/stock-transfers/{id}/cancel`
- ✅ `GET /api/inventory/reports/valuation`
- ✅ `GET /api/inventory/reports/stock-status`
- ✅ `GET /api/inventory/reports/movement`
- ✅ `GET /api/inventory/reports/low-stock`

## Database Schema

### Tables Created

#### 1. `materials`
Stores material/item master data.
- Auto-generated code
- Unit of measure
- Standard cost
- Reorder levels
- Category classification

#### 2. `warehouses`
Stores warehouse master data.
- Warehouse code and name
- Location details
- Manager information
- Active status

#### 3. `projects`
Stores project information.
- Project code and name
- Start/end dates
- Status tracking
- Location

#### 4. `inventory_transactions`
Records all inventory movements.
- Auto-generated transaction number (INV-YYYY-XXXX)
- Transaction types: receipt, issue, transfer, adjustment, return
- Quantity and cost tracking
- Reference linking (polymorphic)
- Audit trail with user tracking

#### 5. `inventory_balances`
Maintains current stock levels per warehouse/material.
- Quantity on hand
- Quantity reserved
- Quantity available (computed)
- Average cost calculation
- Last cost tracking
- Total value (computed)

#### 6. `stock_transfers`
Stock transfer header records.
- Auto-generated transfer number (STR-YYYY-XXXX)
- From/to warehouse tracking
- Status workflow: pending → approved → in_transit → completed
- Approval/receipt user tracking

#### 7. `stock_transfer_items`
Stock transfer line items.
- Material and quantities
- Requested vs transferred vs received quantities
- Unit cost tracking
- Item-level notes

## Architecture

### Models
- `Material` - Material master data
- `Warehouse` - Warehouse master data
- `Project` - Project master data
- `InventoryTransaction` - All inventory movements
- `InventoryBalance` - Current stock levels
- `StockTransfer` - Transfer header
- `StockTransferItem` - Transfer line items

### Services
- `InventoryService` - Core inventory operations
  - Record receipts, issues, adjustments
  - Update inventory balances
  - Average cost calculation
  - Balance queries and reports
  - Transaction history
  - Valuation reports

- `StockTransferService` - Stock transfer workflow
  - Create transfers
  - Approval process
  - Mark in-transit
  - Receive transfers
  - Cancel transfers
  - Transfer queries

### Controllers
- `InventoryController` - Inventory balance and transactions API
- `StockTransferController` - Stock transfer CRUD and workflow API
- `InventoryReportController` - Inventory reports API

## Installation

### 1. Run Migrations
```bash
php artisan migrate
```

This will create all necessary tables:
- materials
- warehouses
- projects
- inventory_transactions
- inventory_balances
- stock_transfers
- stock_transfer_items

### 2. Seed Sample Data (Optional)
Create seeders for materials, warehouses, and initial inventory if needed.

### 3. API Routes
API routes are automatically registered in `routes/api.php` and protected by `auth:sanctum` middleware.

## Usage Examples

### Record a Receipt
```bash
curl -X POST http://localhost/api/inventory/transactions \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{
    "transaction_type": "receipt",
    "transaction_date": "2026-01-03",
    "material_id": 1,
    "warehouse_id": 1,
    "quantity": 100,
    "unit_cost": 10.00,
    "notes": "Initial stock"
  }'
```

### Issue Materials to Project
```bash
curl -X POST http://localhost/api/inventory/transactions \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{
    "transaction_type": "issue",
    "transaction_date": "2026-01-03",
    "material_id": 1,
    "warehouse_id": 1,
    "quantity": 50,
    "unit_cost": 10.00,
    "project_id": 1,
    "notes": "Issued to project"
  }'
```

### Create Stock Transfer
```bash
curl -X POST http://localhost/api/stock-transfers \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{
    "transfer_date": "2026-01-03",
    "from_warehouse_id": 1,
    "to_warehouse_id": 2,
    "notes": "Monthly transfer",
    "items": [
      {
        "material_id": 1,
        "requested_quantity": 50,
        "unit_cost": 10.00
      }
    ]
  }'
```

### Approve Transfer
```bash
curl -X POST http://localhost/api/stock-transfers/1/approve \
  -H "Authorization: Bearer {token}"
```

### Receive Transfer
```bash
curl -X POST http://localhost/api/stock-transfers/1/receive \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{
    "received_quantities": {
      "1": 45
    }
  }'
```

### Get Inventory Balance
```bash
curl -X GET "http://localhost/api/inventory/balance?warehouse_id=1" \
  -H "Authorization: Bearer {token}"
```

### Get Valuation Report
```bash
curl -X GET "http://localhost/api/inventory/reports/valuation?warehouse_id=1" \
  -H "Authorization: Bearer {token}"
```

## Testing

### Run Tests
```bash
# Run all inventory tests
php artisan test tests/Feature/InventoryServiceTest.php
php artisan test tests/Feature/StockTransferServiceTest.php

# Run all tests
php artisan test
```

### Test Coverage
- ✅ Inventory receipt recording
- ✅ Inventory issue recording
- ✅ Insufficient stock validation
- ✅ Inventory adjustment
- ✅ Average cost calculation
- ✅ Valuation report
- ✅ Stock transfer creation
- ✅ Stock transfer approval
- ✅ Stock transfer receiving
- ✅ Stock transfer cancellation
- ✅ Business rule validations

Total: **12 tests** with **39 assertions** - All passing ✅

## Business Logic

### Average Cost Calculation
When receiving materials:
```
New Average Cost = (Old Value + New Value) / (Old Quantity + New Quantity)
```

Example:
1. Receive 100 units @ $10 = $1,000 (Average: $10)
2. Receive 50 units @ $12 = $600
3. New average = ($1,000 + $600) / (100 + 50) = $10.67

### Stock Transfer Workflow
1. **Create** (status: pending)
   - Define source and destination warehouses
   - Add materials with requested quantities
   
2. **Approve** (status: approved)
   - Authorized user approves transfer
   - Sets transferred quantities = requested quantities
   
3. **In Transit** (status: in_transit) - Optional
   - Mark when goods leave source warehouse
   
4. **Receive** (status: completed)
   - Record actual received quantities
   - Creates two inventory transactions:
     - Issue from source warehouse (negative)
     - Receipt to destination warehouse (positive)
   - Updates inventory balances in both warehouses

### Inventory Balance Updates
- **Receipts & Positive Adjustments**: Increase quantity, update average cost
- **Issues & Negative Adjustments**: Decrease quantity, maintain average cost
- **Transfers**: Handled via receive workflow (issue + receipt)

## Security

- All API endpoints protected by `auth:sanctum` middleware
- Operations scoped to authenticated user's company
- User tracking for all transactions (created_by_id)
- Approval/receipt tracking with user IDs

## Future Enhancements

### Potential Improvements
1. **Additional Valuation Methods**
   - FIFO (First In First Out)
   - LIFO (Last In First Out)
   - Standard Cost

2. **Advanced Reporting**
   - Slow-moving items analysis
   - Stock aging report
   - ABC analysis
   - Turnover ratios

3. **Physical Count**
   - Count sheets generation
   - Variance analysis
   - Adjustment posting workflow

4. **Batch/Serial Number Tracking**
   - Lot number management
   - Serial number tracking
   - Expiry date management

5. **Min/Max Inventory Planning**
   - Automatic reorder suggestions
   - EOQ calculations
   - Safety stock management

6. **Integration Points**
   - Purchase Order receipts (GRN)
   - Sales Order issues
   - Production consumption
   - Return processing

## Documentation

- **API Documentation**: See [INVENTORY_API.md](./INVENTORY_API.md)
- **Database Schema**: See migration files in `database/migrations/`
- **Tests**: See `tests/Feature/InventoryServiceTest.php` and `tests/Feature/StockTransferServiceTest.php`

## Support

For issues, questions, or contributions, please refer to the main CEMS repository.

## License

Part of the CEMS (Construction ERP Management System) project.
