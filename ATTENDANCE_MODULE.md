# Attendance & Time Tracking Module

## Overview
This module provides comprehensive employee attendance tracking with biometric integration support, overtime calculation, leave management, and shift scheduling.

## Features

### 1. Employee Management
- Link employees to system users
- Track employment details (department, position, hire date)
- Multiple employment types (full-time, part-time, contract, internship)
- Assign shift schedules to employees

### 2. Attendance Tracking
- **Check-in/Check-out**: Simple API endpoints for recording employee attendance
- **Automatic Calculations**:
  - Work hours based on check-in and check-out times
  - Late arrival minutes
  - Early leave detection
  - Overtime hours calculation
- **Location & Device Tracking**: Optional GPS location and device ID recording
- **Multiple Status Types**: present, absent, on_leave, half_day, weekend, holiday

### 3. Shift Management
- Define multiple shift schedules per company
- Configure start/end times and working hours
- Set grace periods for late arrivals
- Assign employees to specific shifts
- Active/inactive shift management

### 4. Leave Management
- **Leave Request Creation**: Employees can request leave
- **Leave Types**: annual, sick, unpaid, maternity, emergency
- **Approval Workflow**: 
  - Submit leave requests
  - Approve or reject with reasons
  - Cancel pending/approved requests
- **Automatic Calculations**: Total days calculation

### 5. Reporting
- **Daily Attendance Report**: View attendance for a specific date
- **Monthly Summary**: Employee statistics by month
- **Attendance Summary**: Detailed statistics for any date range
- **Leave Report**: Track leave requests and statistics
- **Overtime Report**: Monitor employee overtime hours

## Database Schema

### Tables Created
1. **employees** - Employee records
2. **shift_schedules** - Working shift definitions
3. **attendance_records** - Daily attendance tracking
4. **leave_requests** - Leave request management

## API Endpoints

### Employee Management
```
GET    /api/employees              # List employees
POST   /api/employees              # Create employee
GET    /api/employees/{id}         # Get employee details
PUT    /api/employees/{id}         # Update employee
DELETE /api/employees/{id}         # Delete employee
```

### Attendance
```
GET    /api/attendance             # List attendance records
POST   /api/attendance             # Create attendance record
GET    /api/attendance/{id}        # Get attendance record
PUT    /api/attendance/{id}        # Update attendance record
DELETE /api/attendance/{id}        # Delete attendance record
POST   /api/attendance/check-in    # Check-in endpoint
POST   /api/attendance/check-out   # Check-out endpoint
```

### Leave Requests
```
GET    /api/leave-requests           # List leave requests
POST   /api/leave-requests           # Create leave request
GET    /api/leave-requests/{id}      # Get leave request
PUT    /api/leave-requests/{id}      # Update leave request
DELETE /api/leave-requests/{id}      # Delete leave request
POST   /api/leave-requests/{id}/approve  # Approve leave
POST   /api/leave-requests/{id}/reject   # Reject leave
POST   /api/leave-requests/{id}/cancel   # Cancel leave
```

### Shift Schedules
```
GET    /api/shift-schedules        # List shift schedules
POST   /api/shift-schedules        # Create shift schedule
GET    /api/shift-schedules/{id}   # Get shift schedule
PUT    /api/shift-schedules/{id}   # Update shift schedule
DELETE /api/shift-schedules/{id}   # Delete shift schedule
```

### Reports
```
GET /api/reports/attendance-summary   # Attendance summary report
GET /api/reports/daily-attendance     # Daily attendance report
GET /api/reports/monthly-attendance   # Monthly attendance report
GET /api/reports/leave-report         # Leave report
GET /api/reports/overtime-report      # Overtime report
```

## Usage Examples

### 1. Create an Employee
```json
POST /api/employees
{
  "user_id": 1,
  "employee_number": "EMP001",
  "hire_date": "2024-01-15",
  "department": "IT",
  "position": "Software Developer",
  "employment_type": "full_time",
  "shift_schedule_id": 1,
  "salary": 5000.00,
  "status": "active",
  "company_id": 1
}
```

### 2. Check-in
```json
POST /api/attendance/check-in
{
  "employee_id": 1,
  "location": "Office Building A",
  "device_id": "DEVICE_001"
}
```

### 3. Check-out
```json
POST /api/attendance/check-out
{
  "employee_id": 1
}
```

### 4. Create Leave Request
```json
POST /api/leave-requests
{
  "employee_id": 1,
  "leave_type": "annual",
  "start_date": "2026-02-01",
  "end_date": "2026-02-05",
  "reason": "Family vacation",
  "company_id": 1
}
```

