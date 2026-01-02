# Site Receipt Feature - Quick Start Guide

## What's Been Implemented

A complete Site Receipt management system with:
- ✅ GPS-based location tracking
- ✅ Triple digital signature system (Engineer, Storekeeper, Driver)
- ✅ 4 mandatory document uploads
- ✅ Photo documentation with GPS metadata
- ✅ Auto-GRN creation
- ✅ Mobile-first 7-step wizard
- ✅ Manager verification workflow

## Database Tables Created

1. **projects** - Project management
2. **suppliers** - Supplier information  
3. **products** - Product catalog
4. **purchase_orders** - Purchase orders
5. **purchase_order_items** - PO line items
6. **goods_receipt_notes** - GRN records
7. **site_receipts** - Main receipts with GPS & signatures
8. **site_receipt_items** - Receipt line items
9. **site_receipt_photos** - Photos with GPS metadata

## Files Added

### Migrations (9 files)
- `/database/migrations/2026_01_02_220959_create_projects_table.php`
- `/database/migrations/2026_01_02_221006_create_suppliers_table.php`
- `/database/migrations/2026_01_02_221006_create_products_table.php`
- `/database/migrations/2026_01_02_221006_create_purchase_orders_table.php`
- `/database/migrations/2026_01_02_221007_create_purchase_order_items_table.php`
- `/database/migrations/2026_01_02_221007_create_goods_receipt_notes_table.php`
- `/database/migrations/2026_01_02_221014_create_site_receipts_table.php`
- `/database/migrations/2026_01_02_221015_create_site_receipt_items_table.php`
- `/database/migrations/2026_01_02_221015_create_site_receipt_photos_table.php`

### Models (9 files)
- `/app/Models/Project.php`
- `/app/Models/Supplier.php`
- `/app/Models/Product.php`
- `/app/Models/PurchaseOrder.php`
- `/app/Models/PurchaseOrderItem.php`
- `/app/Models/GoodsReceiptNote.php`
- `/app/Models/SiteReceipt.php`
- `/app/Models/SiteReceiptItem.php`
- `/app/Models/SiteReceiptPhoto.php`

### Controller
- `/app/Http/Controllers/SiteReceiptController.php` - Full CRUD with auto-GRN logic

### Views (4 files)
- `/resources/views/site-receipts/index.blade.php` - List view with filters & map
- `/resources/views/site-receipts/create.blade.php` - 7-step creation wizard
- `/resources/views/site-receipts/show.blade.php` - Detailed view
- `/resources/views/site-receipts/verify.blade.php` - Manager verification

### Routes
Updated `/routes/web.php` with Site Receipt routes

### Documentation
- `/SITE_RECEIPT_DOCUMENTATION.md` - Complete documentation

## Quick Setup

### 1. Run Migrations
```bash
cd /home/runner/work/CEMS/CEMS
php artisan migrate
```

### 2. Create Storage Link
```bash
php artisan storage:link
```

### 3. Access the Feature
Navigate to: `http://your-domain/site-receipts`

## Usage Flow

### Creating a Site Receipt

1. **Go to:** `/site-receipts/create`

2. **Step 1:** Enter basic info (Project, Supplier, Date, Vehicle)

3. **Step 2:** Capture GPS location (browser will ask for permission)

4. **Step 3:** Add materials/items received

5. **Step 4:** Upload 4 required documents:
   - Invoice
   - Delivery Note
   - Packing List
   - Quality Certificates

6. **Step 5:** Take photos (optional)

7. **Step 6:** Collect 3 digital signatures:
   - Site Engineer
   - Storekeeper  
   - Driver/Supplier

8. **Step 7:** Review and submit

### Auto-Actions After Submit

When all 3 signatures are complete:
- ✅ GRN is created automatically
- ✅ Inventory is updated
- ✅ Finance is notified
- ✅ Status changes to "grn_created"
- ✅ Payment status set to "ready_for_payment"

## Features Highlights

### GPS Tracking
- Captures exact location coordinates
- Records timestamp
- Immutable data
- Google Maps integration ready

### Triple Signature System
All three signatures are MANDATORY:
1. Site Engineer - Verifies on-site
2. Storekeeper - Confirms warehouse receipt
3. Driver/Supplier - Confirms delivery

Without all 3 signatures, the receipt cannot be processed!

### Document Management
4 documents are REQUIRED:
1. Original Invoice (PDF/Image)
2. Delivery Note (PDF/Image)
3. Packing List (PDF/Image)
4. Quality Certificates (PDF/Image, multiple files)

### Photo Documentation
- Direct camera capture
- GPS auto-attached to each photo
- Categorization (vehicle, materials, documents, packaging, damage)
- Hash verification for authenticity

### Manager Verification
- Checklist validation
- Approve/Reject workflow
- Manager notes
- Final verification before processing

## Mobile Optimization

The system is fully mobile-responsive:
- Touch-friendly signature pads
- Camera integration
- GPS auto-detection
- Step-by-step wizard
- Optimized for on-site use

## Status Workflow

```
draft → pending_verification → verified → grn_created
```

Rejected receipts go to: `rejected` status

## Technical Details

### Receipt Number Format
`SR-YYYY-NNN` (e.g., SR-2026-001)

### GRN Number Format  
`GRN-YYYY-NNN` (e.g., GRN-2026-001)

### Storage Structure
```
storage/app/public/site-receipts/
  ├── invoices/
  ├── delivery-notes/
  ├── packing-lists/
  ├── quality-certificates/
  └── photos/
```

### Signature Storage
Digital signatures are stored as base64-encoded PNG images in the database.

### Photo Hash
Each photo gets a unique SHA-256 hash for verification:
```php
hash('sha256', $photoPath . $latitude . $longitude . $timestamp)
```

## Testing Checklist

Before going live, test:
- [ ] GPS capture works in browser
- [ ] Signature canvas works on mobile
- [ ] File uploads (all 4 documents)
- [ ] Photo capture from camera
- [ ] All 3 signatures required
- [ ] Auto-GRN creation
- [ ] Manager verification workflow
- [ ] Mobile responsiveness

## Next Steps

1. Run migrations: `php artisan migrate`
2. Create test data (projects, suppliers, products)
3. Test the complete workflow
4. Configure Google Maps API key (optional)
5. Set up proper user roles/permissions
6. Train users on the system

## Notes

- The system is ready to use after running migrations
- Database must be configured in `.env` file
- HTTPS is recommended for GPS functionality
- Storage directories must be writable

---

**Need Help?** See `SITE_RECEIPT_DOCUMENTATION.md` for detailed documentation.
