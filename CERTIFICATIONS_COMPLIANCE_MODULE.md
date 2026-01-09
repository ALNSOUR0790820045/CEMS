# Certifications & Compliance Module Documentation

## Overview
The Certifications & Compliance Module is a comprehensive system for managing certifications, licenses, permits, and compliance requirements in the CEMS (Construction ERP Management System).

## Features

### 1. Certification Management
- **Auto-generated certification numbers** (format: CERT-YYYY-XXXX)
- Track multiple types: license, permit, certificate, registration, insurance
- Organize by category: company, project, employee, equipment, safety
- Track issue and expiry dates with customizable reminder periods
- Manage costs in multiple currencies
- Store attachment documents
- Automatic expiry detection and alerts

### 2. Compliance Requirements
- Define compliance requirements by category (safety, environmental, legal, quality, financial)
- Set frequency: one_time, monthly, quarterly, annually
- Mark as mandatory or optional
- Link to regulation references
- Assign responsible roles

### 3. Compliance Checks
- **Auto-generated check numbers** (format: CC-YYYY-XXXX)
- Schedule and track compliance checks
- Record findings and corrective actions
- Status tracking: pending, passed, failed, waived
- Overdue check detection
- Link to projects and requirements

### 4. Certification Renewals
- **Auto-generated renewal numbers** (format: RN-YYYY-XXXX)
- Track renewal history
- Record renewal costs and dates
- Update certification expiry dates automatically
- Maintain audit trail

### 5. Comprehensive Reporting
- **Certification Register**: Complete list with cost summaries
- **Compliance Status**: Pass/fail rates and statistics by requirement
- **Expiry Calendar**: Upcoming expirations grouped by month

## Database Schema

### Tables Created
1. **certifications** - Main certification records
2. **compliance_requirements** - Compliance rules and regulations
3. **compliance_checks** - Individual compliance check records
4. **certification_renewals** - Renewal history

### Key Relationships
- Certifications belong to Company and Currency
- Certifications have many Renewals
- Compliance Requirements belong to Company
- Compliance Requirements have many Checks
- Compliance Checks belong to Requirement, Company, and optionally Project
- Certification Renewals belong to Certification

## API Endpoints

### Certifications
```
GET    /api/certifications                  - List all certifications
POST   /api/certifications                  - Create new certification
GET    /api/certifications/{id}             - Show certification details
PUT    /api/certifications/{id}             - Update certification
DELETE /api/certifications/{id}             - Delete certification
GET    /api/certifications/expiring         - Get expiring certifications
GET    /api/certifications/expired          - Get expired certifications
POST   /api/certifications/{id}/renew       - Renew a certification
```

### Compliance Requirements
```
GET    /api/compliance-requirements         - List all requirements
POST   /api/compliance-requirements         - Create new requirement
GET    /api/compliance-requirements/{id}    - Show requirement details
PUT    /api/compliance-requirements/{id}    - Update requirement
DELETE /api/compliance-requirements/{id}    - Delete requirement
```

### Compliance Checks
```
GET    /api/compliance-checks               - List all checks
POST   /api/compliance-checks               - Create new check
GET    /api/compliance-checks/{id}          - Show check details
PUT    /api/compliance-checks/{id}          - Update check
DELETE /api/compliance-checks/{id}          - Delete check
POST   /api/compliance-checks/{id}/pass     - Mark check as passed
POST   /api/compliance-checks/{id}/fail     - Mark check as failed
```

### Reports
```
GET    /api/reports/certification-register  - Certification register report
GET    /api/reports/compliance-status       - Compliance status report
GET    /api/reports/expiry-calendar         - Expiry calendar report
```

## Models

### Certification Model
**Key Features:**
- Scopes: `active()`, `expired()`, `expiring($days)`, `byType()`, `byCategory()`, `byReference()`
- Accessors: `days_until_expiry`, `is_expiring`, `is_expired`, `type_name`, `category_name`, `status_name`
- Methods: `generateCertificationNumber()`, `renew()`

### ComplianceRequirement Model
**Key Features:**
- Scopes: `mandatory()`, `byCategory()`, `byFrequency()`
- Accessors: `category_name`, `frequency_name`

### ComplianceCheck Model
**Key Features:**
- Scopes: `pending()`, `passed()`, `failed()`, `overdue()`, `dueSoon($days)`
- Accessors: `status_name`, `is_overdue`
- Methods: `generateCheckNumber()`, `markAsPassed()`, `markAsFailed()`

### CertificationRenewal Model
**Key Features:**
- Methods: `generateRenewalNumber()`

## Usage Examples

### Creating a Certification
```json
POST /api/certifications
{
  "name": "Building License",
  "name_en": "Building License",
  "type": "license",
  "category": "project",
  "issuing_authority": "Municipality",
  "issue_date": "2024-01-01",
  "expiry_date": "2025-01-01",
  "cost": 5000.00,
  "currency_id": 1,
  "reminder_days": 60,
  "company_id": 1
}
```

### Renewing a Certification
```json
POST /api/certifications/1/renew
{
  "new_expiry_date": "2026-01-01",
  "renewal_cost": 5500.00,
  "notes": "Annual renewal"
}
```

### Creating a Compliance Check
```json
POST /api/compliance-checks
{
  "compliance_requirement_id": 1,
  "project_id": 10,
  "check_date": "2024-01-15",
  "due_date": "2024-01-31",
  "company_id": 1
}
```

### Marking Check as Passed
```json
POST /api/compliance-checks/1/pass
{
  "findings": "All safety equipment in place and functional"
}
```

### Marking Check as Failed
```json
POST /api/compliance-checks/1/fail
{
  "findings": "Fire extinguishers expired",
  "corrective_action": "Replace all fire extinguishers by end of week"
}
```

## Testing

### Factory Support
All models have factories for testing:
- `CertificationFactory` with `expiring()` and `expired()` states
- `ComplianceRequirementFactory`
- `ComplianceCheckFactory` with `passed()` and `failed()` states
- `CertificationRenewalFactory`
- `CurrencyFactory` with `base()` state

### Feature Tests
Complete test coverage for:
- Certification CRUD operations
- Certification expiring/expired filtering
- Certification renewal
- Compliance requirement CRUD operations
- Compliance check CRUD operations
- Compliance check pass/fail actions

## Security

### Code Review
All code has been reviewed and:
- Proper DB facade imports added
- SQL injection protection via Eloquent ORM
- Input validation on all endpoints
- Authorization via Sanctum middleware

### CodeQL Analysis
No security vulnerabilities detected.

## Known Issues

### Existing Repository Issues
The repository contains duplicate migrations for several tables (cities, currencies, projects, etc.) that prevent successful migration execution. This is a pre-existing issue not related to this module.

**Workaround**: The new migrations can be manually applied or the duplicate migrations should be cleaned up before running all migrations.

## Future Enhancements

1. **Email Notifications**: Send automatic reminders before certification expiry
2. **Document Management**: Enhanced integration with document management system
3. **Workflow Automation**: Automatic creation of compliance checks based on frequency
4. **Dashboard Widgets**: Visual indicators for expiring certifications
5. **Mobile App Support**: Mobile notifications for expiring certifications
6. **Bulk Operations**: Bulk renewal and bulk check creation
7. **Advanced Reporting**: Custom report builder for compliance analytics

## Conclusion

The Certifications & Compliance Module provides a robust solution for managing organizational certifications and compliance requirements. It includes comprehensive API endpoints, proper data validation, detailed reporting, and full test coverage.
