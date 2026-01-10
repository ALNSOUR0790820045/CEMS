# Database Standards and Conventions

## Overview
This document defines the database standards and conventions for the CEMS (Construction Enterprise Management System) project.

## Column Naming Conventions

### Primary Keys
- **Format**: `id`
- **Type**: `bigint unsigned` (auto-incrementing)
- **Example**: `$table->id();`

### Foreign Keys
- **Format**: `{table}_id` (singular form of referenced table)
- **Type**: `bigint unsigned`
- **Constraints**: Always use `constrained()` with cascade rules
- **Examples**:
  ```php
  $table->foreignId('company_id')->constrained()->cascadeOnDelete();
  $table->foreignId('parent_id')->nullable()->constrained('same_table')->cascadeOnDelete();
  $table->foreignId('created_by_id')->constrained('users')->cascadeOnDelete();
  ```

### Timestamps
- **Standard Laravel timestamps**: `created_at`, `updated_at`
- **Custom timestamps**: Use descriptive names ending in `_at`
- **Examples**: `approved_at`, `submitted_at`, `completed_at`

### Soft Deletes
- **Column**: `deleted_at`
- **Implementation**: `$table->softDeletes();`

### User References
- **Created by**: `created_by_id` (references `users`)
- **Updated by**: `updated_by_id` (references `users`)
- **Owner**: `owner_id` or `{role}_id` (e.g., `manager_id`, `approved_by_id`)

## Data Types

### Monetary Values
| Use Case | Type | Precision | Example |
|----------|------|-----------|---------|
| Standard amounts | `decimal` | 15, 2 | `$table->decimal('amount', 15, 2)` |
| Large amounts (contracts) | `decimal` | 18, 2 | `$table->decimal('contract_value', 18, 2)` |
| Percentages | `decimal` | 5, 2 | `$table->decimal('tax_rate', 5, 2)` |
| Exchange rates | `decimal` | 10, 4 | `$table->decimal('exchange_rate', 10, 4)` |
| Quantities | `decimal` | 15, 3 | `$table->decimal('quantity', 15, 3)` |

### String Types
| Use Case | Type | Length | Example |
|----------|------|--------|---------|
| Codes | `string` | 20-50 | `$table->string('code', 50)` |
| Names | `string` | 255 | `$table->string('name')` |
| Short descriptions | `string` | 255 | `$table->string('description')` |
| Long descriptions | `text` | - | `$table->text('description')` |
| Email addresses | `string` | 255 | `$table->string('email')` |
| Phone numbers | `string` | 20 | `$table->string('phone', 20)` |

### Enum Types
- Use enum for fields with predefined, limited set of values
- Use lowercase with underscores for values
- Example:
  ```php
  $table->enum('status', ['draft', 'pending', 'approved', 'rejected'])->default('draft');
  ```

### Boolean Types
- Use meaningful names starting with `is_` or `has_`
- Examples: `is_active`, `is_approved`, `has_attachment`
- Always set a default value

### Date and Time
| Type | Use Case | Example |
|------|----------|---------|
| `date` | Dates without time | `$table->date('start_date')` |
| `datetime` | Dates with time | `$table->datetime('submitted_at')` |
| `timestamp` | Laravel timestamps | `$table->timestamps()` |
| `time` | Time only | `$table->time('start_time')` |

## Table Naming Conventions

### General Rules
1. **Use plural form**: `projects`, `companies`, `users`
2. **Use snake_case**: `purchase_orders`, `project_activities`
3. **Be descriptive**: Avoid abbreviations unless widely understood

### Pivot Tables
- **Format**: `{table1}_{table2}` (alphabetically ordered)
- **Example**: `project_user`, `role_permission`

### Special Cases
- **Avoid inconsistent underscores**: 
  - ❌ `a_r_receipts` (too many underscores)
  - ✅ `ar_receipts` or `accounts_receivable_receipts`
  
## Indexes

### Automatic Indexes
- Primary keys are automatically indexed
- Foreign keys should always be indexed

### Performance Indexes
```php
// Single column indexes
$table->index('status');
$table->index('created_at');

// Composite indexes (order matters!)
$table->index(['project_id', 'status']);
$table->index(['company_id', 'created_at']);
```

### Unique Constraints
```php
// Single column unique
$table->unique('email');

// Composite unique
$table->unique(['company_id', 'code']);
```

## Foreign Key Constraints

