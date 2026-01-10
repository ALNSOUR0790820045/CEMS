# Contract Templates Module - API Documentation

## Overview
This module provides a complete system for managing contract templates, including JEA (Jordanian Engineers Association) contracts, FIDIC contracts, and custom templates.

## Base URL
All API endpoints are prefixed with `/api` and require authentication.

## Endpoints

### 1. List Contract Templates
**GET** `/api/contract-templates`

Returns a list of all active contract templates.

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "code": "JEA-01",
      "name": "عقد نقابة المقاولين الأردنيين - أعمال البناء",
      "name_en": "Jordanian Engineers Association Contract - Construction Works",
      "type": "jea_01",
      "version": "2024",
      "year": 2024,
      "is_active": true,
      "clauses": [...]
    }
  ]
}
```

### 2. Create Contract Template
**POST** `/api/contract-templates`

Creates a new contract template.

**Request Body:**
```json
{
  "code": "CUSTOM-01",
  "name": "عقد مخصص",
  "name_en": "Custom Contract",
  "type": "custom",
  "version": "1.0",
  "year": 2024,
  "description": "وصف العقد",
  "is_active": true
}
```

**Response:**
```json
{
  "success": true,
  "message": "تم إنشاء قالب العقد بنجاح",
  "data": { ... }
}
```

### 3. Get Contract Template
**GET** `/api/contract-templates/{id}`

Returns details of a specific contract template including clauses, special conditions, and variables.

**Response:**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "code": "JEA-01",
    "name": "عقد نقابة المقاولين الأردنيين - أعمال البناء",
    "clauses": [...],
    "specialConditions": [...],
    "variables": [...]
  }
}
```

### 4. Update Contract Template
**PUT** `/api/contract-templates/{id}`

Updates an existing contract template.

**Request Body:** (same as create)

### 5. Get Template Clauses
**GET** `/api/contract-templates/{id}/clauses`

Returns all clauses for a specific template.

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "clause_number": "1.1",
      "title": "التعاريف والتفسيرات",
      "content": "...",
      "category": "general",
      "has_time_bar": false,
      "children": [...]
    }
  ]
}
```

### 6. Get Template Variables
**GET** `/api/contract-templates/{id}/variables`

Returns all variables that need to be filled for a specific template.

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "variable_key": "{{employer_name}}",
      "variable_label": "اسم صاحب العمل",
      "data_type": "text",
      "is_required": true
    }
  ]
}
```

### 7. Generate Contract from Template
**POST** `/api/contract-templates/{id}/generate`

Generates a new contract instance from a template.

**Request Body:**
```json
{
  "contract_title": "عقد إنشاء مبنى سكني",
  "parties": {
    "employer_name": "شركة ABC",
    "employer_address": "عمان، الأردن",
    "contractor_name": "شركة XYZ للمقاولات",
    "contractor_address": "عمان، الأردن"
  },
  "filled_data": {
    "{{employer_name}}": "شركة ABC",
    "{{contractor_name}}": "شركة XYZ",
    "{{contract_value}}": "500000",
    "{{start_date}}": "2024-01-01",
    "{{completion_period}}": "365"
  },
  "modified_clauses": [],
  "added_special_conditions": []
}
```

**Response:**
```json
{
  "success": true,
  "message": "تم إنشاء العقد بنجاح",
  "data": {
    "id": 1,
    "template_id": 1,
    "contract_title": "عقد إنشاء مبنى سكني",
    "status": "draft",
    ...
  }
}
```

### 8. Get JEA-01 Template
**GET** `/api/contract-templates/jea-01`

Returns the JEA-01 contract template with all details.

### 9. Get JEA-02 Template
**GET** `/api/contract-templates/jea-02`

Returns the JEA-02 contract template with all details.

### 10. Generate from Template (Alternative)
**POST** `/api/contracts/generate-from-template`

Alternative endpoint for generating contracts.

