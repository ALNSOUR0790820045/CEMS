# Sales Quotations Module

This module provides complete functionality for managing sales quotations in the CEMS ERP system.

## Features

- ✅ Create, read, update, and delete sales quotations
- ✅ Auto-generate quotation numbers (Format: SQ-YYYY-####)
- ✅ Support multiple quotation statuses (draft, sent, accepted, rejected, expired)
- ✅ Dynamic product items with automatic calculations
- ✅ Tax and discount calculations
- ✅ PDF export functionality
- ✅ Apple-style RTL design
- ✅ Customer and product management

## Installation

### 1. Run Migrations

```bash
php artisan migrate
```

This will create the following tables:
- `customers` - Store customer information
- `products` - Store product/service catalog
- `sales_quotations` - Store quotation headers
- `sales_quotation_items` - Store quotation line items

### 2. Seed Sample Data (Optional)

```bash
php artisan db:seed --class=CustomerSeeder
php artisan db:seed --class=ProductSeeder
```

Or run all seeders:

```bash
php artisan db:seed
```

This will create:
- 5 sample customers
- 10 sample products

## Database Schema

### Customers Table
- `id` - Primary key
- `name` - Customer name (required)
- `email` - Email address
- `phone` - Phone number
- `address` - Street address
- `city` - City
- `country` - Country code (default: SA)
- `tax_number` - Tax registration number
- `created_at`, `updated_at` - Timestamps

### Products Table
- `id` - Primary key
- `name` - Product name (required)
- `sku` - Stock keeping unit (unique, required)
- `description` - Product description
- `price` - Selling price (required)
- `cost` - Cost price
- `unit` - Unit of measurement (default: وحدة)
- `is_active` - Active status (default: true)
- `created_at`, `updated_at` - Timestamps

### Sales Quotations Table
- `id` - Primary key
- `quotation_number` - Unique quotation number (auto-generated)
- `customer_id` - Foreign key to customers
- `quotation_date` - Date of quotation
- `valid_until` - Expiry date
- `status` - Status (draft, sent, accepted, rejected, expired)
- `subtotal` - Subtotal before tax and discount
- `tax_amount` - Total tax amount
- `discount` - Total discount amount
- `total` - Final total amount
- `terms_conditions` - Terms and conditions text
- `notes` - Additional notes
- `created_by` - Foreign key to users (creator)
- `created_at`, `updated_at` - Timestamps

### Sales Quotation Items Table
- `id` - Primary key
- `sales_quotation_id` - Foreign key to sales_quotations
- `product_id` - Foreign key to products
- `quantity` - Item quantity
- `unit_price` - Price per unit
- `tax_rate` - Tax percentage (default: 15%)
- `tax_amount` - Calculated tax amount
- `discount` - Discount amount
- `total` - Line item total
- `created_at`, `updated_at` - Timestamps

## Usage

### Access the Module

Navigate to the Sales Quotations module from the main navigation:
**المالية** → **المبيعات** → **عروض أسعار المبيعات**

Or directly via URL: `/sales-quotations`

### Create a New Quotation

1. Click **إضافة عرض سعر جديد** (Add New Quotation)
2. Fill in the basic information:
   - Customer (required)
   - Quotation date (required)
   - Valid until date (required)
   - Status (required, default: draft)
   - Terms and conditions (optional)
   - Notes (optional)
3. Add products:
   - Click **إضافة منتج** (Add Product) to add product lines
   - Select product from dropdown
   - Enter quantity (required)
   - Unit price will auto-populate from product price
   - Enter tax rate (default: 15%)
   - Enter discount amount (optional)
   - Line total is calculated automatically
4. Click **حفظ** (Save)

### View Quotation

Click **عرض** (View) button on any quotation to see:
- Complete quotation details
- Customer information
- Product line items
- Calculated totals (subtotal, tax, discount, total)
- Terms and conditions
- Notes

### Export to PDF

From the quotation view page, click **تحميل PDF** (Download PDF) to generate and download a PDF version of the quotation.

### Edit Quotation

1. Click **تعديل** (Edit) button
2. Modify any fields as needed
3. Add/remove product lines
4. Click **حفظ** (Save)

### Delete Quotation

Click **حذف** (Delete) button and confirm the deletion.

## Quotation Number Format

Quotation numbers are automatically generated in the format:
```
SQ-YYYY-####
```

Where:
- `SQ` = Sales Quotation prefix
- `YYYY` = Current year (e.g., 2026)
- `####` = Sequential number padded to 4 digits (e.g., 0001)

Example: `SQ-2026-0001`

## Status Types

- **مسودة (draft)** - Initial state, quotation is being prepared
- **مرسل (sent)** - Quotation has been sent to customer
- **مقبول (accepted)** - Customer has accepted the quotation
- **مرفوض (rejected)** - Customer has rejected the quotation
- **منتهي (expired)** - Quotation validity period has expired

## Calculations

### Line Item Total
```
Subtotal = Quantity × Unit Price
After Discount = Subtotal - Discount
Tax Amount = After Discount × (Tax Rate / 100)
Line Total = After Discount + Tax Amount
```

### Quotation Totals
```
Subtotal = Sum of all line item subtotals
Total Discount = Sum of all line item discounts
Total Tax = Sum of all line item tax amounts
Grand Total = Subtotal - Total Discount + Total Tax
```

## API Routes

All routes are protected by authentication middleware.

| Method | URI | Action | Name |
|--------|-----|--------|------|
| GET | /sales-quotations | List all quotations | sales-quotations.index |
| GET | /sales-quotations/create | Show create form | sales-quotations.create |
| POST | /sales-quotations | Store new quotation | sales-quotations.store |
| GET | /sales-quotations/{id} | Show quotation | sales-quotations.show |
| GET | /sales-quotations/{id}/edit | Show edit form | sales-quotations.edit |
| PUT/PATCH | /sales-quotations/{id} | Update quotation | sales-quotations.update |
| DELETE | /sales-quotations/{id} | Delete quotation | sales-quotations.destroy |
| GET | /sales-quotations/{id}/pdf | Export to PDF | sales-quotations.pdf |

## Models and Relationships

### SalesQuotation Model
```php
// Relationships
belongsTo Customer
hasMany SalesQuotationItem (items)
belongsTo User (creator)

// Methods
generateQuotationNumber() - Static method to generate next quotation number
```

### SalesQuotationItem Model
```php
// Relationships
belongsTo SalesQuotation
belongsTo Product
```

### Customer Model
```php
// Relationships
hasMany SalesQuotation
```

### Product Model
```php
// No direct relationships with quotations
```

## Validation Rules

### Quotation Validation
- `customer_id` - required, must exist in customers table
- `quotation_date` - required, valid date
- `valid_until` - required, valid date, must be after quotation_date
- `status` - required, must be one of: draft, sent, accepted, rejected, expired
- `terms_conditions` - optional, string
- `notes` - optional, string
- `items` - required, array, minimum 1 item

### Item Validation
- `product_id` - required, must exist in products table
- `quantity` - required, numeric, minimum 0.001
- `unit_price` - required, numeric, minimum 0
- `tax_rate` - required, numeric, between 0 and 100
- `discount` - optional, numeric, minimum 0

## Troubleshooting

### Migration Errors

If you encounter foreign key constraint errors:
1. Ensure migrations run in order (customers and products before sales_quotations)
2. Check database connection in `.env` file
3. Clear migration cache: `php artisan migrate:fresh`

### PDF Generation Issues

If PDF export fails:
1. Ensure `barryvdh/laravel-dompdf` package is installed
2. Check PHP memory limit (increase if needed)
3. Verify write permissions on storage directory

### JavaScript Errors

If dynamic product form doesn't work:
1. Check browser console for JavaScript errors
2. Ensure Lucide icons are loading
3. Clear browser cache

## Support

For issues or questions about this module, please contact the development team or refer to the main CEMS ERP documentation.
