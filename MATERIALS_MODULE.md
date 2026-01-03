# Materials/Items Master Module - API Documentation

## Overview

The Materials/Items Master Module provides a complete materials catalog system with specifications, pricing, and vendor management.

## Database Schema

### Tables

1. **units** - Measurement units (pieces, kg, liters, etc.)
2. **currencies** - Currency definitions
3. **vendors** - Vendor/supplier information
4. **material_categories** - Hierarchical material categories
5. **materials** - Main materials/items catalog
6. **material_vendors** - Material-vendor pricing relationships
7. **material_specifications** - Technical specifications for materials

## API Endpoints

### Materials

#### List Materials
```
GET /api/materials
```

**Query Parameters:**
- `material_type` - Filter by type (raw_material, finished_goods, consumables, tools, equipment)
- `category_id` - Filter by category
- `is_active` - Filter by active status (true/false)
- `search` - Search in name, code, barcode, or SKU
- `per_page` - Results per page (default: 15)

**Response:**
```json
{
  "current_page": 1,
  "data": [
    {
      "id": 1,
      "material_code": "MAT-2026-0001",
      "name": "Material Name",
      "name_en": "Material Name EN",
      "description": "Material description",
      "material_type": "raw_material",
      "category_id": 1,
      "unit_id": 1,
      "standard_cost": "100.00",
      "selling_price": "150.00",
      "currency_id": 1,
      "is_active": true,
      "category": { ... },
      "unit": { ... },
      "currency": { ... }
    }
  ],
  "total": 100,
  "per_page": 15
}
```

#### Get Single Material
```
GET /api/materials/{id}
```

**Response:**
```json
{
  "id": 1,
  "material_code": "MAT-2026-0001",
  "name": "Material Name",
  "specifications": {...},
  "category": {...},
  "unit": {...},
  "currency": {...},
  "material_vendors": [...]
}
```

#### Create Material
```
POST /api/materials
```

**Request Body:**
```json
{
  "name": "Material Name (required)",
  "name_en": "Material Name EN",
  "description": "Material description",
  "material_type": "raw_material",
  "category_id": 1,
  "unit_id": 1,
  "reorder_level": 10,
  "min_stock": 5,
  "max_stock": 100,
  "standard_cost": 100,
  "selling_price": 150,
  "currency_id": 1,
  "barcode": "1234567890",
  "sku": "SKU-001",
  "specifications": {},
  "is_active": true,
  "is_stockable": true
}
```

**Material Types:**
- `raw_material` - Raw materials
- `finished_goods` - Finished products
- `consumables` - Consumable items
- `tools` - Tools and equipment
- `equipment` - Heavy equipment

#### Update Material
```
PUT /api/materials/{id}
```

**Request Body:** Same as create, all fields optional

#### Delete Material
```
DELETE /api/materials/{id}
```

**Response:**
```json
{
  "message": "Material deleted successfully"
}
```

### Material Vendors

#### Get Material Vendors
```
GET /api/materials/{id}/vendors
```

**Response:**
```json
[
  {
    "id": 1,
    "material_id": 1,
    "vendor_id": 1,
    "vendor_material_code": "VEND-MAT-001",
    "unit_price": "95.00",
    "currency_id": 1,
    "lead_time_days": 7,
    "min_order_quantity": "10.00",
    "is_preferred": true,
    "vendor": {...},
    "currency": {...}
  }
]
```

#### Add Vendor to Material
```
POST /api/materials/{id}/vendors
```

**Request Body:**
```json
{
  "vendor_id": 1,
  "vendor_material_code": "VEND-MAT-001",
  "unit_price": 95,
  "currency_id": 1,
  "lead_time_days": 7,
  "min_order_quantity": 10,
  "is_preferred": true
}
```

### Material Categories

#### List Categories
```
GET /api/material-categories
```

**Query Parameters:**
- `root_only=true` - Get only root categories (no parent)
- `tree=true` - Get hierarchical tree structure

**Response:**
```json
[
  {
    "id": 1,
    "name": "Category Name",
    "name_en": "Category Name EN",
    "parent_id": null,
    "parent": null,
    "children": [...]
  }
]
```

#### Get Single Category
```
GET /api/material-categories/{id}
```

#### Create Category
```
POST /api/material-categories
```

**Request Body:**
```json
{
  "name": "Category Name (required)",
  "name_en": "Category Name EN",
  "parent_id": null
}
```

#### Update Category
```
PUT /api/material-categories/{id}
```

#### Delete Category
```
DELETE /api/material-categories/{id}
```

**Note:** Cannot delete categories with subcategories or materials.

## Features

### Material Code Auto-Generation
Materials automatically receive a unique code in the format: `MAT-YYYY-XXXX`
- YYYY: Current year
- XXXX: Sequential number (0001, 0002, etc.)

### Hierarchical Categories
Categories support parent-child relationships for organizing materials in a tree structure.

### Multi-Currency Support
Materials and vendor pricing can use different currencies with exchange rates.

### Vendor Management
- Track multiple vendors per material
- Store vendor-specific pricing and codes
- Set preferred vendors
- Track lead times and minimum order quantities

### Inventory Integration
- Reorder levels and alerts
- Min/max stock tracking
- Stockable/non-stockable items

### Specifications Management
- Custom technical specifications
- JSON storage for flexible schema
- Quality parameters tracking

## Database Models

### Material Model
- Auto-generates material codes
- Soft deletes support
- Relationships: category, unit, currency, vendors, specifications

### MaterialCategory Model
- Hierarchical structure (parent-child)
- Relationships: parent, children, materials

### MaterialVendor Model
- Pivot model for material-vendor relationships
- Pricing and lead time information

### Unit Model
- Bilingual support (Arabic/English)
- Symbol representation

### Currency Model
- Exchange rate tracking
- Active/inactive status

### Vendor Model
- Soft deletes support
- Contact information
- Tax details

## Example Usage

### Create a Complete Material with Vendor

```bash
# 1. Create a material
curl -X POST http://localhost:8000/api/materials \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Steel Rebar 12mm",
    "name_en": "Steel Rebar 12mm",
    "material_type": "raw_material",
    "category_id": 1,
    "unit_id": 1,
    "currency_id": 1,
    "standard_cost": 500,
    "selling_price": 650,
    "reorder_level": 100,
    "min_stock": 50,
    "max_stock": 500
  }'

# 2. Add a vendor to the material
curl -X POST http://localhost:8000/api/materials/1/vendors \
  -H "Content-Type: application/json" \
  -d '{
    "vendor_id": 1,
    "unit_price": 480,
    "currency_id": 1,
    "lead_time_days": 5,
    "min_order_quantity": 10,
    "is_preferred": true
  }'
```

### Search Materials

```bash
# Search by name or code
curl "http://localhost:8000/api/materials?search=steel"

# Filter by type
curl "http://localhost:8000/api/materials?material_type=raw_material"

# Filter by category
curl "http://localhost:8000/api/materials?category_id=1"
```

## Testing

The module has been tested with:
- SQLite database migrations
- API endpoint functionality
- Material code auto-generation
- Vendor relationship management
- Category hierarchical structure

All migrations run successfully and API endpoints respond correctly with proper JSON formatting.
