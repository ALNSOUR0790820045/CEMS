# Roles & Permissions Integration Guide

This guide explains how to integrate the Roles & Permissions system into your CEMS application.

## Table of Contents
1. [Installation](#installation)
2. [Configuration](#configuration)
3. [Database Setup](#database-setup)
4. [API Endpoints](#api-endpoints)
5. [Usage Examples](#usage-examples)
6. [Middleware](#middleware)
7. [Testing](#testing)

## Installation

The system uses Spatie's Laravel Permission package, which is already installed.

### Verify Installation

```bash
composer show spatie/laravel-permission
```

## Configuration

The permission configuration is located at `config/permission.php`. Key settings:

```php
'models' => [
    'permission' => Spatie\Permission\Models\Permission::class,
    'role' => Spatie\Permission\Models\Role::class,
],

'table_names' => [
    'roles' => 'roles',
    'permissions' => 'permissions',
    'model_has_permissions' => 'model_has_permissions',
    'model_has_roles' => 'model_has_roles',
    'role_has_permissions' => 'role_has_permissions',
],
```

## Database Setup

### Run Migrations

```bash
php artisan migrate
```

This creates the following tables:
- `roles`
- `permissions`
- `model_has_roles` (pivot table)
- `model_has_permissions` (pivot table)
- `role_has_permissions` (pivot table)

### Seed Roles and Permissions

```bash
php artisan db:seed --class=RolesAndPermissionsSeeder
```

This creates:
- 4 predefined roles (Super Admin, Admin, Manager, Employee)
- 50+ permissions across all modules
- Permission assignments for each role

### Verify Seeding

```bash
php artisan tinker
```

```php
// Check roles
App\Models\Role::count(); // Should return 4

// Check permissions
App\Models\Permission::count(); // Should return 50+

// Check role permissions
$role = App\Models\Role::where('name', 'Admin')->first();
$role->permissions->count(); // Should return 48
```

## API Endpoints

### Authentication

All API endpoints require authentication via Sanctum:

```http
Authorization: Bearer {your-api-token}
```

### Role Management

#### List All Roles
```http
GET /api/roles
```

Response:
```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "name": "Super Admin",
            "guard_name": "web",
            "permissions": [...]
        }
    ]
}
```

#### Create Role
```http
POST /api/roles
Content-Type: application/json

{
    "name": "Accountant",
    "guard_name": "web",
    "company_id": 1
}
```

#### Get Single Role
```http
GET /api/roles/{id}
```

#### Update Role
```http
PUT /api/roles/{id}
Content-Type: application/json

{
    "name": "Senior Accountant"
}
```

#### Assign Permissions to Role
```http
POST /api/roles/{id}/permissions
Content-Type: application/json

{
    "permissions": [
        "view-payroll",
        "process-payroll"
    ]
}
```

### Permission Management

#### List All Permissions
```http
GET /api/permissions
```

#### Create Permission
```http
POST /api/permissions
Content-Type: application/json

{
    "name": "approve-invoices",
    "guard_name": "web",
    "module": "invoices"
}
```

### User Role Assignment

#### Assign Role to User
```http
POST /api/users/{id}/assign-role
Content-Type: application/json

{
    "role": "Manager"
}
```

## Usage Examples

### In Controllers

```php
use App\Models\User;
use App\Models\Role;

class UserController extends Controller
{
    public function store(Request $request)
    {
        // Check if current user has permission
        if (!$request->user()->can('create-users')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $user = User::create($request->validated());
        
        // Assign role to new user
        $user->assignRole('Employee');
        
        return response()->json($user);
    }

    public function destroy(User $user)
    {
        // Check permission before deleting
        $this->authorize('delete-users');
        
        $user->delete();
        
        return response()->json(['message' => 'User deleted']);
    }
}
```

### In Routes

```php
use App\Http\Controllers\EmployeeController;

// Protect entire resource with permission
Route::middleware(['auth:sanctum', 'permission:manage-employees'])
    ->resource('employees', EmployeeController::class);

// Protect specific routes with different permissions
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/employees', [EmployeeController::class, 'index'])
        ->middleware('permission:view-employees');
    
    Route::post('/employees', [EmployeeController::class, 'store'])
        ->middleware('permission:create-employees');
    
    Route::delete('/employees/{id}', [EmployeeController::class, 'destroy'])
        ->middleware('permission:delete-employees');
});

// Protect with role
Route::middleware(['auth:sanctum', 'role:Admin'])
    ->get('/admin/dashboard', [AdminController::class, 'dashboard']);
```

### In Blade Views

```blade
@role('Super Admin')
    <a href="/admin/settings">System Settings</a>
@endrole

@can('edit-users')
    <button class="btn-edit">Edit User</button>
@endcan

@canany(['approve-contracts', 'edit-contracts'])
    <button class="btn-manage">Manage Contract</button>
@endcanany

@hasrole('Manager')
    <div class="manager-panel">...</div>
@endhasrole
```

### Direct Permission/Role Checks

```php
// Check permission
if ($user->hasPermissionTo('edit-users')) {
    // User has permission
}

// Check role
if ($user->hasRole('Manager')) {
    // User has role
}

// Check any role
if ($user->hasAnyRole(['Admin', 'Super Admin'])) {
    // User has at least one role
}

// Check all roles
if ($user->hasAllRoles(['Manager', 'Employee'])) {
    // User has all specified roles
}

// Get all permissions
$permissions = $user->getAllPermissions();

// Get all roles
$roles = $user->getRoleNames();
```

## Middleware

### Available Middleware

1. **permission** - Checks if user has specific permission
2. **role** - Checks if user has specific role

### Usage

```php
// Single permission
Route::middleware('permission:create-users')->group(function () {
    // Routes
});

// Multiple permissions (OR)
Route::middleware('permission:create-users|edit-users')->group(function () {
    // Routes
});

// Multiple permissions (AND)
Route::middleware('permission:create-users,edit-users')->group(function () {
    // Routes - user must have both permissions
});

// Single role
Route::middleware('role:Admin')->group(function () {
    // Routes
});

// Multiple roles (OR)
Route::middleware('role:Admin|Manager')->group(function () {
    // Routes
});
```

## Testing

### Run All Tests

```bash
php artisan test
```

### Run Specific Test Suite

```bash
# Role management tests
php artisan test --filter=RoleManagementTest

# Permission assignment tests
php artisan test --filter=PermissionAssignmentTest

# Route protection tests
php artisan test --filter=RouteProtectionTest

# User role assignment tests
php artisan test --filter=UserRoleAssignmentTest
```

### Example Test

```php
use App\Models\User;
use App\Models\Role;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class FeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_manager_can_view_employees()
    {
        $manager = User::factory()->create();
        $manager->assignRole('Manager');

        $response = $this->actingAs($manager, 'sanctum')
            ->getJson('/api/employees');

        $response->assertStatus(200);
    }

    public function test_employee_cannot_delete_users()
    {
        $employee = User::factory()->create();
        $employee->assignRole('Employee');
        
        $targetUser = User::factory()->create();

        $response = $this->actingAs($employee, 'sanctum')
            ->deleteJson('/api/users/' . $targetUser->id);

        $response->assertStatus(403);
    }
}
```

## Troubleshooting

### Cache Issues

If permissions don't seem to work after changes:

```bash
# Clear permission cache
php artisan permission:cache-reset

# Clear application cache
php artisan cache:clear

# Clear config cache
php artisan config:clear
```

### Common Errors

**Error: "Permission does not exist"**
- Solution: Ensure permission is created in database
- Check: `Permission::where('name', 'permission-name')->exists()`

**Error: "User does not have permission"**
- Solution: Verify user has role with permission
- Check: `$user->getAllPermissions()` and `$user->getRoleNames()`

**Error: "Middleware not found"**
- Solution: Ensure middleware is registered in `bootstrap/app.php`
- Check the `withMiddleware` configuration

## Best Practices

1. **Always use permissions, not roles** in your code when checking access
2. **Clear cache** after making permission changes
3. **Test thoroughly** before deploying to production
4. **Document custom permissions** in PERMISSIONS.md
5. **Use role hierarchy** - don't give employees admin permissions
6. **Seed in order** - create permissions first, then roles, then assign
7. **Regular audits** - review who has what permissions quarterly

## Additional Resources

- [Spatie Laravel Permission Documentation](https://spatie.be/docs/laravel-permission)
- [PERMISSIONS.md](./PERMISSIONS.md) - Complete permission list
- [ROLES.md](./ROLES.md) - Role descriptions and capabilities
- [Laravel Authorization](https://laravel.com/docs/authorization)
