# Variation Orders Module - Documentation

## Overview
This module provides a complete solution for managing Variation Orders (أوامر التغيير) in construction projects. It includes database migrations, models, controllers, views, and API endpoints.

## Features Implemented

### ✅ Database Structure
- **variation_orders**: Main table for variation orders with full workflow support
- **variation_order_items**: Line items for each variation order
- **variation_order_attachments**: File attachments (drawings, photos, documents)
- **variation_order_timeline**: Complete audit trail of all actions
- **Supporting tables**: projects, contracts, boq_items

### ✅ Models
- `VariationOrder`: Main model with relationships and helper methods
- `VariationOrderItem`: Line items with auto-calculation
- `VariationOrderAttachment`: File management
- `VariationOrderTimeline`: Audit trail
- `Project`, `Contract`, `BoqItem`: Supporting models

### ✅ Controller Features
- Full CRUD operations (Create, Read, Update, Delete)
- Workflow actions: submit, approve, reject
- Statistics endpoint for reporting
- Export to print/PDF view
- Project-specific variation orders listing

### ✅ Web Routes
```
GET    /variation-orders                  - List view
POST   /variation-orders                  - Create
GET    /variation-orders/create           - Create form
GET    /variation-orders/{id}             - Details view
GET    /variation-orders/{id}/edit        - Edit form
PUT    /variation-orders/{id}             - Update
DELETE /variation-orders/{id}             - Delete
POST   /variation-orders/{id}/submit      - Submit for review
POST   /variation-orders/{id}/approve     - Approve
POST   /variation-orders/{id}/reject      - Reject
GET    /variation-orders/{id}/export      - Export PDF
```

### ✅ API Routes
```
GET    /api/variation-orders                      - List (JSON)
POST   /api/variation-orders                      - Create (JSON)
GET    /api/variation-orders/{id}                 - Details (JSON)
PUT    /api/variation-orders/{id}                 - Update (JSON)
DELETE /api/variation-orders/{id}                 - Delete
POST   /api/variation-orders/{id}/submit          - Submit
POST   /api/variation-orders/{id}/approve         - Approve
POST   /api/variation-orders/{id}/reject          - Reject
GET    /api/variation-orders/statistics           - Statistics
GET    /api/projects/{id}/variation-orders        - By Project
```

### ✅ Views (RTL Supported)
1. **index.blade.php**: List view with filtering
2. **create.blade.php**: Creation form
3. **edit.blade.php**: Edit form
4. **show.blade.php**: Details view with workflow actions
5. **print.blade.php**: Print/PDF export view

## Workflow States

```
identified → draft → submitted → under_review → negotiating → approved/rejected
                                                           ↓
                                                    in_progress → completed
```

Available statuses:
- `identified`: تم تحديده - Initial identification
- `draft`: مسودة - Work in progress
- `submitted`: مقدم - Submitted for review
- `under_review`: قيد المراجعة - Being reviewed
- `negotiating`: قيد التفاوض - In negotiation
- `approved`: معتمد - Approved
- `rejected`: مرفوض - Rejected
- `partially_approved`: معتمد جزئياً - Partially approved
- `in_progress`: قيد التنفيذ - Being executed
- `completed`: منتهي - Completed
- `cancelled`: ملغي - Cancelled

## Types of Variation Orders

- `addition`: إضافة أعمال - Addition of work
- `omission`: حذف أعمال - Omission of work
- `modification`: تعديل أعمال - Modification of work
- `substitution`: استبدال - Substitution

## Sources

- `client`: طلب العميل - Client request
- `consultant`: طلب الاستشاري - Consultant request
- `contractor`: طلب المقاول - Contractor request
- `design_change`: تغيير التصميم - Design change
- `site_condition`: ظروف الموقع - Site conditions

## Installation & Setup

### 1. Run Migrations
```bash
php artisan migrate
```

### 2. Seed Test Data (Optional)
```bash
php artisan db:seed --class=TestDataSeeder
```

This will create:
- 1 test user (email: test@example.com, password: password)
- 1 company
- 2 projects
- 1 contract
- 3 variation orders (draft, submitted, approved)

### 3. Access the Module
Navigate to: `/variation-orders`

