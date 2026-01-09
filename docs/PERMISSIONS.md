# Permissions Documentation

This document lists all available permissions in the CEMS (Contract & Employee Management System).

## Permission Structure

Permissions are organized by module and follow a naming pattern: `{action}-{module}`

## Available Permissions

### User Management
- `view-users` - View user list and details
- `create-users` - Create new users
- `edit-users` - Edit existing users
- `delete-users` - Delete users

### Role & Permission Management
- `manage-roles` - Create, edit, and delete roles
- `manage-permissions` - Create and manage permissions
- `assign-roles` - Assign roles to users

### Company Management
- `view-companies` - View company list and details
- `create-companies` - Create new companies
- `edit-companies` - Edit company information
- `delete-companies` - Delete companies

### Employee Management
- `view-employees` - View employee list and details
- `create-employees` - Create new employee records
- `edit-employees` - Edit employee information
- `delete-employees` - Delete employee records

### Department Management
- `view-departments` - View department list and details
- `create-departments` - Create new departments
- `edit-departments` - Edit department information
- `delete-departments` - Delete departments

### Contract Management
- `view-contracts` - View contract list and details
- `create-contracts` - Create new contracts
- `edit-contracts` - Edit contract information
- `delete-contracts` - Delete contracts
- `approve-contracts` - Approve pending contracts

### Document Management
- `view-documents` - View document list
- `upload-documents` - Upload new documents
- `download-documents` - Download documents
- `delete-documents` - Delete documents

### Attendance Management
- `view-attendance` - View attendance records
- `record-attendance` - Record attendance for employees
- `edit-attendance` - Edit attendance records
- `approve-attendance` - Approve attendance records

### Leave Management
- `view-leaves` - View leave requests
- `request-leave` - Submit leave requests
- `approve-leave` - Approve leave requests
- `reject-leave` - Reject leave requests

### Payroll Management
- `view-payroll` - View payroll information
- `process-payroll` - Process payroll calculations
- `approve-payroll` - Approve payroll for payment
- `export-payroll` - Export payroll reports

### Reports
- `view-reports` - View system reports
- `generate-reports` - Generate new reports
- `export-reports` - Export reports to various formats

### Settings
- `view-settings` - View system settings
- `edit-settings` - Modify system settings

## Usage in Code

### Check Permission in Controller
```php
if ($user->hasPermissionTo('create-users')) {
    // User has permission
}
```

### Protect Route with Permission Middleware
```php
Route::middleware('permission:manage-roles')->group(function () {
    Route::post('/roles', [RoleController::class, 'store']);
});
```

### Check Permission in Blade Template
```php
@can('edit-users')
    <button>Edit User</button>
@endcan
```

### Give Permission to User
```php
$user->givePermissionTo('view-reports');
```

### Revoke Permission from User
```php
$user->revokePermissionTo('delete-users');
```

## Best Practices

1. **Principle of Least Privilege**: Only grant the minimum permissions necessary for a role
2. **Use Roles**: Assign permissions to roles, then assign roles to users
3. **Regular Audits**: Periodically review and audit permission assignments
4. **Document Custom Permissions**: If you add new permissions, document them here
5. **Test Permissions**: Always test permission checks in your features and tests
