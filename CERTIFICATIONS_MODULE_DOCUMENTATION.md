# Certifications & Compliance Module - Implementation Documentation

## Overview
This document provides comprehensive information about the Certifications & Compliance Module implemented for the CEMS (Construction Enterprise Management System).

## Database Schema

### Tables Created

#### 1. `certifications`
Stores certification records for various entities (company, employee, equipment, material, contractor).

**Columns:**
- `id` - Primary key
- `certification_code` - Unique auto-generated code (format: CERT-YYYY-XXXX)
- `certification_name` - Name of the certification
- `certification_type` - Enum: company, employee, equipment, material, contractor
- `entity_type` - Polymorphic type for the certified entity
- `entity_id` - Polymorphic ID for the certified entity
- `issuing_authority` - Authority that issued the certification
- `certificate_number` - Certificate number (optional)
- `issue_date` - Date the certification was issued
- `expiry_date` - Expiration date
- `is_renewable` - Boolean, default true
- `renewal_period_days` - Number of days for renewal period
- `status` - Enum: active, expired, suspended, renewed
- `certificate_file_path` - Path to uploaded certificate file
- `alert_before_days` - Days before expiry to send alerts (default: 30)
- `last_alert_sent` - Date of last alert sent
- `notes` - Additional notes
- `company_id` - Foreign key to companies table
- `timestamps` - Created at, updated at

**Indexes:**
- Unique index on `certification_code`
- Composite index on `entity_type` and `entity_id`

#### 2. `compliance_requirements`
Defines regulatory and compliance requirements.

**Columns:**
- `id` - Primary key
- `requirement_code` - Unique requirement identifier
- `requirement_name` - Name of the requirement
- `regulatory_body` - Regulatory authority
- `requirement_type` - Enum: license, permit, certification, audit, reporting
- `applicable_to` - Enum: company, project, department, employee
- `description` - Detailed description
- `frequency` - Enum: one_time, annual, quarterly, monthly
- `is_mandatory` - Boolean, default true
- `penalty_description` - Description of penalties for non-compliance
- `company_id` - Foreign key to companies table
- `timestamps`

#### 3. `compliance_tracking`
Tracks compliance status and progress.

**Columns:**
- `id` - Primary key
- `compliance_requirement_id` - Foreign key to compliance_requirements
- `entity_type` - Polymorphic type for the entity being tracked
- `entity_id` - Polymorphic ID for the entity being tracked
- `due_date` - Compliance due date
- `completion_date` - Date compliance was completed
- `status` - Enum: pending, in_progress, completed, overdue, waived
- `responsible_person_id` - Foreign key to users table (optional)
- `evidence_file_path` - Path to evidence document
- `remarks` - Additional remarks
- `company_id` - Foreign key to companies table
- `timestamps`

**Indexes:**
- Composite index on `entity_type` and `entity_id`

## Models

### Certification Model
**Location:** `app/Models/Certification.php`

**Features:**
- Auto-generates certification codes in format CERT-YYYY-XXXX
- Polymorphic relationship to any entity type
- Belongs to Company
- Scopes:
  - `active()` - Get only active certifications
  - `expiring($days)` - Get certifications expiring within specified days
  - `expired()` - Get expired certifications
- Accessors:
  - `is_expired` - Boolean, checks if certification has expired
  - `is_expiring_soon` - Boolean, checks if expiring within alert period
  - `days_until_expiry` - Integer, days until expiration

### ComplianceRequirement Model
**Location:** `app/Models/ComplianceRequirement.php`

**Features:**
- Belongs to Company
- Has many ComplianceTracking records
- Scopes:
  - `mandatory()` - Get only mandatory requirements
  - `byType($type)` - Filter by requirement type
  - `applicableTo($type)` - Filter by applicable entity type

### ComplianceTracking Model
**Location:** `app/Models/ComplianceTracking.php`

