# Document Management Module - API Documentation

## Overview
The Document Management Module provides a comprehensive system for managing documents with version control, access management, and advanced search capabilities.

## Database Schema

### Tables Created
1. **documents** - Main document storage with metadata
2. **document_versions** - Version history tracking
3. **document_access** - Access control by user/role

## API Endpoints

### Authentication
All endpoints require authentication using Laravel Sanctum. Include the Bearer token in the Authorization header:
```
Authorization: Bearer {your-token}
```

### 1. List Documents
**GET** `/api/documents`

Query Parameters:
- `document_type` - Filter by type (contract, drawing, specification, certificate, report, correspondence, other)
- `status` - Filter by status (draft, review, approved, archived, obsolete)
- `category` - Filter by category
- `is_confidential` - Filter by confidentiality (true/false)
- `per_page` - Number of results per page (default: 15)

Response:
```json
{
  "data": [
    {
      "id": 1,
      "document_number": "DOC-2026-0001",
      "document_name": "Sample Contract",
      "document_type": "contract",
      "status": "approved",
      "uploaded_by": {...},
      "company": {...}
    }
  ],
  "links": {...},
  "meta": {...}
}
```

### 2. Create Document
**POST** `/api/documents`

Request (multipart/form-data):
```json
{
  "document_name": "string (required)",
  "document_type": "contract|drawing|specification|certificate|report|correspondence|other (required)",
  "category": "string (optional)",
  "related_entity_type": "string (optional)",
  "related_entity_id": "integer (optional)",
  "file": "file (required, max 50MB)",
  "description": "string (optional)",
  "tags": ["array", "of", "strings"],
  "is_confidential": "boolean (optional)",
  "status": "draft|review|approved|archived|obsolete (optional)",
  "expiry_date": "date (optional)"
}
```

Response:
```json
{
  "message": "Document created successfully",
  "document": {
    "id": 1,
    "document_number": "DOC-2026-0001",
    "document_name": "Sample Contract",
    ...
  }
}
```

### 3. Get Document
**GET** `/api/documents/{id}`

Response:
```json
{
  "id": 1,
  "document_number": "DOC-2026-0001",
  "document_name": "Sample Contract",
  "uploaded_by": {...},
  "versions": [...],
  "access_rights": [...]
}
```

### 4. Update Document
**PUT/PATCH** `/api/documents/{id}`

Request:
```json
{
  "document_name": "string",
  "document_type": "string",
  "category": "string",
  "description": "string",
  "tags": ["array"],
  "is_confidential": "boolean",
  "status": "string",
  "expiry_date": "date"
}
```

### 5. Delete Document
**DELETE** `/api/documents/{id}`

Response:
```json
{
  "message": "Document deleted successfully"
}
```

### 6. Upload New Version
**POST** `/api/documents/{id}/upload-version`

Request (multipart/form-data):
```json
{
  "file": "file (required, max 50MB)",
  "version": "string (required, e.g., '2.0')",
  "change_description": "string (optional)"
}
```

Response:
```json
{
  "message": "New version uploaded successfully",
  "document": {
    "id": 1,
    "version": "2.0",
    "versions": [...]
  }
}
```

### 7. Get Version History
**GET** `/api/documents/{id}/versions`

Response:
```json
[
  {
    "id": 1,
    "version": "2.0",
    "file_path": "documents/...",
    "change_description": "Updated terms",
    "uploaded_by": {...},
    "uploaded_at": "2026-01-04T10:00:00Z"
  }
]
```

### 8. Grant Access
**POST** `/api/documents/{id}/grant-access`

Request:
```json
{
  "user_id": "integer (optional)",
  "role_id": "integer (optional)",
  "access_level": "view|download|edit|delete (required)"
}
```

Note: Either `user_id` or `role_id` must be provided.

Response:
```json
{
  "message": "Access granted successfully",
  "access": {
    "id": 1,
    "access_level": "view",
    "user": {...}
  }
}
```

### 9. Search Documents
**GET** `/api/documents/search`

Query Parameters:
- `q` - Search term (searches in name, number, description, category)
- `document_type` - Filter by type
- `status` - Filter by status
- `tags` - Filter by tags (can be array)
- `related_entity_type` - Filter by related entity type
- `related_entity_id` - Filter by related entity ID
- `per_page` - Results per page

Response:
```json
{
  "data": [...],
  "links": {...},
  "meta": {...}
}
```

## Access Control

### Document Visibility Rules
1. **Company Isolation**: Users can only access documents from their own company
2. **Confidential Documents**: Only users with explicit access rights can view confidential documents
3. **Document Owner**: The user who uploaded the document has full access

### Access Levels
- **view**: Can view document details
- **download**: Can view and download the document
- **edit**: Can view, download, and modify the document
- **delete**: Can view, download, modify, and delete the document

### Access Grant Types
- **User-based**: Grant access to specific users
- **Role-based**: Grant access to all users with a specific role

## Features

### Automatic Document Numbering
Documents are automatically assigned unique numbers in the format: `DOC-YYYY-XXXX`
- YYYY = Current year
- XXXX = Sequential number (0001, 0002, etc.)

### Version Control
- All document versions are preserved
- Version history includes change descriptions
- Current version is always displayed on the main document
- Previous versions can be accessed through the versions endpoint

### File Support
- Maximum file size: 50MB
- Supported types: PDF, Word, Excel, CAD, Images, and more
- Files are stored securely with unique paths

### Search Capabilities
- Full-text search in document name, number, description, and category
- Filter by document type, status, tags
- Filter by related entities
- Combined search criteria support

## Usage Examples

### Create a Contract Document
```bash
curl -X POST https://your-domain/api/documents \
  -H "Authorization: Bearer {token}" \
  -F "document_name=Employment Contract" \
  -F "document_type=contract" \
  -F "category=HR" \
  -F "file=@contract.pdf" \
  -F "tags[]=employment" \
  -F "tags[]=legal" \
  -F "is_confidential=true"
```

### Search for Contracts
```bash
curl -X GET "https://your-domain/api/documents/search?document_type=contract&status=approved" \
  -H "Authorization: Bearer {token}"
```

### Upload New Version
```bash
curl -X POST https://your-domain/api/documents/1/upload-version \
  -H "Authorization: Bearer {token}" \
  -F "file=@contract_v2.pdf" \
  -F "version=2.0" \
  -F "change_description=Updated salary terms"
```

## Error Responses

### 401 Unauthorized
```json
{
  "message": "Unauthenticated."
}
```

### 403 Forbidden
```json
{
  "message": "This action is unauthorized."
}
```

### 422 Validation Error
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "document_name": ["The document name field is required."]
  }
}
```

### 500 Server Error
```json
{
  "message": "Failed to create document",
  "error": "Error details..."
}
```

## Best Practices

1. **Document Organization**
   - Use consistent naming conventions
   - Apply relevant tags for easy filtering
   - Set appropriate categories

2. **Version Control**
   - Always include meaningful change descriptions
   - Follow semantic versioning (1.0, 1.1, 2.0)
   - Don't skip version numbers

3. **Access Control**
   - Mark sensitive documents as confidential
   - Grant minimal required access levels
   - Use role-based access for teams

4. **Search Optimization**
   - Include keywords in document descriptions
   - Use tags consistently across related documents
   - Link documents to related entities when applicable

5. **File Management**
   - Keep file sizes reasonable
   - Use appropriate file formats
   - Consider document expiry dates for time-sensitive documents