**Request Body:** (same as endpoint #7, but includes template_id)

### 11. Export Contract to Word
**GET** `/api/contracts/{id}/export-word`

Exports a generated contract to Word format.

**Note:** Currently returns a placeholder response. Full implementation pending.

### 12. Export Contract to PDF
**GET** `/api/contracts/{id}/export-pdf`

Exports a generated contract to PDF format.

**Note:** Currently returns a placeholder response. Full implementation pending.

## Web Routes

The following web routes are available for browser access:

- `GET /contract-templates` - List all templates
- `GET /contract-templates/{id}` - View template details
- `GET /contract-templates/{id}/clauses` - View all clauses
- `GET /contract-templates/{id}/generate` - Generate contract form
- `POST /contract-templates/generate-contract` - Submit generated contract
- `GET /contract-templates/preview/{id}` - Preview generated contract
- `GET /contract-templates/jea-01` - JEA-01 specific page
- `GET /contract-templates/jea-02` - JEA-02 specific page

## Data Models

### ContractTemplate
- `id`: Primary key
- `code`: Unique code (e.g., JEA-01)
- `name`: Arabic name
- `name_en`: English name
- `type`: Enum (jea_01, jea_02, fidic_red, fidic_yellow, fidic_silver, ministry, custom)
- `version`: Version number
- `year`: Year
- `description`: Description
- `file_path`: Optional file path
- `is_active`: Boolean

### ContractTemplateClause
- `id`: Primary key
- `template_id`: Foreign key
- `clause_number`: Clause number (e.g., 1.1)
- `title`: Arabic title
- `title_en`: English title
- `content`: Arabic content
- `content_en`: English content
- `parent_id`: Self-referencing for sub-clauses
- `category`: Enum (general, contractor_obligations, employer_obligations, time, payment, variations, claims, termination, disputes, other)
- `has_time_bar`: Boolean
- `time_bar_days`: Number of days for time bar
- `time_bar_description`: Description of time bar requirements
- `is_mandatory`: Boolean
- `is_modifiable`: Boolean
- `sort_order`: Integer

### ContractTemplateVariable
- `id`: Primary key
- `template_id`: Foreign key
- `variable_key`: Key like {{employer_name}}
- `variable_label`: Arabic label
- `variable_label_en`: English label
- `data_type`: Enum (text, number, date, currency, percentage)
- `is_required`: Boolean
- `default_value`: Optional default
- `description`: Optional description

### ContractGenerated
- `id`: Primary key
- `template_id`: Foreign key
- `project_id`: Optional foreign key
- `tender_id`: Optional foreign key
- `contract_title`: Contract title
- `parties`: JSON of parties
- `filled_data`: JSON of filled variables
- `modified_clauses`: JSON of modified clauses
- `added_special_conditions`: JSON of added conditions
- `status`: Enum (draft, review, approved, signed)
- `generated_file`: Optional file path
- `generated_by`: Foreign key to users

## Authentication

All endpoints require authentication using Laravel's built-in authentication system. Include the session cookie or API token in your requests.

## Error Responses

All endpoints return consistent error responses:

```json
{
  "success": false,
  "message": "Error message",
  "errors": {
    "field": ["validation error"]
  }
}
```

## Features

- ✅ JEA-01 و JEA-02 templates ready
- ✅ FIDIC contract support
- ✅ Customizable clauses
- ✅ Special conditions
- ✅ Automatic data filling
- ✅ Word/PDF export (pending implementation)
- ✅ RTL support
- ✅ Time bar tracking
- ✅ Variable management
- ✅ Hierarchical clauses

## Usage Example

### Creating and Generating a Contract

1. **List available templates:**
```bash
curl -X GET http://localhost:8000/api/contract-templates \
  -H "Authorization: Bearer {token}"
```

2. **Get template details:**
```bash
curl -X GET http://localhost:8000/api/contract-templates/1 \
  -H "Authorization: Bearer {token}"
```

3. **Generate a contract:**
```bash
curl -X POST http://localhost:8000/api/contract-templates/1/generate \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{
    "contract_title": "عقد إنشاء مبنى",
    "parties": {
      "employer_name": "شركة ABC",
      "contractor_name": "شركة XYZ"
    },
    "filled_data": {
      "{{contract_value}}": "500000"
    }
  }'
```

## Future Enhancements

- PDF generation using DomPDF
- Word generation using PHPWord
- Contract versioning
- Digital signatures
- Automated notifications
- Template approval workflow
