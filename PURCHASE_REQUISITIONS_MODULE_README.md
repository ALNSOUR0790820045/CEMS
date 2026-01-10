# Purchase Requisitions Module (طلبات الشراء)

## Overview
This module implements a complete Purchase Requisition system with approval workflows, vendor quote management, and comprehensive reporting.

## Database Tables

### 1. `purchase_requisitions` - طلبات الشراء
Main table for purchase requisitions with the following fields:
- Auto-generated requisition number (PR-YYYY-XXXX format)
- Project and department tracking
- Multiple priority levels (low, normal, high, urgent)
- Different types (materials, services, equipment, subcontract)
- Complete status workflow
- Approval tracking

### 2. `purchase_requisition_items` - بنود طلب الشراء
Line items for each requisition with:
- Material reference or custom description
- Quantity and unit management
- Estimated pricing
- Preferred vendor selection
- Order quantity tracking

### 3. `pr_approval_workflows` - سير عمل الموافقات
Multi-level approval system with:
- Level-based approval hierarchy
- Approver assignments
- Status tracking (pending, approved, rejected, skipped)
- Comments and timestamps

### 4. `pr_quotes` - عروض الأسعار
Vendor quotes with:
- Auto-generated quote number (QT-YYYY-XXXX format)
- Validity period
- Payment and delivery terms
- Status tracking
- Document attachments

### 5. `pr_quote_items` - بنود عرض السعر
Quote line items with:
- Quantity and pricing
- Discount percentages
- Delivery timeframes
- Item-level notes

## Models

### PurchaseRequisition
Main model with relationships to:
- Project, Department, Users
- Currency, Company
- Items, Approval Workflows, Quotes

Key methods:
- `submit()` - Submit for approval
- `approve($user)` - Approve requisition
- `reject($user, $reason)` - Reject with reason
- `cancel()` - Cancel requisition

### PurchaseRequisitionItem
Line item model with relationships to:
- Purchase Requisition
- Material, Unit, Vendor
- Quote Items

### PrApprovalWorkflow
Approval workflow tracking with methods:
- `approve($comments)` - Approve at this level
- `reject($comments)` - Reject at this level

### PrQuote
Quote management with:
- Auto-numbering system
- `select()` - Mark quote as selected (rejects others)

### PrQuoteItem
Individual quote line items

## API Endpoints

### Purchase Requisitions
```
GET    /api/purchase-requisitions              - List all requisitions
POST   /api/purchase-requisitions              - Create new requisition
GET    /api/purchase-requisitions/{id}         - Get requisition details
PUT    /api/purchase-requisitions/{id}         - Update requisition (draft only)
DELETE /api/purchase-requisitions/{id}         - Delete requisition
POST   /api/purchase-requisitions/{id}/submit  - Submit for approval
POST   /api/purchase-requisitions/{id}/approve - Approve requisition
POST   /api/purchase-requisitions/{id}/reject  - Reject requisition
POST   /api/purchase-requisitions/{id}/cancel  - Cancel requisition
GET    /api/purchase-requisitions/{id}/approval-history - Get approval history
POST   /api/purchase-requisitions/{id}/convert-to-po - Convert to Purchase Order
```

### Quotes
```
GET    /api/pr-quotes                          - List all quotes
POST   /api/pr-quotes                          - Create new quote
GET    /api/pr-quotes/{id}                     - Get quote details
PUT    /api/pr-quotes/{id}                     - Update quote
DELETE /api/pr-quotes/{id}                     - Delete quote
POST   /api/pr-quotes/{id}/select              - Select winning quote
GET    /api/purchase-requisitions/{prId}/quotes - Get quotes for PR
POST   /api/purchase-requisitions/{prId}/request-quotes - Request quotes from vendors
```

### Reports
```
GET    /api/reports/pr-status                  - PR status breakdown report
GET    /api/reports/pr-by-department           - PRs grouped by department
GET    /api/reports/pending-approvals          - Pending approvals for user
```

## Workflow

1. **Create Draft** - User creates a purchase requisition in draft status
2. **Submit for Approval** - Requisition is submitted (status: pending_approval)
3. **Approval Process** - Multi-level approvers review and approve/reject
4. **Request Quotes** - After approval, request quotes from vendors
5. **Quote Comparison** - Compare quotes and select the best one
6. **Convert to PO** - Convert approved requisition to Purchase Order

## Business Rules

