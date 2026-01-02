# WBS (Work Breakdown Structure) for Tenders

## Overview
This feature provides a comprehensive Work Breakdown Structure (WBS) management system for tenders in the CEMS ERP system. It supports up to 5 hierarchical levels and provides automatic cost rollup calculations.

## Features

### Database Structure
- **tender_wbs table**: Main WBS items table with support for 5-level hierarchy
- **tender_wbs_boq_mapping table**: Links WBS items to BOQ (Bill of Quantities) items
- **tenders table**: Base table for tender management
- **tender_boq_items table**: Bill of Quantities items

### Key Capabilities
1. **5-Level Hierarchy**: Supports complex project structures with up to 5 levels of breakdown
2. **Cost Management**: 
   - Tracks estimated costs broken down by category (materials, labor, equipment, subcontractor)
   - Automatic cost rollup for summary items
3. **Time Management**: Estimated duration tracking in days
4. **Weight Percentages**: Assign relative weights to WBS items
5. **Summary Items**: Flag items as containers for sub-items
6. **Flexible Ordering**: Custom sort order support

### Models

#### TenderWbs Model
- Includes relationships: tender, parent, children, boqItems
- Provides cost rollup calculation method
- Includes helper methods for tree traversal
- Scopes for filtering (rootLevel, active)

#### Key Methods
- `calculateCostRollup()`: Automatically calculates total cost from children
- `getDescendants()`: Retrieves all descendant items in the tree
- `getTotalCostAttribute()`: Accessor for summing all cost categories

### Routes
All routes are prefixed with `/tenders/{tender}/wbs`:
- `GET /` - List all WBS items (index)
- `GET /create` - Create new WBS item form
- `POST /` - Store new WBS item
- `GET /{wbs}/edit` - Edit WBS item form
- `PUT /{wbs}` - Update WBS item
- `DELETE /{wbs}` - Delete WBS item
- `GET /import` - Import WBS data
- `POST /update-sort` - Update sort order

### Views

#### index.blade.php
- Interactive tree view with collapsible nodes
- Color-coded by level (5 different colors)
- Shows WBS code, name, weight, cost, and duration
- Edit and delete actions for each item
- Summary statistics at the bottom

#### create.blade.php / edit.blade.php
- Comprehensive form with sections:
  - Basic Information (code, level, parent, name, description)
  - Cost Information (materials, labor, equipment, subcontractor)
  - Schedule & Weight (duration, weight percentage, sort order)

#### import.blade.php
- Placeholder for future import functionality:
  - Import from templates
  - Import from Excel
  - Copy from previous projects

### Usage Example

```php
// Create a root level WBS item
$wbs = TenderWbs::create([
    'tender_id' => $tender->id,
    'wbs_code' => '1.0',
    'name' => 'Site Works',
    'level' => 1,
    'estimated_cost' => 1000000,
    'weight_percentage' => 15,
    'is_summary' => true,
]);

// Create a child item
$child = TenderWbs::create([
    'tender_id' => $tender->id,
    'wbs_code' => '1.1',
    'name' => 'Preparation',
    'level' => 2,
    'parent_id' => $wbs->id,
    'estimated_cost' => 500000,
    'materials_cost' => 200000,
    'labor_cost' => 300000,
]);

// Calculate cost rollup
$wbs->calculateCostRollup();
```

### Seeding Test Data
Run the seeder to create sample WBS structure:
```bash
php artisan db:seed --class=TenderWbsSeeder
```

This creates a complete 87-item WBS structure with all 5 levels demonstrated.

### UI Features
- **RTL Support**: Full right-to-left layout support
- **Color Coding**: Different background colors for each level
- **Interactive Tree**: Click to expand/collapse nodes
- **Responsive Design**: Works on all screen sizes
- **Icons**: Uses Lucide icons for visual clarity

### Future Enhancements
- Drag & drop reordering
- Excel import/export functionality
- Template library
- Integration with project scheduling
- Gantt chart visualization
- Resource allocation
- Progress tracking
