# Punch List / Snag List Module Documentation

## Overview
The Punch List module (also known as Snag List or Defects List) is a comprehensive system for managing construction project defects, incomplete work, and quality issues during pre-handover, handover, and defects liability periods.

## Features

### Core Functionality
1. **Punch List Management**
   - Create and manage multiple punch lists per project
   - Categorize by list type (pre-handover, handover, defects liability, final)
   - Track location details (area/zone, building, floor)
   - Assign to contractors and subcontractors
   - Monitor completion progress and statistics

2. **Punch Item Tracking**
   - Document individual defects/issues with detailed descriptions
   - Classify by category (defect, incomplete, damage, missing, wrong)
   - Rate severity (minor, major, critical)
   - Assign priority levels (low, medium, high, urgent)
   - Track by discipline (architectural, structural, electrical, mechanical, etc.)
   - Upload before and after photos
   - Set due dates and track completion

3. **Workflow Management**
   - Status progression: Open → In Progress → Completed → Verified → Closed
   - Rejection capability with reasons
   - Reopen functionality
   - Automated notifications at each stage
   - Complete audit trail

4. **Collaboration Features**
   - Comments and discussions on each item
   - Multiple comment types (note, query, response, rejection)
   - File attachments support
   - Multiple stakeholder involvement

5. **Reporting & Analytics**
   - Summary reports by project
   - Detailed reports with full item listings
   - Contractor-wise breakdown
   - Overdue items tracking
   - Statistical analysis (by discipline, severity, priority)
   - Completion trends
   - Aging analysis

6. **Templates & Categories**
   - Reusable punch item templates
   - Hierarchical category structure
   - Discipline-specific templates
   - Quick application to punch lists

7. **Dashboard & Visualizations**
   - Project-level dashboards
   - Real-time statistics
   - Breakdown by discipline, contractor, location
   - Aging analysis
   - Trend charts

## Database Schema

### Tables

#### 1. `punch_lists`
Main table for punch list records.

**Key Fields:**
- `list_number`: Auto-generated (PL-YYYY-XXXX)
- `project_id`: Foreign key to projects
- `list_type`: pre_handover, handover, defects_liability, final
- `discipline`: architectural, structural, mep, civil, landscape
- `contractor_id`, `subcontractor_id`: Foreign keys to vendors
- `status`: draft, issued, in_progress, completed, verified, closed
- `total_items`, `completed_items`, `pending_items`, `completion_percentage`: Auto-calculated statistics

#### 2. `punch_items`
Individual punch items/defects.

**Key Fields:**
- `item_number`: Auto-generated per list
- `punch_list_id`: Foreign key to punch_lists
- `category`: defect, incomplete, damage, missing, wrong
- `severity`: minor, major, critical
- `status`: open, in_progress, completed, verified, rejected
- `priority`: low, medium, high, urgent
- `photos`, `completion_photos`: JSON arrays
- `due_date`, `completed_date`, `verified_date`

#### 3. `punch_item_comments`
Comments and discussions on items.

**Key Fields:**
- `punch_item_id`: Foreign key to punch_items
- `comment_type`: note, query, response, rejection
- `attachments`: JSON array

#### 4. `punch_item_history`
Complete audit trail of all changes.

**Key Fields:**
- `action`: created, assigned, status_changed, comment_added, photo_added, verified, rejected
- `old_value`, `new_value`
- `performed_by_id`, `performed_at`

#### 5. `punch_templates`
Reusable templates for common punch items.

**Key Fields:**
- `items`: JSON array of template items
- `discipline`, `category`: For filtering

#### 6. `punch_categories`
Hierarchical categorization system.

**Key Fields:**
- `parent_id`: Self-referencing for hierarchy
- `color`: Visual identification

#### 7. `punch_reports`
Generated report metadata.

**Key Fields:**
- `report_number`: Auto-generated (PLR-YYYY-XXXX)
- `report_type`: summary, detailed, location, discipline, contractor
- `filters`: JSON stored criteria

#### 8. `punch_statistics`
Daily/periodic statistical snapshots.

**Key Fields:**
- `by_discipline`, `by_severity`, `by_contractor`: JSON breakdowns
- `avg_resolution_days`: Performance metric

## API Endpoints

### Punch Lists