**Features:**
- Auto-updates status to 'overdue' if past due date
- Belongs to Company, ComplianceRequirement, and User (responsible person)
- Polymorphic relationship to tracked entity
- Scopes:
  - `pending()` - Get pending trackings
  - `overdue()` - Get overdue trackings
  - `completed()` - Get completed trackings
  - `inProgress()` - Get in-progress trackings
- Accessors:
  - `is_overdue` - Boolean, checks if tracking is overdue
  - `days_until_due` - Integer, days until due date

## Controllers

### CertificationController
**Location:** `app/Http/Controllers/CertificationController.php`

**Endpoints:**
- `index()` - List all certifications with filters (company, type, status)
- `store()` - Create new certification with file upload support
- `show($id)` - Get certification details
- `update($id)` - Update certification
- `destroy($id)` - Delete certification
- `expiring()` - Get certifications expiring soon
- `renew($id)` - Renew a certification (marks old as renewed, creates new)

**Validation:**
- File uploads: PDF, JPG, JPEG, PNG (max 5MB)
- Required fields validated per schema
- Date validation (expiry must be after issue date)

### ComplianceRequirementController
**Location:** `app/Http/Controllers/ComplianceRequirementController.php`

**Endpoints:**
- `index()` - List all requirements with filters
- `store()` - Create new requirement
- `show($id)` - Get requirement details with trackings
- `update($id)` - Update requirement
- `destroy($id)` - Delete requirement

### ComplianceTrackingController
**Location:** `app/Http/Controllers/ComplianceTrackingController.php`

**Endpoints:**
- `index()` - List all trackings with filters
- `store()` - Create new tracking with evidence upload
- `show($id)` - Get tracking details
- `update($id)` - Update tracking
- `destroy($id)` - Delete tracking
- `overdue()` - Get overdue trackings

**File Upload:**
- Evidence files: PDF, JPG, JPEG, PNG, DOC, DOCX (max 10MB)

### ComplianceReportController
**Location:** `app/Http/Controllers/ComplianceReportController.php`

**Endpoints:**
- `dashboard()` - Get comprehensive dashboard statistics
  - Certification statistics (total, active, expiring, expired, by type)
  - Compliance tracking statistics (total, pending, in progress, completed, overdue, by status)
  - Requirements statistics (total, mandatory, by type, by frequency)
  - Recent expiring certifications (top 10)
  - Recent overdue trackings (top 10)
- `certificationRegister()` - Get certification register report with filters and summary
- `complianceStatus()` - Get compliance status report with filters and summary

## API Routes

All routes are prefixed with `/api/` and configured in `routes/api.php`

### Certifications
```
GET     /api/certifications              - List certifications
POST    /api/certifications              - Create certification
GET     /api/certifications/expiring     - Get expiring certifications
GET     /api/certifications/{id}         - Get certification details
PUT     /api/certifications/{id}         - Update certification
PATCH   /api/certifications/{id}         - Partial update certification
DELETE  /api/certifications/{id}         - Delete certification
POST    /api/certifications/{id}/renew   - Renew certification
```

### Compliance Requirements
```
GET     /api/compliance-requirements           - List requirements
POST    /api/compliance-requirements           - Create requirement
GET     /api/compliance-requirements/{id}      - Get requirement details
PUT     /api/compliance-requirements/{id}      - Update requirement
PATCH   /api/compliance-requirements/{id}      - Partial update requirement
DELETE  /api/compliance-requirements/{id}      - Delete requirement
```

### Compliance Tracking
```
GET     /api/compliance-tracking            - List trackings
POST    /api/compliance-tracking            - Create tracking
GET     /api/compliance-tracking/overdue    - Get overdue trackings
GET     /api/compliance-tracking/{id}       - Get tracking details
PUT     /api/compliance-tracking/{id}       - Update tracking
PATCH   /api/compliance-tracking/{id}       - Partial update tracking
DELETE  /api/compliance-tracking/{id}       - Delete tracking
```

### Reports
```
GET     /api/reports/compliance-dashboard      - Get dashboard statistics
GET     /api/reports/certification-register    - Get certification register
GET     /api/reports/compliance-status         - Get compliance status report
```

