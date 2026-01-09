# Roles Documentation

This document describes the predefined roles in the CEMS (Contract & Employee Management System) and their permission levels.

## Role Hierarchy

```
Super Admin (Full System Access)
    ↓
Admin (Management Level)
    ↓
Manager (Operational Level)
    ↓
Employee (Basic Level)
```

## Role Descriptions

### 1. Super Admin

**Purpose**: Complete system control and administration

**Who should have this role**: System administrators, technical staff

**Permissions**: ALL permissions in the system

**Key Capabilities**:
- Full access to all modules
- Can manage roles and permissions
- Can manage system settings
- Can perform all administrative tasks
- Can delete any records

**Use Cases**:
- Initial system setup
- Critical system maintenance
- Security configuration
- Emergency access

---

### 2. Admin

**Purpose**: Business administration and management

**Who should have this role**: HR managers, office administrators, department heads

**Key Capabilities**:
- Manage users (view, create, edit - but not delete)
- Manage companies and departments
- Full control over employees and contracts
- Approve contracts and attendance
- Process and approve payroll
- Generate and export reports
- Manage documents

**Permissions** (48 permissions):
- User Management: view, create, edit
- Company Management: view, create, edit
- Employee Management: view, create, edit, delete
- Department Management: view, create, edit, delete
- Contract Management: view, create, edit, approve
- Document Management: view, upload, download, delete
- Attendance Management: view, edit, approve
- Leave Management: view, approve, reject
- Payroll Management: view, process, approve, export
- Reports: view, generate, export

**Limitations**:
- Cannot delete users
- Cannot delete companies
- Cannot manage system settings
- Cannot manage roles/permissions

---

### 3. Manager

**Purpose**: Day-to-day operational management

**Who should have this role**: Team leaders, project managers, supervisors

**Key Capabilities**:
- Manage employees and departments
- Create and manage contracts
- Record and approve attendance
- Approve leave requests
- View payroll information
- Generate reports
- Manage documents

**Permissions** (24 permissions):
- Employee Management: view, create, edit
- Department Management: view, create, edit
- Contract Management: view, create, edit
- Document Management: view, upload, download
- Attendance Management: view, record, approve
- Leave Management: view, approve, reject
- Payroll Management: view
- Reports: view, generate

**Limitations**:
- Cannot delete employees
- Cannot delete departments or contracts
- Cannot process payroll
- Cannot delete documents
- Cannot manage users or companies

---

### 4. Employee

**Purpose**: Basic employee self-service

**Who should have this role**: Regular employees, staff members

**Key Capabilities**:
- View and download own documents
- View and record own attendance
- Request leave
- View own payroll information

**Permissions** (7 permissions):
- Document Management: view, download (own documents)
- Attendance Management: view, record (own attendance)
- Leave Management: view, request (own leave)
- Payroll Management: view (own payroll)

**Limitations**:
- Can only access own records
- Cannot manage other employees
- Cannot approve requests
- Cannot access reports
- Cannot manage any system data

---

## Role Assignment

### API Endpoint
```http
POST /api/users/{id}/assign-role
Authorization: Bearer {token}
Content-Type: application/json

{
    "role": "Manager"
}
```

### In Code
```php
// Assign role to user
$user->assignRole('Manager');

// Check if user has role
if ($user->hasRole('Manager')) {
    // User is a manager
}

// Get user's roles
$roles = $user->getRoleNames();

// Remove role
$user->removeRole('Employee');
```

## Permission Assignment to Roles

### API Endpoint
```http
POST /api/roles/{id}/permissions
Authorization: Bearer {token}
Content-Type: application/json

{
    "permissions": [
        "view-users",
        "create-users"
    ]
}
```

### In Code
```php
// Assign permissions to role
$role->givePermissionTo(['view-users', 'create-users']);

// Sync permissions (replace existing)
$role->syncPermissions(['view-users', 'create-users']);

// Check if role has permission
if ($role->hasPermissionTo('view-users')) {
    // Role has permission
}
```

## Custom Roles

You can create custom roles for specific organizational needs:

```http
POST /api/roles
Authorization: Bearer {token}
Content-Type: application/json

{
    "name": "Accountant",
    "guard_name": "web"
}
```

Then assign specific permissions:

```http
POST /api/roles/{id}/permissions
Authorization: Bearer {token}
Content-Type: application/json

{
    "permissions": [
        "view-payroll",
        "process-payroll",
        "approve-payroll",
        "export-payroll",
        "view-reports",
        "generate-reports"
    ]
}
```

## Best Practices

1. **Start with Predefined Roles**: Use the standard roles as a starting point
2. **Role-Based, Not User-Based**: Assign permissions to roles, not individual users
3. **Minimize Super Admin Usage**: Only assign Super Admin to trusted technical staff
4. **Regular Reviews**: Audit role assignments quarterly
5. **Document Custom Roles**: Keep track of any custom roles created and their purpose
6. **Test Role Changes**: Always test permission changes in a non-production environment
7. **Principle of Least Privilege**: Assign the minimum role necessary for job function

## Troubleshooting

### User Can't Access Feature
1. Check if user has the required role: `$user->getRoleNames()`
2. Check if role has the required permission: `$role->permissions`
3. Clear permission cache: `php artisan permission:cache-reset`

### Permission Not Working
1. Ensure permission exists in database
2. Check middleware is applied to route
3. Verify user is authenticated
4. Clear application cache: `php artisan cache:clear`

## Migration Path

When updating from older systems:

1. Run migrations: `php artisan migrate`
2. Seed roles and permissions: `php artisan db:seed --class=RolesAndPermissionsSeeder`
3. Assign roles to existing users based on their current access level
4. Test thoroughly before deploying to production
