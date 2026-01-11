# Database Naming Conventions

**Version:** 1.0  
**Last Updated:** 2026-01-10  
**Status:** Active Standard

---

## 📋 Overview

This document defines the naming conventions for database tables, columns, constraints, and indexes in the CEMS (Construction Enterprise Management System) project.

---

## 🎯 Core Principles

1. **Consistency** - Use the same patterns throughout
2. **Clarity** - Names should be self-documenting
3. **Simplicity** - Avoid unnecessary complexity
4. **Laravel Conventions** - Follow Laravel/Eloquent best practices

---

## 📊 Table Names

### Rules

- ✅ **Use:** `snake_case` (lowercase with underscores)
- ✅ **Use:** Plural form (e.g., `users`, `projects`, `tenders`)
- ✅ **Length:** Prefer 2-3 words maximum
- ❌ **Avoid:** CamelCase, PascalCase, or UPPERCASE

### Examples

```sql
✅ Good Examples:
- companies
- projects
- tenders
- tender_site_visits
- risk_registers
- project_wbs
- a_r_receipts (Accounts Receivable Receipts)
- boq_items (Bill of Quantities Items)

❌ Bad Examples:
- Company (not plural)
- ProjectWBS (not snake_case)
- TENDERS (not lowercase)
- TenderSiteVisit (not snake_case/plural)
```

### Pivot/Junction Tables

For many-to-many relationships:

```sql
Format: {table1_singular}_{table2_singular}
Example: project_user, tender_competitor
```

---

## 🔤 Column Names

### Primary Keys

```sql
✅ Standard: id
Type: bigint UNSIGNED AUTO_INCREMENT
Laravel: $table->id()
```

### Foreign Keys

```sql
Format: {singular_table_name}_id
Type: bigint UNSIGNED

✅ Examples:
- company_id → references companies(id)
- project_id → references projects(id)
- tender_id → references tenders(id)
- risk_register_id → references risk_registers(id)

❌ Avoid:
- companyId (camelCase)
- company (missing _id suffix)
- fk_company (unnecessary prefix)
```

### Timestamp Columns

```sql
✅ Standard Laravel timestamps:
- created_at (TIMESTAMP NULL)
- updated_at (TIMESTAMP NULL)
- deleted_at (TIMESTAMP NULL) for soft deletes

✅ Custom timestamps:
- issued_at
- approved_at
- submitted_at
- closed_at

Format: {action}_at
```

### Date Columns

```sql
Format: {purpose}_date
Type: DATE

✅ Examples:
- start_date
- end_date
- issue_date
- closing_date
- identification_date
- notification_date

❌ Avoid:
- startDate (camelCase)
- date_start (reversed order)
```

### Boolean Columns

```sql
Format: is_{adjective}, has_{noun}, can_{verb}
Type: BOOLEAN (tinyint(1))
Default: Usually false

✅ Examples:
- is_active
- is_approved
- is_completed
- has_attachments
- has_sub_items
- can_edit
- can_delete

❌ Avoid:
- active (ambiguous type)
- approved (ambiguous type)
- flag_active (unnecessary prefix)
```

### Enum Columns

```sql
Format: {purpose} (no suffix)
Type: ENUM or VARCHAR

✅ Examples:
- status ('draft', 'active', 'completed')
- priority ('low', 'medium', 'high', 'critical')
- type ('incoming', 'outgoing')
- category ('technical', 'financial', 'safety')

Values: snake_case lowercase
```

### Monetary Columns

```sql
Format: {purpose}_{amount|cost|value|price}
Type: DECIMAL(15, 2)

✅ Examples:
- contract_value
- total_amount
- unit_price
- labor_cost
- estimated_value

❌ Avoid:
- price (ambiguous - unit? total?)
- cost (ambiguous - what cost?)
```

### Text Columns

```sql
Short text:
- name, title, subject
Type: VARCHAR(255)

Long text:
- description, notes, remarks, comments
Type: TEXT

Format: Singular noun
```

---

## 🔗 Constraints

### Foreign Key Constraints

```sql
Format: {child_table}_{parent_table}_foreign
Laravel: Automatically generated

Example:
projects_company_id_foreign
risks_risk_register_id_foreign
tender_site_visits_tender_id_foreign
```

### Unique Constraints

