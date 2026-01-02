# Procurement Planning for Tenders

## Overview

The Procurement Planning module provides comprehensive tools for managing procurement activities within tender processes. It enables systematic planning, supplier evaluation, timeline management, and tracking of long-lead items.

## Features

### 1. Procurement Packages Management

Organize procurement activities into logical packages with the following attributes:

- **Package Information**
  - Unique package code (e.g., PKG-001)
  - Package name and description
  - Procurement type: materials, equipment, subcontract, services, rental
  - Category: civil, structural, architectural, electrical, mechanical, plumbing, finishing

- **Scope & Cost**
  - Detailed scope of work
  - Bill of quantities (JSON format)
  - Estimated value

- **Timeline**
  - Required by date
  - Lead time (in days)
  - Procurement start date

- **Strategy**
  - Competitive bidding
  - Direct purchase
  - Framework agreement
  - Preferred supplier

- **Requirements**
  - Technical specifications
  - Sample requirements
  - Warranty requirements

### 2. Supplier Management

- Add multiple suppliers to each package
- Track quotations and pricing
- Record delivery times
- Document payment terms
- Evaluate technical compliance
- Score suppliers (0-100)
- Mark recommended suppliers

### 3. Timeline & Gantt Chart

- Visual timeline for all procurement packages
- Gantt chart showing procurement schedule
- Identification of critical items (>60 days lead time)
- Responsible person tracking

### 4. Long Lead Items Tracking

- Dedicated tracking for items with extended delivery times
- Critical item flagging
- Must-order-by date alerts
- Mitigation plan documentation
- Calendar view for planning
- Overdue item notifications

## Database Schema

### Tables

1. **tenders** - Base tender information
2. **suppliers** - Supplier master data
3. **tender_procurement_packages** - Procurement packages for each tender
4. **tender_procurement_suppliers** - Supplier quotations and evaluations
5. **tender_long_lead_items** - Items requiring early ordering

## Routes

All routes are prefixed with `/tenders/{tender}/` and protected by authentication:

```php
// Package Management
GET    /procurement                    - List all packages
GET    /procurement/create             - Create new package form
POST   /procurement                    - Store new package
GET    /procurement/{package}          - Show package details
GET    /procurement/{package}/edit     - Edit package form
PUT    /procurement/{package}          - Update package
DELETE /procurement/{package}          - Delete package

// Supplier Management
GET    /procurement/{package}/suppliers     - Supplier comparison
POST   /procurement/{package}/suppliers     - Add supplier
PUT    /procurement/{package}/suppliers/{id} - Update supplier

// Special Views
GET    /procurement-timeline            - Timeline & Gantt chart
GET    /long-lead-items                - Long lead items list
POST   /long-lead-items                - Add long lead item
```

## Models

### TenderProcurementPackage

```php
$package = TenderProcurementPackage::create([
    'tender_id' => 1,
    'package_code' => 'PKG-001',
    'package_name' => 'Structural Steel',
    'procurement_type' => 'materials',
    'estimated_value' => 500000.00,
    'strategy' => 'competitive_bidding',
    // ... other fields
]);
```

**Relationships:**
- `tender()` - BelongsTo Tender
- `responsible()` - BelongsTo User
- `suppliers()` - BelongsToMany Supplier
- `longLeadItems()` - HasMany TenderLongLeadItem

**Helper Methods:**
- `getStatusLabelAttribute()` - Returns Arabic status label
- `getProcurementTypeLabel()` - Returns Arabic type label
- `getCategoryLabel()` - Returns Arabic category label
- `getStrategyLabel()` - Returns Arabic strategy label

### TenderLongLeadItem

```php
$item = TenderLongLeadItem::create([
    'tender_id' => 1,
    'item_name' => 'Elevator System',
    'lead_time_weeks' => 16,
    'must_order_by' => '2026-03-01',
    'is_critical' => true,
    // ... other fields
]);
```

**Helper Methods:**
- `isDueWithinDays($days)` - Check if item is due soon
- `isOverdue()` - Check if order deadline has passed

## Usage Examples

### Creating a Procurement Package

1. Navigate to tender page
2. Click "خطة الشراء" (Procurement Plan)
3. Click "+ إضافة حزمة" (Add Package)
4. Fill in package details
5. Submit form

### Adding Suppliers

1. Go to package details
2. Click "إدارة الموردين" (Manage Suppliers)
3. Select supplier from dropdown
4. Enter quotation details
5. Assign score and recommendation
6. Submit

### Viewing Timeline

1. Go to procurement packages list
2. Click "الجدول الزمني" (Timeline)
3. View timeline and Gantt chart
4. Identify critical items (marked in red)

### Tracking Long Lead Items

1. Go to procurement packages list
2. Click "البنود طويلة الأجل" (Long Lead Items)
3. View items sorted by order deadline
4. Monitor overdue items (red alert)
5. Add mitigation plans for critical items

## Design Features

- **RTL Support**: Full right-to-left layout for Arabic
- **Responsive**: Mobile-friendly design
- **Modern UI**: Clean, Apple-inspired design system
- **Visual Indicators**: Color-coded badges and status labels
- **Interactive**: Filters, sorting, and dynamic content
- **Accessibility**: Proper semantic HTML and ARIA labels

## Security

- All routes protected by authentication middleware
- CSRF protection on all forms
- Input validation using Laravel Form Requests
- SQL injection prevention via Eloquent ORM
- XSS protection via Blade templating

## Performance Considerations

- User queries limited to 100 records
- Eager loading of relationships to prevent N+1 queries
- Indexed foreign keys and unique constraints
- JSON storage for flexible quantity data

## Future Enhancements

- Export to Excel/PDF
- Email notifications for overdue items
- Advanced supplier scoring algorithms
- Integration with BOQ module
- Purchase order generation
- Budget tracking and comparison
- Multi-currency support

## Support

For issues or questions, please contact the development team or create an issue in the repository.