```
GET    /api/punch-lists                           - List all punch lists
POST   /api/punch-lists                           - Create new punch list
GET    /api/punch-lists/{id}                      - Get punch list details
PUT    /api/punch-lists/{id}                      - Update punch list
DELETE /api/punch-lists/{id}                      - Delete punch list
GET    /api/punch-lists/project/{projectId}       - Get lists by project
POST   /api/punch-lists/{id}/issue                - Issue punch list
POST   /api/punch-lists/{id}/verify               - Verify punch list
POST   /api/punch-lists/{id}/close                - Close punch list
GET    /api/punch-lists/{id}/pdf                  - Generate PDF
POST   /api/punch-lists/{id}/send-notification    - Send notifications
```

### Punch Items

```
GET    /api/punch-items                           - List all items
POST   /api/punch-items                           - Create new item
GET    /api/punch-items/{id}                      - Get item details
PUT    /api/punch-items/{id}                      - Update item
DELETE /api/punch-items/{id}                      - Delete item
GET    /api/punch-items/list/{listId}             - Get items by list
POST   /api/punch-items/{id}/assign               - Assign to user
POST   /api/punch-items/{id}/complete             - Mark as completed
POST   /api/punch-items/{id}/verify               - Verify completion
POST   /api/punch-items/{id}/reject               - Reject completion
POST   /api/punch-items/{id}/reopen               - Reopen item
POST   /api/punch-items/{id}/photos               - Upload photos
POST   /api/punch-items/{id}/completion-photos    - Upload completion photos
POST   /api/punch-items/bulk-update               - Bulk update items
POST   /api/punch-items/bulk-assign               - Bulk assign items
GET    /api/punch-items/{itemId}/history          - Get item history
```

### Comments

```
GET    /api/punch-items/{itemId}/comments         - Get comments
POST   /api/punch-items/{itemId}/comments         - Add comment
```

### Templates

```
GET    /api/punch-templates                       - List templates
POST   /api/punch-templates                       - Create template
GET    /api/punch-templates/{id}                  - Get template
PUT    /api/punch-templates/{id}                  - Update template
DELETE /api/punch-templates/{id}                  - Delete template
POST   /api/punch-lists/{listId}/apply-template/{templateId} - Apply template
```

### Categories

```
GET    /api/punch-categories                      - List categories
POST   /api/punch-categories                      - Create category
GET    /api/punch-categories/{id}                 - Get category
PUT    /api/punch-categories/{id}                 - Update category
DELETE /api/punch-categories/{id}                 - Delete category
GET    /api/punch-categories/tree                 - Get category tree
```

### Dashboard

```
GET    /api/punch-dashboard/project/{projectId}   - Project dashboard
GET    /api/punch-dashboard/summary/{projectId}   - Summary statistics
GET    /api/punch-dashboard/by-discipline/{projectId} - By discipline
GET    /api/punch-dashboard/by-contractor/{projectId} - By contractor
GET    /api/punch-dashboard/by-location/{projectId}   - By location
GET    /api/punch-dashboard/aging/{projectId}     - Aging analysis
GET    /api/punch-dashboard/trend/{projectId}     - Trend data
```

### Reports

```
GET    /api/reports/punch-summary/{projectId}     - Summary report
GET    /api/reports/punch-detailed/{projectId}    - Detailed report
GET    /api/reports/punch-by-contractor/{projectId} - By contractor
GET    /api/reports/punch-overdue/{projectId}     - Overdue items
GET    /api/reports/punch-statistics/{projectId}  - Statistics
POST   /api/reports/punch-export/{projectId}      - Export report
```

## Models

### PunchList
**Relationships:**
- belongsTo: Project, Vendor (contractor, subcontractor), User (inspector, issuedBy, verifiedBy, closedBy), Company
- hasMany: PunchItem

**Methods:**
- `updateStatistics()`: Recalculates total, completed, pending items and completion percentage

### PunchItem
**Relationships:**
- belongsTo: PunchList, User (assignedTo, verifiedBy)
- hasMany: PunchItemComment, PunchItemHistory

**Methods:**
- `isOverdue()`: Checks if item is past due date
- `addHistory()`: Adds history entry for tracking changes

### PunchItemComment
**Relationships:**
- belongsTo: PunchItem, User (commentedBy)

### PunchItemHistory
**Relationships:**
- belongsTo: PunchItem, User (performedBy)