```sql
Format: {table}_{column}_unique
Laravel: Automatically generated

Example:
companies_registration_number_unique
projects_project_number_unique
tenders_tender_number_unique
```

---

## 📇 Indexes

### Single Column Index

```sql
Format: {table}_{column}_index
Laravel: $table->index('column')

Example:
projects_status_index
tenders_closing_date_index
```

### Composite Index

```sql
Format: {table}_{col1}_{col2}_index
Laravel: $table->index(['col1', 'col2'])

Example:
projects_company_id_status_index
risks_project_id_risk_level_index
```

### Full-Text Index

```sql
Format: {table}_{column}_fulltext
Laravel: $table->fullText('column')

Example:
projects_description_fulltext
tenders_title_fulltext
```

---

## 🎨 Current Schema Analysis

### Consistent Patterns Found

✅ **Well-Named Tables:**
- companies, projects, tenders, contracts
- tender_site_visits, tender_clarifications
- risk_registers, risks, risk_assessments
- project_wbs, boq_items, boq_sections

✅ **Consistent Foreign Keys:**
- company_id, project_id, tender_id
- user_id, employee_id, client_id

✅ **Good Boolean Names:**
- is_active, is_approved, is_completed
- has_attachments, can_edit

### Inconsistencies to Fix

⚠️ **Mixed Terminology:**
- Some tables use `description`, others use `notes`
- Some use `remarks`, others use `comments`
- **Recommendation:** Standardize on:
  - `description` for detailed explanations
  - `notes` for internal comments
  - `remarks` for additional observations

⚠️ **Number/Reference Fields:**
- Some use `{entity}_number`, others use `reference_number`
- **Current standard:** Use `{entity}_number` (e.g., project_number, tender_number)

⚠️ **Abbreviations:**
- Generally avoided (good!)
- Exception: Common acronyms like WBS, BOQ, IPC, EOT, AR, AP, GL
- **Keep:** These are industry-standard in construction

---

## 📝 Migration File Naming

### Format

```
YYYY_MM_DD_HHMMSS_create_{table_name}_table.php
YYYY_MM_DD_HHMMSS_add_{column}_to_{table}_table.php
YYYY_MM_DD_HHMMSS_modify_{table}_table.php
```

### Examples

```
✅ Good:
2026_01_02_121900_create_companies_table.php
2026_01_02_122000_create_projects_table.php
2026_01_10_170005_add_currency_to_branches_table.php

❌ Avoid:
CreateCompaniesTable.php (no timestamp)
2026_01_02_companies.php (not descriptive)
```

---

## 🔍 Laravel Eloquent Conventions

### Model Relationships

```php
// Foreign key: company_id
public function company()
{
    return $this->belongsTo(Company::class);
}

// Inverse (one-to-many)
public function projects()
{
    return $this->hasMany(Project::class);
}

// Pivot table: project_user
public function users()
{
    return $this->belongsToMany(User::class);
}
```

### Accessor/Mutator Naming

```php
// Column: start_date
protected function startDate(): Attribute
{
    return Attribute::make(
        get: fn ($value) => Carbon::parse($value),
    );
}
```

---

## ✅ Validation Checklist

When creating a new migration, verify:

- [ ] Table name is plural, snake_case, lowercase
- [ ] Primary key is `id` using `$table->id()`
- [ ] Foreign keys end with `_id` and use `foreignId()`
- [ ] Timestamps use `$table->timestamps()`
- [ ] Soft deletes use `$table->softDeletes()`
- [ ] Boolean columns start with `is_`, `has_`, or `can_`
- [ ] Date columns end with `_date`
- [ ] Enum values are snake_case lowercase
- [ ] Indexes are added for frequently queried columns
- [ ] Foreign keys reference existing tables (parent created first)

---

## 📚 References

- [Laravel Database Migrations](https://laravel.com/docs/migrations)
- [Laravel Eloquent Relationships](https://laravel.com/docs/eloquent-relationships)
- [Laravel Naming Conventions](https://laravel.com/docs/eloquent#eloquent-model-conventions)

---

## 🔄 Change Log

| Version | Date | Changes |
|---------|------|---------|
| 1.0 | 2026-01-10 | Initial comprehensive naming conventions document |

---

**Maintained by:** CEMS Development Team  
**Questions?** Review this document before creating new migrations.
