# Claims Management Module

## Overview
وحدة متكاملة لإدارة المطالبات التعاقدية في المشاريع الإنشائية.

## Database Tables Created

1. **projects** - Projects table (prerequisite)
2. **contracts** - Contracts table (prerequisite)
3. **claims** - Main claims table
4. **claim_events** - Claim events tracking
5. **claim_documents** - Claim documents management
6. **claim_timeline** - Claim timeline/history
7. **claim_correspondence** - Claim correspondence tracking

## Models

- `App\Models\Project`
- `App\Models\Contract`
- `App\Models\Claim`
- `App\Models\ClaimEvent`
- `App\Models\ClaimDocument`
- `App\Models\ClaimTimeline`
- `App\Models\ClaimCorrespondence`

## Routes

### Web Routes (UI)
- `GET /claims` - List all claims
- `GET /claims/create` - Show create form
- `POST /claims` - Store new claim
- `GET /claims/{id}` - Show claim details
- `GET /claims/{id}/edit` - Show edit form
- `PUT /claims/{id}` - Update claim
- `DELETE /claims/{id}` - Delete claim

### API Routes
- `POST /claims/{id}/send-notice` - Send notice
- `POST /claims/{id}/submit` - Submit claim
- `POST /claims/{id}/resolve` - Resolve claim
- `GET /claims/{id}/export` - Export claim as PDF
- `GET /projects/{id}/claims` - Get claims for project
- `GET /claims-statistics` - Get statistics

## Views

1. **claims/index.blade.php** - Claims list with filtering and pagination
2. **claims/create.blade.php** - Create new claim form
3. **claims/edit.blade.php** - Edit claim form with all fields
4. **claims/show.blade.php** - Detailed view with timeline and quick actions
5. **claims/report.blade.php** - PDF export template

## Features

✅ Full CRUD operations
✅ Claim status workflow (identified → notice_sent → submitted → settled)
✅ Timeline tracking with automatic logging
✅ Multiple claim types and causes
✅ Financial and time tracking (claimed, assessed, approved)
✅ Document management structure
✅ Correspondence tracking
✅ PDF export functionality
✅ Project and contract associations
✅ RTL support for Arabic
✅ Responsive design

## Claim Status Workflow

1. **identified** - تم تحديده
2. **notice_sent** - تم إرسال الإشعار
3. **documenting** - قيد التوثيق
4. **submitted** - مقدم
5. **under_review** - قيد المراجعة
6. **negotiating** - قيد التفاوض
7. **approved** - معتمد
8. **partially_approved** - معتمد جزئياً
9. **rejected** - مرفوض
10. **withdrawn** - مسحوب
11. **arbitration** - تحكيم
12. **litigation** - تقاضي
13. **settled** - تمت التسوية

## Claim Types

- **time_extension** - تمديد وقت
- **cost_compensation** - تعويض مالي
- **time_and_cost** - وقت ومال
- **acceleration** - تسريع
- **disruption** - إعاقة
- **prolongation** - إطالة
- **loss_of_productivity** - فقدان الإنتاجية

## Claim Causes

- **client_delay** - تأخير العميل
- **design_changes** - تغييرات التصميم
- **differing_conditions** - ظروف مختلفة
- **force_majeure** - قوة قاهرة
- **suspension** - إيقاف
- **late_payment** - تأخر الدفع
- **acceleration_order** - أمر بالتسريع
- **other** - أخرى

## Installation

1. Run migrations:
```bash
php artisan migrate
```

2. Access the Claims module from the navigation menu:
   المالية → العقود → المطالبات

## Usage

### Creating a Claim

1. Navigate to Claims → Create New
2. Fill in the required fields:
   - Project (required)
   - Contract (optional)
   - Title and Description
   - Type and Cause
   - Claimed amounts and days
   - Event dates
   - Priority level
3. Submit the form

### Managing Claims

- View all claims in the index page
- Click on a claim to view details and timeline
- Edit claims to update information
- Use quick actions to:
  - Send notice
  - Submit claim
  - Resolve claim
- Export claims to PDF

### Timeline

Every action on a claim is automatically logged in the timeline, including:
- Creation
- Status changes
- Updates
- Resolutions

## Technical Details

### Automatic Claim Number Generation

Claim numbers are automatically generated in the format:
```
CLM-{PROJECT_CODE}-{SEQUENCE_NUMBER}
```
Example: `CLM-PRJ001-001`

### Database Relationships

- Claim belongs to Project
- Claim belongs to Contract (optional)
- Claim belongs to User (prepared_by)
- Claim belongs to User (reviewed_by, optional)
- Claim has many ClaimEvents
- Claim has many ClaimDocuments
- Claim has many ClaimTimeline entries
- Claim has many ClaimCorrespondence

### Security

- All routes are protected with authentication middleware
- CSRF protection on all forms
- Soft deletes enabled for data retention
- Cascade delete for related records

## Future Enhancements

The module structure supports future additions:
- Document upload functionality
- Email notifications
- Advanced reporting and analytics
- Claim approval workflows
- Integration with accounting module
- Mobile app support

## Support

For issues or questions about the Claims Management Module, please refer to the codebase or contact the development team.