### PunchTemplate
**Relationships:**
- belongsTo: Company

### PunchCategory
**Relationships:**
- belongsTo: PunchCategory (parent), Company
- hasMany: PunchCategory (children)

### PunchReport
**Relationships:**
- belongsTo: Project, User (generatedBy)

### PunchStatistic
**Relationships:**
- belongsTo: Project

## Workflow

### Punch List Workflow
```
Draft → Issued → In Progress → Completed → Verified → Closed
```

### Punch Item Workflow
```
Open → In Progress → Completed → Verified
                  ↓
               Rejected → In Progress (Reopen)
```

## Usage Examples

### Creating a Punch List

```php
POST /api/punch-lists
{
    "project_id": 1,
    "name": "Building A Pre-Handover Inspection",
    "description": "Pre-handover inspection for Building A",
    "list_type": "pre_handover",
    "area_zone": "Zone 1",
    "building": "Building A",
    "floor": "Ground Floor",
    "discipline": "architectural",
    "contractor_id": 5,
    "inspection_date": "2026-01-15",
    "inspector_id": 10,
    "target_completion_date": "2026-02-15"
}
```

### Adding a Punch Item

```php
POST /api/punch-items
{
    "punch_list_id": 1,
    "location": "Room 101",
    "description": "Wall paint chipped in multiple locations",
    "category": "defect",
    "severity": "minor",
    "discipline": "architectural",
    "priority": "medium",
    "due_date": "2026-01-20",
    "cost_to_rectify": 500.00
}
```

### Completing an Item

```php
POST /api/punch-items/1/complete
{
    "completion_remarks": "Wall repainted and finished"
}
```

### Verifying an Item

```php
POST /api/punch-items/1/verify
```

### Applying a Template

```php
POST /api/punch-lists/1/apply-template/3
```

## Testing

The module includes comprehensive feature tests:

### PunchListTest
- test_can_create_punch_list
- test_can_list_punch_lists
- test_can_show_punch_list
- test_can_update_punch_list
- test_can_delete_punch_list
- test_can_issue_punch_list
- test_can_get_punch_lists_by_project
- test_list_statistics_update_when_items_added

### PunchItemTest
- test_can_create_punch_item
- test_can_assign_punch_item
- test_can_complete_punch_item
- test_can_verify_punch_item
- test_can_reject_punch_item
- test_can_reopen_punch_item
- test_can_add_comment_to_item
- test_can_get_item_comments
- test_can_get_item_history
- test_can_bulk_assign_items
- test_item_workflow_from_open_to_verified
- test_cannot_verify_non_completed_item

Run tests:
```bash
php artisan test --filter PunchListTest
php artisan test --filter PunchItemTest
```

## Installation & Setup

1. Run migrations:
```bash
php artisan migrate
```

2. Seed sample data (if seeders are created):
```bash
php artisan db:seed --class=PunchListSeeder
```

3. Configure storage for photo uploads:
```bash
php artisan storage:link
```

## Best Practices

1. **Always track history**: Use `addHistory()` method when making significant changes to items
2. **Update statistics**: Call `updateStatistics()` on PunchList after bulk operations
3. **Photo management**: Store photos in separate directories per item
4. **Workflow validation**: Always validate status transitions before allowing state changes
5. **Bulk operations**: Use bulk endpoints for efficiency when updating multiple items
6. **Notifications**: Implement proper notification system for stakeholder updates
7. **PDF generation**: Ensure proper formatting and branding in generated PDFs

## Security Considerations

1. All API endpoints require authentication (sanctum middleware)
2. Implement proper authorization checks for project access
3. Validate file uploads (type, size) for photos
4. Sanitize user inputs to prevent XSS
5. Use soft deletes to maintain audit trail
6. Implement role-based permissions for sensitive operations

## Future Enhancements

1. Mobile app support for on-site inspections
2. Offline mode for areas with poor connectivity
3. OCR for extracting text from photos
4. AI-powered defect detection
5. Integration with BIM models
6. Automated progress tracking via IoT sensors
7. Multi-language support
8. Email/SMS notifications
9. QR code generation for quick access
10. Integration with project scheduling tools

## Support

For issues or questions regarding the Punch List module, please contact the development team or refer to the main CEMS documentation.
