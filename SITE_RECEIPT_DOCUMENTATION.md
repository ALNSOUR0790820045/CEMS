# Site Receipt System - Documentation

## Overview
This system provides a comprehensive solution for managing site receipts with GPS tracking, digital signatures, and automatic GRN (Goods Receipt Note) creation.

## Features Implemented

### 1. Database Structure
- **9 Tables Created:**
  - `projects` - Project management
  - `suppliers` - Supplier information
  - `products` - Product catalog
  - `purchase_orders` - Purchase orders
  - `purchase_order_items` - PO line items
  - `goods_receipt_notes` - GRN records
  - `site_receipts` - Main site receipt records with GPS & signatures
  - `site_receipt_items` - Receipt line items
  - `site_receipt_photos` - Photo documentation with GPS metadata

### 2. Key Features

#### A. GPS Tracking
- Captures precise GPS coordinates (latitude/longitude)
- Records GPS capture timestamp
- Displays location on maps
- Immutable GPS data for verification

#### B. Triple Signature System (Mandatory)
1. **Site Engineer** - Verifies materials on site
2. **Storekeeper** - Confirms receipt into warehouse
3. **Driver/Supplier** - Confirms delivery

All three signatures are required before the receipt can be processed.

#### C. Document Management (4 Required Documents)
1. Original Invoice
2. Delivery Note
3. Packing List
4. Quality Certificates (multiple files supported)

#### D. Photo Documentation
- Capture photos directly from camera
- Auto-attach GPS coordinates to each photo
- Categorize photos (vehicle, materials, documents, packaging, damage, general)
- Hash verification for authenticity
- Immutable timestamps

#### E. Auto-GRN Creation
- Automatically creates GRN when all three signatures are complete
- Updates inventory in real-time
- Notifies finance department
- Sets payment status to "ready_for_payment"

### 3. Workflow

```
1. Create Site Receipt
   ├── Step 1: Basic Information (Project, Supplier, PO, Date, Vehicle)
   ├── Step 2: GPS Capture (Mandatory)
   ├── Step 3: Materials/Items (Add products & quantities)
   ├── Step 4: Upload Documents (4 mandatory documents)
   ├── Step 5: Take Photos (Optional but recommended)
   ├── Step 6: Digital Signatures (3 mandatory)
   └── Step 7: Review & Submit

2. Auto-Processing
   ├── System creates GRN automatically
   ├── Updates inventory
   ├── Notifies finance
   └── Status: "grn_created"

3. Manager Verification (Optional)
   ├── Review all information
   ├── Approve or Reject
   └── Add notes

4. Finance Processing
   └── Payment ready based on GRN
```

### 4. Routes Available

```php
GET    /site-receipts                    # List all receipts
GET    /site-receipts/create             # Create new receipt (7-step wizard)
POST   /site-receipts                    # Store new receipt
GET    /site-receipts/{id}               # Show receipt details
GET    /site-receipts/{id}/verify        # Manager verification page
POST   /site-receipts/{id}/verify        # Process verification
GET    /purchase-orders/{id}/items       # Get PO items via AJAX
```

### 5. Models & Relationships

```php
Project
  ├── hasMany: SiteReceipts
  ├── hasMany: PurchaseOrders
  └── belongsTo: Company

Supplier
  ├── hasMany: SiteReceipts
  └── hasMany: PurchaseOrders

SiteReceipt
  ├── belongsTo: Project
  ├── belongsTo: Supplier
  ├── belongsTo: PurchaseOrder
  ├── belongsTo: Engineer (User)
  ├── belongsTo: Storekeeper (User)
  ├── belongsTo: GRN
  ├── hasMany: SiteReceiptItems
  └── hasMany: SiteReceiptPhotos

GoodsReceiptNote
  ├── belongsTo: Project
  ├── belongsTo: Supplier
  ├── belongsTo: PurchaseOrder
  └── hasOne: SiteReceipt
```

### 6. Status Flow

```
draft → pending_verification → verified → grn_created
                                    ↓
                               rejected
```

### 7. Payment Status Flow

```
pending → ready_for_payment → paid
```

## Setup Instructions

### 1. Run Migrations
```bash
php artisan migrate
```

### 2. Storage Setup
```bash
php artisan storage:link
```

### 3. Configure Google Maps (Optional)
Add your Google Maps API key to `.env`:
```
GOOGLE_MAPS_API_KEY=your_api_key_here
```

### 4. Permissions
Ensure the following directories are writable:
- `storage/app/public/site-receipts/`
- `storage/app/public/site-receipts/invoices/`
- `storage/app/public/site-receipts/delivery-notes/`
- `storage/app/public/site-receipts/packing-lists/`
- `storage/app/public/site-receipts/quality-certificates/`
- `storage/app/public/site-receipts/photos/`

## API Integration Points

### Get PO Items
```javascript
fetch(`/purchase-orders/${poId}/items`)
  .then(response => response.json())
  .then(items => {
    // Auto-fill items in the form
  });
```

### GPS Capture
```javascript
navigator.geolocation.getCurrentPosition(
  (position) => {
    document.getElementById('latitude').value = position.coords.latitude;
    document.getElementById('longitude').value = position.coords.longitude;
  }
);
```

### Signature Capture
```javascript
// Uses HTML5 Canvas
const canvas = document.getElementById('signature_canvas');
const ctx = canvas.getContext('2d');
// Canvas signature data is saved as base64 PNG
const signatureData = canvas.toDataURL();
```

## Mobile Optimization

The system is fully optimized for mobile devices:
- Touch-friendly signature pads
- Camera integration for photos
- GPS auto-capture
- Responsive grid layouts
- Step-by-step wizard interface

## Security Features

1. **GPS Verification**
   - Coordinates cannot be modified after capture
   - Timestamp is immutable
   - Hash verification for photos

2. **Digital Signatures**
   - Stored as base64 images
   - Timestamped with user identification
   - Cannot be modified after saving

3. **Document Verification**
   - File type validation
   - Size limits (10MB per file)
   - Secure storage

4. **Access Control**
   - Authentication required for all operations
   - Role-based permissions (future enhancement)

## Future Enhancements

1. **Offline Mode**
   - Service workers for offline functionality
   - Local storage sync
   - Background sync when online

2. **Blockchain Integration**
   - Immutable record keeping
   - Hash verification
   - Audit trail

3. **Advanced Reporting**
   - Site receipt logs
   - GPS verification reports
   - Material tracking
   - Performance analytics

4. **Push Notifications**
   - Real-time updates
   - Approval requests
   - Status changes

5. **Barcode/QR Scanning**
   - Quick material lookup
   - Automated data entry

## Troubleshooting

### GPS Not Working
- Ensure HTTPS is enabled (required for GPS in browsers)
- Check browser permissions
- Verify device has GPS capability

### Signature Not Saving
- Ensure canvas is not empty
- Check browser compatibility (HTML5 Canvas)
- Verify JavaScript is enabled

### File Upload Fails
- Check file size limits in php.ini
- Verify storage directory permissions
- Ensure correct MIME types

## Support

For issues or questions, please contact the development team.

---

**Version:** 1.0.0  
**Last Updated:** 2026-01-02  
**Author:** CEMS Development Team
