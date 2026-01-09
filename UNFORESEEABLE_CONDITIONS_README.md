# Unforeseeable Physical Conditions Module

## Overview
This module implements the management of Unforeseeable Physical Conditions according to FIDIC Clause 4.12. It provides comprehensive tracking, documentation, and reporting capabilities for unexpected site conditions encountered during construction projects.

## Features

### ✅ Core Functionality
- **Comprehensive Documentation**: Track all details of unforeseen conditions including location, type, impact, and timeline
- **GPS Coordinates**: Precise location tracking with latitude/longitude support
- **Evidence Management**: Upload and manage multiple types of evidence (photos, videos, reports, etc.)
- **Status Workflow**: Track conditions through their lifecycle (identified → notice_sent → under_investigation → agreed → resolved)
- **FIDIC Compliance**: Built specifically for FIDIC Clause 4.12 requirements
- **Cost & Time Impact**: Track estimated delays and cost implications
- **RTL Support**: Full Arabic language support with RTL text handling

### 📊 Condition Types Supported
- Ground conditions (ظروف التربة)
- Rock conditions (ظروف صخرية)
- Water conditions (ظروف مائية)
- Contamination (تلوث)
- Underground utilities (خدمات تحت الأرض)
- Archaeological findings (آثار)
- Unexploded ordnance (مخلفات حربية)
- Other

### 🔗 Integration
- Links to Time Bar Events
- Links to Claims
- Links to EOT (Extension of Time) Requests
- Links to Projects and Contracts

## Database Schema

### Tables Created
1. **projects** - Basic project information
2. **contracts** - Contract details
3. **claims** - Claims management
4. **eot_requests** - Extension of Time requests
5. **time_bar_events** - Time bar event tracking
6. **unforeseeable_conditions** - Main conditions table
7. **unforeseeable_conditions_evidence** - Evidence attachments

## API Endpoints

All API endpoints require authentication via Laravel Sanctum.

### List Conditions
```http
GET /api/unforeseeable-conditions
```

**Query Parameters:**
- `status` - Filter by status
- `condition_type` - Filter by condition type
- `project_id` - Filter by project
- `contract_id` - Filter by contract
- `per_page` - Results per page (default: 15)

**Response:**
```json
{
  "data": [
    {
      "id": 1,
      "condition_number": "UFC-2026-0001",
      "title": "Unexpected Rock Layer",
      "status": "notice_sent",
      "project": {...},
      "contract": {...},
      ...
    }
  ],
  "links": {...},
  "meta": {...}
}
```

### Create Condition
```http
POST /api/unforeseeable-conditions
```

**Required Fields:**
- `project_id` - Project ID
- `title` - Condition title
- `description` - Detailed description
- `location` - Location description
- `condition_type` - Type of condition
- `discovery_date` - Date discovered
- `impact_description` - Impact description
- `actual_conditions` - Description of actual conditions found
- `difference_analysis` - Analysis of difference from expected

**Optional Fields:**
- `contract_id` - Contract ID
- `location_latitude` - GPS latitude
- `location_longitude` - GPS longitude
- `notice_date` - Date notice was sent
- `inspection_date` - Date of inspection
- `contractual_clause` - Contractual clause reference (default: "4.12")
- `estimated_delay_days` - Estimated delay in days
- `estimated_cost_impact` - Estimated cost impact
- `currency` - Currency code (default: "JOD")
- `tender_assumptions` - What was assumed in tender
- `site_investigation_data` - Original site investigation data
- `immediate_measures` - Immediate measures taken
- `proposed_solution` - Proposed solution
- `status` - Current status
- `time_bar_event_id` - Related time bar event
- `claim_id` - Related claim
- `eot_id` - Related EOT request
- `notes` - Additional notes

**Response:**
```json
{
  "message": "Unforeseeable condition created successfully",
  "data": {
    "id": 1,
    "condition_number": "UFC-2026-0001",
    ...
  }
}
```

### Get Single Condition
```http
GET /api/unforeseeable-conditions/{id}
```

**Response:**
```json
{
  "id": 1,
  "condition_number": "UFC-2026-0001",
  "title": "Unexpected Rock Layer",
  "project": {...},
  "contract": {...},
  "evidence": [...],
  "reported_by": {...},
  "verified_by": {...},
  ...
}
```