## Usage Examples

### Creating a Variation Order

```php
$variationOrder = VariationOrder::create([
    'vo_number' => 'temp', // Will be auto-generated
    'project_id' => 1,
    'contract_id' => 1,
    'sequence_number' => 1,
    'title' => 'Additional Floor',
    'description' => 'Add one additional floor',
    'type' => 'addition',
    'source' => 'client',
    'estimated_value' => 1000000,
    'currency' => 'SAR',
    'identification_date' => now(),
    'status' => 'draft',
    'priority' => 'high',
    'requested_by' => auth()->id(),
]);

// Generate VO number
$variationOrder->vo_number = $variationOrder->generateVoNumber();
$variationOrder->save();

// Add timeline entry
$variationOrder->addTimelineEntry('Created', null, 'draft', 'VO created');
```

### Submitting for Review

```php
$variationOrder->update([
    'status' => 'submitted',
    'submission_date' => now(),
]);

$variationOrder->addTimelineEntry('Submitted', 'draft', 'submitted');
```

### Approving

```php
$variationOrder->update([
    'status' => 'approved',
    'approved_value' => 950000,
    'approval_date' => now(),
    'approved_by' => auth()->id(),
]);

$variationOrder->addTimelineEntry('Approved', 'submitted', 'approved', 'Approved for 950,000 SAR');
```

### Getting Statistics

```php
$statistics = [
    'total_count' => VariationOrder::count(),
    'by_status' => VariationOrder::select('status', DB::raw('count(*) as count'))
        ->groupBy('status')
        ->pluck('count', 'status'),
    'total_estimated_value' => VariationOrder::sum('estimated_value'),
    'total_approved_value' => VariationOrder::sum('approved_value'),
];
```

## Security Features

- All routes require authentication (`auth` middleware)
- API routes use Sanctum authentication
- Soft deletes enabled for data recovery
- Complete audit trail via timeline
- Status-based permission checks

## Financial Tracking

Each variation order tracks:
- **estimated_value**: Initial estimate
- **quoted_value**: Contractor's quote
- **approved_value**: Client-approved value
- **executed_value**: Actual executed value

## Time Impact

- **time_impact_days**: Estimated impact on project duration
- **extension_approved**: Boolean flag for approval
- **approved_extension_days**: Actual approved extension

## RTL Support

All views are fully RTL (Right-to-Left) compatible with:
- Arabic language support
- Proper text direction
- Cairo font family
- Bilingual labels (Arabic/English)

## Testing

### Manual Testing
1. Start server: `php artisan serve`
2. Login with test credentials
3. Navigate to `/variation-orders`
4. Test CRUD operations
5. Test workflow (submit, approve, reject)

### Database Verification
```bash
# Check variation orders
sqlite3 database/database.sqlite "SELECT vo_number, title, status FROM variation_orders;"

# Check timeline
sqlite3 database/database.sqlite "SELECT vo.vo_number, t.action, t.created_at FROM variation_order_timeline t JOIN variation_orders vo ON t.variation_order_id = vo.id;"
```

## API Testing

### List All VOs
```bash
curl -X GET http://localhost:8000/api/variation-orders \
  -H "Authorization: Bearer {token}"
```

### Get Statistics
```bash
curl -X GET http://localhost:8000/api/variation-orders/statistics \
  -H "Authorization: Bearer {token}"
```

### Create VO
```bash
curl -X POST http://localhost:8000/api/variation-orders \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{
    "project_id": 1,
    "title": "New VO",
    "description": "Description",
    "type": "addition",
    "source": "client",
    "identification_date": "2024-01-01",
    "priority": "medium"
  }'
```

## Future Enhancements

- [ ] Email notifications on status changes
- [ ] PDF generation using Laravel DomPDF
- [ ] File upload for attachments
- [ ] Advanced reporting dashboard
- [ ] Integration with project budget
- [ ] Multi-currency support
- [ ] Cost center allocation
- [ ] Approval workflow customization

## Support

For issues or questions, please contact the development team.

---

**Module Version:** 1.0.0  
**Laravel Version:** 12.x  
**Database:** SQLite/PostgreSQL/MySQL  
**Created:** 2026-01-04