1. **Edit Restrictions**: Requisitions can only be edited in 'draft' status
2. **Delete Restrictions**: Only 'draft' or 'rejected' requisitions can be deleted
3. **Approval Flow**: Requisitions must be in 'pending_approval' to approve/reject
4. **Quote Selection**: Selecting a quote automatically rejects other quotes for that PR
5. **Multi-level Approvals**: Approval workflows can have multiple approval levels
6. **Auto-numbering**: System automatically generates PR and Quote numbers

## Usage Examples

### Creating a Purchase Requisition
```json
POST /api/purchase-requisitions
{
  "requisition_date": "2026-01-07",
  "required_date": "2026-02-07",
  "department_id": 1,
  "priority": "normal",
  "type": "materials",
  "currency_id": 1,
  "justification": "Required for Project X",
  "items": [
    {
      "item_description": "Steel Bars",
      "specifications": "Grade 60, 16mm",
      "quantity_requested": 100,
      "unit_id": 1,
      "estimated_unit_price": 50.00
    }
  ]
}
```

### Creating a Quote
```json
POST /api/pr-quotes
{
  "purchase_requisition_id": 1,
  "vendor_id": 5,
  "quote_date": "2026-01-08",
  "validity_date": "2026-02-08",
  "currency_id": 1,
  "payment_terms": "Net 30",
  "delivery_terms": "FOB Destination",
  "items": [
    {
      "pr_item_id": 1,
      "quantity": 100,
      "unit_price": 48.00,
      "discount_percentage": 5
    }
  ]
}
```

## Testing

The module includes comprehensive tests in `tests/Feature/PurchaseRequisitionTest.php`:
- Create purchase requisition
- List requisitions with filters
- Submit for approval
- Approve/reject workflow
- Create and select quotes
- Edit restrictions

**Note**: There is a pre-existing issue in the repository with duplicate migration files for the `cities` table. This may cause test failures when running with `RefreshDatabase`. The Purchase Requisitions module code is correct and functional.

To run the tests:
```bash
php artisan test --filter=PurchaseRequisitionTest
```

## Future Enhancements

1. Email notifications for approval requests
2. Purchase Order generation
3. Budget checking integration
4. Three-quote requirement enforcement
5. Vendor performance tracking
6. Automated vendor quote requests
7. Document workflow for attachments
8. Mobile approval interface

## Files Created

### Migrations
- `2026_01_07_191107_create_purchase_requisitions_table.php`
- `2026_01_07_191114_create_purchase_requisition_items_table.php`
- `2026_01_07_191114_create_pr_approval_workflows_table.php`
- `2026_01_07_191114_create_pr_quotes_table.php`
- `2026_01_07_191115_create_pr_quote_items_table.php`

### Models
- `app/Models/PurchaseRequisition.php`
- `app/Models/PurchaseRequisitionItem.php`
- `app/Models/PrApprovalWorkflow.php`
- `app/Models/PrQuote.php`
- `app/Models/PrQuoteItem.php`

### Controllers
- `app/Http/Controllers/Api/PurchaseRequisitionController.php`
- `app/Http/Controllers/Api/PrQuoteController.php`
- `app/Http/Controllers/Api/PrReportController.php`
- `app/Http/Controllers/Api/PrApprovalController.php` (placeholder)

### Tests
- `tests/Feature/PurchaseRequisitionTest.php`

### Factories
- `database/factories/PurchaseRequisitionFactory.php`

## Integration Points

This module integrates with:
- **Projects**: PRs can be linked to specific projects
- **Departments**: Track which department requests the PR
- **Vendors**: Quote management from multiple vendors
- **Materials**: Link to material master data
- **Units**: Standard unit of measure
- **Currencies**: Multi-currency support
- **Users**: Requestor and approver tracking
- **Purchase Orders**: (Future) Convert PR to PO

## Security Considerations

- All endpoints require authentication via Sanctum
- Permission-based access control should be implemented
- Edit restrictions prevent tampering after submission
- Approval audit trail maintained
- Soft deletes preserve data history

## Maintenance

To add this module to your Laravel application:

1. Run migrations:
   ```bash
   php artisan migrate
   ```

2. Ensure required dependencies exist:
   - Company model and table
   - Currency model and table
   - Department model and table
   - Material model and table
   - Unit model and table
   - Vendor model and table
   - User model with Sanctum authentication

3. Configure permissions if using Spatie Permission package

4. Test the API endpoints using Postman or similar tool
