# Site Diary Module Documentation

## Overview
The Site Diary Module provides a comprehensive system for recording daily site activities, manpower, equipment usage, incidents, and progress tracking.

## Database Schema

### Main Tables Created:
1. **site_diaries** - Main diary records
2. **diary_manpower** - Daily manpower records  
3. **diary_equipment** - Equipment usage records
4. **diary_activities** - Daily activities and progress
5. **diary_materials** - Material receipts and usage
6. **diary_visitors** - Site visitor logs
7. **diary_incidents** - Safety and incident reports
8. **diary_instructions** - Instructions and directives
9. **diary_photos** - Photo documentation with GPS

## Models Created

All models are located in `app/Models/`:
- `SiteDiary` - Main diary model with workflow methods
- `DiaryManpower` - Manpower tracking
- `DiaryEquipment` - Equipment tracking
- `DiaryActivity` - Activity tracking
- `DiaryMaterial` - Material tracking
- `DiaryVisitor` - Visitor tracking
- `DiaryIncident` - Incident tracking
- `DiaryInstruction` - Instruction tracking
- `DiaryPhoto` - Photo tracking

## Controllers

Located in `app/Http/Controllers/Api/`:

### SiteDiaryController
- CRUD operations for site diaries
- Workflow methods: submit, review, approve, reject
- Utility methods: byDate, latest, duplicateFromPrevious

### DiaryEntryController
- Methods to add entries to diaries:
  - addManpower, updateManpower, deleteManpower
  - addEquipment
  - addActivity
  - addMaterial
  - addVisitor
  - addIncident
  - addInstruction
  - uploadPhoto

### DiaryReportController
- dailySummary - Daily activity summary
- weeklySummary - Weekly aggregated data
- monthlySummary - Monthly statistics
- manpowerHistogram - Manpower trends over time
- equipmentUtilization - Equipment efficiency metrics
- weatherAnalysis - Weather impact analysis
- incidentLog - Safety incident history
- progressPhotos - Photo gallery with filters

## API Routes

All routes are prefixed with `/api/` and require authentication (`auth:sanctum`).

### Site Diaries
```
GET    /api/site-diaries                      - List all diaries
POST   /api/site-diaries                      - Create new diary
GET    /api/site-diaries/{id}                 - Get diary details
PUT    /api/site-diaries/{id}                 - Update diary
DELETE /api/site-diaries/{id}                 - Delete diary
GET    /api/site-diaries/by-date/{projectId}/{date} - Get diary by date
GET    /api/site-diaries/latest/{projectId}   - Get latest diary
POST   /api/site-diaries/{id}/submit          - Submit for review
POST   /api/site-diaries/{id}/review          - Mark as reviewed
POST   /api/site-diaries/{id}/approve         - Approve diary
POST   /api/site-diaries/{id}/reject          - Reject diary
POST   /api/site-diaries/{id}/duplicate       - Duplicate from previous
```

### Diary Entries
```
POST   /api/site-diaries/{id}/manpower        - Add manpower
PUT    /api/site-diaries/{id}/manpower/{entryId} - Update manpower
DELETE /api/site-diaries/{id}/manpower/{entryId} - Delete manpower
POST   /api/site-diaries/{id}/equipment       - Add equipment
POST   /api/site-diaries/{id}/activities      - Add activity
POST   /api/site-diaries/{id}/materials       - Add material
POST   /api/site-diaries/{id}/visitors        - Add visitor
POST   /api/site-diaries/{id}/incidents       - Add incident
POST   /api/site-diaries/{id}/instructions    - Add instruction
POST   /api/site-diaries/{id}/photos          - Upload photo
```

### Reports
```
GET /api/reports/daily-summary/{projectId}         - Daily summary
GET /api/reports/weekly-summary/{projectId}        - Weekly summary
GET /api/reports/monthly-summary/{projectId}       - Monthly summary
GET /api/reports/manpower-histogram/{projectId}    - Manpower trends
GET /api/reports/equipment-utilization/{projectId} - Equipment utilization
GET /api/reports/weather-analysis/{projectId}      - Weather analysis
GET /api/reports/incident-log/{projectId}          - Incident log
GET /api/reports/progress-photos/{projectId}       - Progress photos
```

## Business Rules

1. **One diary per project per day** - Enforced by unique constraint
2. **Cannot edit after approval** - Controlled by `canEdit()` method
3. **Workflow** - Draft → Submitted → Reviewed → Approved
4. **Auto-generated diary numbers** - Format: SD-YYYYMMDD-XXX
5. **Weather and manpower required** - For operational diaries
6. **Delay reason required** - When work_status is 'delayed'

## Usage Examples

### Creating a Site Diary
```php
POST /api/site-diaries
{
    "project_id": 1,
    "diary_date": "2026-01-09",
    "weather_morning": "sunny",
    "weather_afternoon": "cloudy",
    "temperature_min": 20.5,
    "temperature_max": 32.0,
    "site_condition": "dry",
    "work_status": "normal",
    "notes": "Normal operations"
}
```

### Adding Manpower
```php
POST /api/site-diaries/1/manpower
{
    "trade": "carpenter",
    "own_count": 5,
    "subcontractor_count": 3,
    "hours_worked": 8.0,
    "overtime_hours": 2.0
}
```

### Recording an Incident
```php
POST /api/site-diaries/1/incidents
{
    "incident_type": "near_miss",
    "severity": "minor",
    "time_occurred": "14:30",
    "location": "Excavation Area",
    "description": "Worker almost stepped into excavated area",
    "immediate_action": "Area cordoned off",
    "hse_notified": true
}
```

### Getting Weekly Summary
```php
GET /api/reports/weekly-summary/1?start_date=2026-01-06&end_date=2026-01-12
```

## Features

1. ✅ Daily comprehensive site recording
2. ✅ Manpower and equipment tracking
3. ✅ Weather condition documentation
4. ✅ Visitor and incident logging
5. ✅ Photo upload with GPS coordinates
6. ✅ Weekly and monthly reports
7. ✅ Duplicate from previous day
8. ✅ Multi-level approval workflow
9. ✅ Equipment utilization analysis
10. ✅ Weather impact analysis

## Testing

A comprehensive test suite is available in `tests/Feature/SiteDiaryTest.php` covering:
- Creating site diaries
- Adding various entry types (manpower, equipment, activities, incidents)
- Workflow operations (submit, review, approve)
- Report generation
- Business rule enforcement
- Diary duplication

### Running Tests

**Note**: Due to multiple duplicate migration files in the project for the `projects` table, the test database migration may fail. To run the tests successfully:

1. Clean up duplicate migration files (keep only the latest)
2. Or run: `php artisan migrate:fresh` in the test environment
3. Then run: `php artisan test --filter SiteDiaryTest`

## Database Migrations

To run the Site Diary migrations:

```bash
php artisan migrate
```

This will create all 9 tables required for the Site Diary module.

## Next Steps

1. **Frontend Development** - Create Vue.js/React components for the diary interface
2. **PDF Export** - Add PDF generation for daily/weekly reports
3. **Email Notifications** - Alert stakeholders when diaries need review/approval
4. **Mobile App** - Develop mobile app for on-site diary entry
5. **Photo Compression** - Add automatic photo compression for uploads
6. **Weather API Integration** - Auto-fill weather data from weather APIs
7. **Analytics Dashboard** - Visual dashboard for project progress tracking

## Support

For issues or questions, please contact the development team.
