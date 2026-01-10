# EOT & Prolongation Costs Management System

## Overview
This system manages Extension of Time (EOT) claims and prolongation costs in construction projects according to FIDIC standards.

## Features

### 1. Database Structure
- **Projects**: Main project information
- **Project Activities**: Gantt chart activities
- **Time Bar Claims**: Time bar notifications
- **EOT Claims**: Extension of Time claims with full workflow
- **Prolongation Cost Items**: Detailed cost breakdown
- **EOT Affected Activities**: Activities impacted by delays

### 2. EOT Claim Management
- Create EOT claims with FIDIC cause categories
- Track event details, impact, and justification
- Request specific number of days extension
- Link to Time Bar claims
- Multi-level approval workflow (Engineer → Consultant → Client)
- Support for partial approvals

### 3. Prolongation Costs
- Detailed cost breakdown by category:
  - Site staff
  - Site facilities
  - Equipment rental
  - Utilities
  - Security
  - Insurance
  - Head office costs
  - Financing costs
- Daily rate calculation
- Supporting documentation

### 4. Views
- **Dashboard**: KPIs, charts, recent claims
- **Index**: List all claims with filtering
- **Create**: Multi-step form for new claims
- **Show**: Detailed claim view with timeline
- **Edit**: Modify draft claims
- **Approve**: Approval workflow interface
- **Report**: Comprehensive analytics and reports

### 5. Approval Workflow
1. Engineer prepares claim (Draft)
2. Submit for review (Submitted)
3. Consultant reviews (Under Review - Consultant)
4. Client approves (Under Review - Client)
5. Final status: Approved/Partially Approved/Rejected/Disputed

## Installation & Testing

### Prerequisites
- PHP 8.2+
- PostgreSQL or MySQL
- Composer
- Laravel 12

### Setup

1. **Install dependencies**
```bash
composer install
```

2. **Configure database**
Edit `.env` file with your database credentials:
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=cems_erp
DB_USERNAME=root
DB_PASSWORD=
```

3. **Run migrations**
```bash
php artisan migrate
```

4. **Create test data (optional)**
You can create test projects and users to test the system:
```bash
php artisan tinker
```

Then in tinker:
```php
// Create a test project
$project = \App\Models\Project::create([
    'name' => 'مشروع الطريق الدائري',
    'code' => 'PROJ-2026-001',
    'contract_start_date' => '2026-01-01',
    'original_completion_date' => '2027-12-31',
    'current_completion_date' => '2027-12-31',
    'company_id' => 1, // Make sure you have a company
]);

// Create test activities
$activity = \App\Models\ProjectActivity::create([
    'project_id' => $project->id,
    'activity_code' => 'ACT-001',
    'activity_name' => 'أعمال الحفر',
    'planned_start_date' => '2026-02-01',
    'planned_end_date' => '2026-04-30',
    'is_critical_path' => true,
]);
```

5. **Start the server**
```bash
php artisan serve
```

6. **Access EOT system**
Navigate to:
- Dashboard: `http://localhost:8000/eot/dashboard`
- Create Claim: `http://localhost:8000/eot/create`
- List Claims: `http://localhost:8000/eot`
- Reports: `http://localhost:8000/eot/report`

### Testing Workflow

1. **Create a new EOT Claim**
   - Go to `/eot/create`
   - Fill in all required fields
   - Select a FIDIC cause category
   - Enter requested days
   - Save as draft

2. **Submit for Review**
   - View the claim from index or dashboard
   - Click "Submit" button
   - Status changes to "Submitted"

3. **Review & Approve (as Consultant)**
   - Go to approval form
   - Review claim details
   - Choose: Full Approval, Partial Approval, or Reject
   - Enter comments
   - Submit decision

4. **Final Approval (as Client)**
   - Similar process to consultant review
   - Final status is set

5. **View Reports**
   - Go to `/eot/report`
   - See statistics by cause, status
   - View detailed claim table
   - Check approval rates

## Routes

```php
// Dashboard
GET /eot/dashboard

// CRUD Operations
GET /eot                    - List all claims
GET /eot/create            - Create form
POST /eot                  - Store new claim
GET /eot/{id}              - Show claim details
GET /eot/{id}/edit         - Edit form
PUT /eot/{id}              - Update claim
DELETE /eot/{id}           - Delete draft claim

// Workflow
POST /eot/{id}/submit      - Submit for review
GET /eot/{id}/approve      - Approval form
POST /eot/{id}/approve     - Process approval

// Reports
GET /eot/report            - Generate reports
```

## FIDIC Cause Categories

The system supports the following FIDIC-based cause categories:

1. **Client Delay** (FIDIC 8.4) - تأخير المالك
2. **Consultant Delay** - تأخير الاستشاري
3. **Variations** (FIDIC 13) - أوامر تغييرية
4. **Unforeseeable Conditions** (FIDIC 4.12) - ظروف غير منظورة
5. **Force Majeure** (FIDIC 19) - قوة قاهرة
6. **Weather** - طقس استثنائي
7. **Delays by Others** - تأخير الآخرين
8. **Suspension** (FIDIC 8.8) - إيقاف الأعمال
9. **Late Drawings** - تأخر المخططات
10. **Late Approvals** - تأخر الموافقات
11. **Other** - أخرى

## Status Flow

```
Draft → Submitted → Under Review (Consultant) → Under Review (Client) → Approved/Rejected/Partially Approved
                                                                      → Disputed
```

## Features Implemented

✅ Database migrations for all tables
✅ Eloquent models with relationships
✅ Full CRUD controller
✅ Dashboard with KPIs and charts
✅ Multi-step claim creation form
✅ Detailed claim view with timeline
✅ Approval workflow
✅ Comprehensive reports
✅ RTL support (Arabic)
✅ Professional UI design
✅ FIDIC compliance

## Notes

- Only draft claims can be edited or deleted
- Submitted claims cannot be modified
- Approval workflow requires proper user roles
- All dates are tracked with timestamps
- Supporting documents can be attached (field available)
- Critical path impact is tracked
- Prolongation costs are optional but calculated automatically

## Future Enhancements

- File upload functionality for supporting documents
- Email notifications for workflow steps
- Advanced filtering and search
- Export to PDF/Excel
- Gantt chart integration
- Cost item management UI
- Activity management interface
- Dashboard charts (using Chart.js or similar)
- Multi-language support beyond Arabic

## Support

For issues or questions, please refer to the project documentation or contact the development team.