### 5. Approve Leave Request
```json
POST /api/leave-requests/1/approve
{
  "approved_by_id": 2
}
```

### 6. Get Daily Attendance Report
```
GET /api/reports/daily-attendance?company_id=1&date=2026-01-04
```

### 7. Get Monthly Attendance Report
```
GET /api/reports/monthly-attendance?company_id=1&year=2026&month=1
```

### 8. Get Overtime Report
```
GET /api/reports/overtime-report?company_id=1&start_date=2026-01-01&end_date=2026-01-31
```

## Model Relationships

- **Employee** belongs to User, Company, ShiftSchedule
- **Employee** has many AttendanceRecords, LeaveRequests
- **AttendanceRecord** belongs to Employee, Company, ApprovedBy (User)
- **LeaveRequest** belongs to Employee, Company, ApprovedBy (User)
- **ShiftSchedule** belongs to Company
- **ShiftSchedule** has many Employees

## Query Filters

### Attendance Records
- `company_id` - Filter by company
- `employee_id` - Filter by employee
- `date` - Filter by specific date
- `start_date` & `end_date` - Filter by date range
- `status` - Filter by status
- `per_page` - Pagination (default: 15)

### Leave Requests
- `company_id` - Filter by company
- `employee_id` - Filter by employee
- `status` - Filter by status (pending, approved, rejected, cancelled)
- `leave_type` - Filter by leave type
- `per_page` - Pagination (default: 15)

### Employees
- `company_id` - Filter by company
- `status` - Filter by status (default: active)
- `department` - Filter by department
- `per_page` - Pagination (default: 15)

### Shift Schedules
- `company_id` - Filter by company
- `is_active` - Filter by active status
- `per_page` - Pagination (default: 15)

## Business Logic Features

### Automatic Calculations
1. **Work Hours**: Automatically calculated from check-in and check-out times
2. **Late Minutes**: Calculated based on shift start time and grace period
3. **Overtime Hours**: Calculated when work hours exceed shift working hours
4. **Leave Days**: Automatically calculated from start and end dates

### Model Methods

#### AttendanceRecord
- `calculateWorkHours()` - Calculate work hours from check times
- `calculateLateMinutes($shiftStartTime)` - Calculate late arrival minutes
- `calculateOvertimeHours($standardHours)` - Calculate overtime hours

#### LeaveRequest
- `calculateTotalDays()` - Calculate total leave days
- `approve($userId)` - Approve leave request
- `reject($userId, $reason)` - Reject leave request
- `cancel()` - Cancel leave request

### Model Scopes

#### Employee
- `active()` - Get only active employees
- `forCompany($companyId)` - Filter by company

#### AttendanceRecord
- `forCompany($companyId)` - Filter by company
- `forDate($date)` - Filter by specific date
- `forEmployee($employeeId)` - Filter by employee
- `forDateRange($startDate, $endDate)` - Filter by date range

#### LeaveRequest
- `pending()` - Get pending leave requests
- `approved()` - Get approved leave requests
- `forCompany($companyId)` - Filter by company
- `forEmployee($employeeId)` - Filter by employee

#### ShiftSchedule
- `active()` - Get only active shifts
- `forCompany($companyId)` - Filter by company

## Testing

The module includes comprehensive feature tests covering:
- Model relationships
- Database operations
- Business logic methods
- Model scopes
- Leave request workflow

Run tests with:
```bash
php artisan test --filter=AttendanceModuleTest
```

## Installation

1. Run migrations:
```bash
php artisan migrate
```

2. Create a shift schedule:
```bash
# Via API or database seeder
```

3. Create employees linked to users

4. Start tracking attendance!

## Integration Notes

### Biometric Device Integration
The module supports biometric device integration through:
- `device_id` field in attendance records
- Store device identifier when recording attendance
- Use this for device-based authentication

### GPS Location Tracking
- `location` field stores location information
- Can be used to store GPS coordinates or location names
- Useful for remote work or field employee tracking

### Multi-tenancy Support
- All models include `company_id` for multi-tenant support
- Filter all queries by company
- Maintains data isolation between companies

## Security Considerations

- All endpoints should be protected with authentication middleware
- Implement authorization checks to ensure users can only access their company's data
- Validate employee IDs belong to the authenticated user's company
- Approval actions should verify approver permissions

## Future Enhancements

Potential additions:
- Geofencing for check-in validation
- Face recognition integration
- Automatic shift assignment
- Leave balance tracking
- Holiday calendar management
- Notification system for pending approvals
- Mobile app integration
- Real-time attendance dashboard