## Testing

### Test Suite
**Location:** `tests/Feature/CertificationTest.php`

**Tests Included:**
1. `test_certification_code_is_auto_generated()` - Verifies auto-generation of certification codes
2. `test_certification_expiring_scope()` - Tests the expiring scope functionality
3. `test_certification_belongs_to_company()` - Validates company relationship

**Test Results:**
- ✅ 3 tests passed
- ✅ 6 assertions successful
- ✅ All migrations run successfully

### Factory
**Location:** `database/factories/CompanyFactory.php`
- Creates fake company data for testing
- Supports inactive state for testing inactive companies

## Usage Examples

### Creating a Certification
```bash
POST /api/certifications
Content-Type: multipart/form-data

{
    "certification_name": "ISO 9001:2015",
    "certification_type": "company",
    "entity_type": "Company",
    "entity_id": 1,
    "issuing_authority": "ISO",
    "certificate_number": "ISO-123456",
    "issue_date": "2024-01-01",
    "expiry_date": "2027-01-01",
    "is_renewable": true,
    "renewal_period_days": 90,
    "alert_before_days": 60,
    "company_id": 1,
    "certificate_file": <file>
}
```

### Getting Expiring Certifications
```bash
GET /api/certifications/expiring?days=30&company_id=1
```

### Renewing a Certification
```bash
POST /api/certifications/{id}/renew

{
    "new_issue_date": "2027-01-01",
    "new_expiry_date": "2030-01-01",
    "certificate_number": "ISO-789012",
    "notes": "Renewed after audit",
    "certificate_file": <file>
}
```

### Getting Dashboard Statistics
```bash
GET /api/reports/compliance-dashboard?company_id=1
```

Response includes:
- Certification statistics
- Compliance tracking statistics
- Requirements overview
- Recent expiring certifications
- Recent overdue trackings

## File Storage

### Certificates
- **Storage path:** `storage/app/public/certifications/`
- **Allowed formats:** PDF, JPG, JPEG, PNG
- **Max size:** 5MB

### Evidence Files
- **Storage path:** `storage/app/public/compliance_evidence/`
- **Allowed formats:** PDF, JPG, JPEG, PNG, DOC, DOCX
- **Max size:** 10MB

## Code Quality

### Linting
All code has been validated with Laravel Pint:
- ✅ Models linted
- ✅ Controllers linted
- ✅ Migrations linted
- ✅ Factories linted
- ✅ Routes linted

### Security
- ✅ No security vulnerabilities detected
- ✅ Proper validation on all inputs
- ✅ File upload restrictions in place
- ✅ Foreign key constraints enforced
- ✅ Cascade delete configured appropriately

## Future Enhancements

Potential features for future development:
1. Email notifications for expiring certifications
2. Automated renewal workflows
3. Integration with external certification authorities
4. Bulk upload functionality
5. Advanced reporting with charts and graphs
6. Export functionality (PDF, Excel)
7. Audit trail for all changes
8. Reminder scheduling system
9. Document version control
10. Mobile app integration

## Migration Instructions

To apply these changes to your database:

```bash
# Run migrations
php artisan migrate

# If you need to rollback
php artisan migrate:rollback --step=3
```

## Testing Instructions

```bash
# Run all tests
php artisan test

# Run only certification tests
php artisan test --filter=CertificationTest

# Run with coverage
php artisan test --coverage
```

## Dependencies

No additional dependencies were added. The implementation uses:
- Laravel 12.x framework
- Standard Laravel packages (Eloquent, Validation, Storage)

## Notes

- All timestamps are automatically managed by Laravel
- Soft deletes are implemented on Company model
- Polymorphic relationships allow flexibility for entity types
- Auto-status updates ensure data integrity
- Comprehensive scopes enable easy querying
- File cleanup on delete prevents orphaned files

## Support

For issues or questions, please refer to:
- Laravel Documentation: https://laravel.com/docs
- CEMS Project Repository: https://github.com/ALNSOUR0790820045/CEMS