### Update Condition
```http
PUT /api/unforeseeable-conditions/{id}
PATCH /api/unforeseeable-conditions/{id}
```

**Fields:** Same as create endpoint (all optional)

### Delete Condition
```http
DELETE /api/unforeseeable-conditions/{id}
```

### Send Notice
```http
POST /api/unforeseeable-conditions/{id}/send-notice
```

Marks the condition as "notice_sent" and sets the notice_date to today.

**Response:**
```json
{
  "message": "Notice sent successfully",
  "data": {...}
}
```

### Upload Evidence
```http
POST /api/unforeseeable-conditions/{id}/evidence
Content-Type: multipart/form-data
```

**Required Fields:**
- `evidence_type` - Type of evidence (photo, video, soil_test, survey_report, expert_report, witness_statement, correspondence, other)
- `title` - Evidence title
- `file` - File to upload (max 50MB)
- `evidence_date` - Date of evidence

**Optional Fields:**
- `description` - Evidence description
- `latitude` - GPS latitude where evidence was captured
- `longitude` - GPS longitude where evidence was captured
- `capture_timestamp` - Timestamp when evidence was captured

**Response:**
```json
{
  "message": "Evidence uploaded successfully",
  "data": {
    "id": 1,
    "condition_id": 1,
    "evidence_type": "photo",
    "title": "Site Photo",
    "file_path": "unforeseeable_conditions/1/1735941234_photo.jpg",
    "uploaded_by": {...}
  }
}
```

### Export Condition
```http
GET /api/unforeseeable-conditions/{id}/export
```

Exports the complete condition data including all relationships and evidence.

**Response:**
```json
{
  "export_type": "json",
  "export_date": "2026-01-04 21:45:00",
  "data": {...}
}
```

## Models

### UnforeseeableCondition
**Relationships:**
- `project()` - Belongs to Project
- `contract()` - Belongs to Contract
- `timeBarEvent()` - Belongs to TimeBarEvent
- `claim()` - Belongs to Claim
- `eotRequest()` - Belongs to EotRequest
- `reportedBy()` - Belongs to User
- `verifiedBy()` - Belongs to User
- `evidence()` - Has many UnforeseeableConditionEvidence

### UnforeseeableConditionEvidence
**Relationships:**
- `condition()` - Belongs to UnforeseeableCondition
- `uploadedBy()` - Belongs to User

## Installation & Setup

### 1. Run Migrations
```bash
php artisan migrate
```

### 2. (Optional) Seed Sample Data
```bash
php artisan db:seed --class=UnforeseeableConditionSeeder
```

### 3. Configure Storage
Ensure the `public` disk is configured in `config/filesystems.php` and linked:
```bash
php artisan storage:link
```

## Status Workflow

1. **identified** - Condition has been identified
2. **notice_sent** - Notice has been sent to Engineer
3. **under_investigation** - Being investigated by parties
4. **agreed** - Parties have agreed on the condition
5. **disputed** - Condition is under dispute
6. **resolved** - Condition has been resolved
7. **rejected** - Condition claim has been rejected

## Condition Numbering

Conditions are automatically numbered in the format: `UFC-YYYY-NNNN`
- UFC = Unforeseeable Condition
- YYYY = Current year
- NNNN = Sequential number (0001, 0002, etc.)

Example: `UFC-2026-0001`

## Authentication

All API endpoints require authentication using Laravel Sanctum. Include the API token in the Authorization header:

```http
Authorization: Bearer {your-api-token}
```

## File Storage

Evidence files are stored in:
```
storage/app/public/unforeseeable_conditions/{condition_id}/
```

Files are accessible via the public URL:
```
http://your-domain.com/storage/unforeseeable_conditions/{condition_id}/{filename}
```

## Arabic (RTL) Support

The module fully supports Arabic text with Right-to-Left (RTL) display. All text fields accept Arabic characters, and the database schema includes comments in both English and Arabic.

## Related Documentation

- FIDIC Conditions of Contract
- Clause 4.12 - Unforeseeable Physical Conditions
- Time Bar Provisions (Clause 20.1)
- Claims Procedures (Clause 20.1)

## License

This module is part of the CEMS (Contract & Engineering Management System) project.