### Cascade Rules
| Rule | Use Case |
|------|----------|
| `cascadeOnDelete()` | Delete child records when parent is deleted |
| `cascadeOnUpdate()` | Update child records when parent key changes |
| `nullOnDelete()` | Set foreign key to NULL when parent is deleted |
| `restrictOnDelete()` | Prevent parent deletion if children exist |

### Examples
```php
// Delete children when parent is deleted
$table->foreignId('project_id')->constrained()->cascadeOnDelete();

// Set to NULL when parent is deleted (optional relationship)
$table->foreignId('approved_by_id')->nullable()->constrained('users')->nullOnDelete();
```

## Migration Best Practices

### File Naming
- Use descriptive names: `create_{table}_table.php`
- For modifications: `add_{column}_to_{table}_table.php`
- For complex changes: `update_{feature}_structure.php`

### Migration Order
1. **Core tables first**: users, companies, roles, permissions
2. **Reference tables**: currencies, countries, cities, units
3. **Business tables**: projects, contracts, tenders
4. **Detail tables**: invoice_items, order_items
5. **Modification migrations**: alter table, add column

### Dependencies
- Ensure referenced tables are created before tables that reference them
- For same-timestamp migrations, create parent tables first in the file
- Avoid circular dependencies

## Data Integrity

### Nullable Fields
- **Foreign keys**: Usually NOT nullable unless it's an optional relationship
  ```php
  // Required relationship
  $table->foreignId('company_id')->constrained();
  
  // Optional relationship
  $table->foreignId('parent_id')->nullable()->constrained('categories');
  ```

### Default Values
- Always provide sensible defaults for status fields
- Use `default()` for boolean, enum, and numeric fields
  ```php
  $table->boolean('is_active')->default(true);
  $table->enum('status', ['draft', 'active'])->default('draft');
  $table->integer('sort_order')->default(0);
  ```

### Check Constraints (Laravel 10.40+)
```php
// Ensure positive values
$table->decimal('amount', 15, 2);
$table->check('amount >= 0');

// Ensure range
$table->integer('rating');
$table->check('rating BETWEEN 1 AND 5');

// Ensure min <= max
$table->decimal('min_value', 10, 2);
$table->decimal('max_value', 10, 2);
$table->check('min_value <= max_value');
```

## Common Patterns

### Approval Workflow
```php
$table->enum('status', ['draft', 'pending', 'approved', 'rejected'])->default('draft');
$table->foreignId('submitted_by_id')->nullable()->constrained('users');
$table->timestamp('submitted_at')->nullable();
$table->foreignId('approved_by_id')->nullable()->constrained('users');
$table->timestamp('approved_at')->nullable();
$table->text('rejection_reason')->nullable();
```

### Audit Fields
```php
$table->foreignId('created_by_id')->constrained('users');
$table->foreignId('updated_by_id')->nullable()->constrained('users');
$table->timestamps();
$table->softDeletes();
```

### Multi-Currency Support
```php
$table->decimal('amount', 15, 2);
$table->foreignId('currency_id')->constrained();
$table->decimal('exchange_rate', 10, 4)->default(1);
$table->decimal('amount_in_base_currency', 15, 2);
```

### Hierarchical Data (Tree Structure)
```php
$table->foreignId('parent_id')->nullable()->constrained('same_table')->cascadeOnDelete();
$table->integer('level')->default(0);
$table->string('path')->nullable(); // e.g., "1/2/3"
$table->integer('sort_order')->default(0);
```

## Performance Considerations

### Indexing Strategy
1. Index foreign keys
2. Index columns used in WHERE clauses frequently
3. Index columns used in ORDER BY
4. Create composite indexes for common query patterns
5. Don't over-index (affects INSERT/UPDATE performance)

### Query Optimization
- Use appropriate data types (don't use VARCHAR(255) for everything)
- Use integers for IDs (not UUIDs unless necessary)
- Use enums for limited value sets
- Consider partitioning for very large tables

## Security Considerations

### Sensitive Data
- Never store plain-text passwords (use Laravel's hashing)
- Encrypt sensitive fields if needed
- Be careful with soft deletes on sensitive data

### SQL Injection Prevention
- Always use Eloquent or Query Builder
- Never concatenate user input into raw SQL
- Use parameter binding for raw queries

## Documentation
- Document complex foreign keys
- Add comments for non-obvious field purposes
- Keep migration files clean and readable

## Revision History
| Version | Date | Changes |
|---------|------|---------|
| 1.0 | 2026-01-10 | Initial documentation |
