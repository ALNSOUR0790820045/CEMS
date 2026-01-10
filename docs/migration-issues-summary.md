# Migration Analysis Summary

**Generated**: 2026-01-10  
**Total Migrations**: 349  
**Total Tables**: 335  
**Total Foreign Keys**: 939  

## Issue Statistics

- **🔴 Critical Issues**: 73
- **🟡 Medium Issues**: 51  
- **🟢 Low Issues**: 11

## Critical Issues Breakdown

### Issue Category 1: Same-Timestamp Dependencies (48 issues)

**Problem**: Multiple tables created in the same migration file reference each other, but there's no guarantee of creation order within the same file.

**Examples**:
1. `activity_dependencies` → `project_activities` (both in `2026_01_02_211654`)
2. `daily_report_photos` → `daily_reports` (both in `2026_01_02_211857`)
3. `tender_wbs_boq_mapping` → `tender_wbs`, `tender_boq_items` (both in `2026_01_02_214414`)
4. `cost_plus_invoice_items` → `cost_plus_invoices`, `cost_plus_transactions` (all in `2026_01_04_211131`)

**Solution**:
- **Option A (Recommended)**: Keep tables in the same file but ensure parent tables are created FIRST
- **Option B**: Split into separate migration files with different timestamps

**Example Fix**:
```php
// BEFORE (in same file 2026_01_02_211654):
// File: create_activity_dependencies_table.php
Schema::create('activity_dependencies', function (Blueprint $table) {
    $table->foreignId('predecessor_id')->constrained('project_activities');
    // ...
});

// File: create_project_activities_table.php  
Schema::create('project_activities', function (Blueprint $table) {
    // ...
});

// AFTER: Ensure activities is created first
// Rename migrations to enforce order:
// 2026_01_02_211654_create_project_activities_table.php (first)
// 2026_01_02_211655_create_activity_dependencies_table.php (second)
```

### Issue Category 2: Wrong Creation Order (8 issues)

**Problem**: Child table is created BEFORE parent table it references.

**Examples**:
1. `photo_albums` (2026_01_09_152024) → `photos` (2026_01_09_152031)
2. `checks` (2026_01_10_170002) → `payment_templates` (2026_01_10_170004)
3. `promissory_notes` (2026_01_10_170003) → `payment_templates` (2026_01_10_170004)
4. `time_bar_events` (2026_01_04_210005) → `correspondence` (2026_01_04_211830)

**Solution**: Rename migration files to ensure parent is created first.

**Example Fix**:
```bash
# Current order (WRONG):
# 2026_01_10_170002_create_checks_table.php (references payment_templates)
# 2026_01_10_170003_create_promissory_notes_table.php (references payment_templates)
# 2026_01_10_170004_create_payment_templates_table.php

# Fixed order (CORRECT):
# 2026_01_10_170001_create_payment_templates_table.php (parent first)
# 2026_01_10_170002_create_checks_table.php
# 2026_01_10_170003_create_promissory_notes_table.php
```

### Issue Category 3: Missing Referenced Tables (17 issues)

**Problem**: Tables reference other tables that don't exist in migrations (incorrect table name guessing).

**Examples**:
1. Multiple tables reference `equipments` but table is `equipment` (singular)
2. Multiple tables reference `journal_entrys` but table is `journal_entries`
3. Multiple tables reference `site_diarys` but table is `site_diaries`
4. Multiple tables reference `correspondences` but table is `correspondence` (singular)
5. Multiple tables reference `defects_liabilitys` but table is `defects_liability`
6. Multiple tables reference `tender_activitys` but table is `tender_activities`
7. `document_access` references `roles` (from spatie/permissions package, not in migrations)
8. Several tables reference `cost_categorys` but table is `cost_categories`

**Solution**: Fix the foreign key reference to use the correct table name.

**Example Fix**:
```php
// WRONG:
$table->foreignId('equipment_id')->constrained(); // looks for 'equipments'

// CORRECT (Option 1 - specify table name):
$table->foreignId('equipment_id')->constrained('equipment');

// CORRECT (Option 2 - rename column to match table):
$table->foreignId('equipment_item_id')->constrained('equipment');

// For site_diary references:
// WRONG:
$table->foreignId('site_diary_id')->constrained(); // looks for 'site_diarys'

// CORRECT:
$table->foreignId('site_diary_id')->constrained('site_diaries');
```

## Medium Issues (51)

### Data Type Inconsistencies

**Decimal Precision Inconsistencies**:
- `amount` column varies between (10,2), (15,2), and (18,2)
- `exchange_rate` varies between (8,4) and (10,4)
- `quantity` varies between (10,2), (15,3), and (12,2)

**String Length Inconsistencies**:
- `code` column varies between 10, 20, 50, and 255 (default)
- `name` column sometimes specified as 100, 150, 200, or 255
- `description` alternates between `string(500)` and `text`

**Recommendation**: Standardize according to `/docs/database-standards.md`:
- Standard amounts: `decimal(15, 2)`
- Large amounts: `decimal(18, 2)`
- Exchange rates: `decimal(10, 4)`
- Quantities: `decimal(15, 3)`
- Codes: `string(50)`
- Names: `string(255)`
- Descriptions: `text`

## Low Issues (11)

### Naming Convention Issues

**Inconsistent Table Names**:
1. `a_r_receipts` → Should be `ar_receipts` or `accounts_receivable_receipts`
2. `a_r_invoices` → Should be `ar_invoices` or `accounts_receivable_invoices`
3. `a_r_receipt_allocations` → Should be `ar_receipt_allocations`
4. `g_l_accounts` → Should be `gl_accounts` or `general_ledger_accounts`
5. `g_r_n_items` → Should be `grn_items` or `goods_receipt_note_items`
6. `i_p_c_s` → Should be `ipcs` or `interim_payment_certificates`

**Decision Needed**: Choose between abbreviated (shorter) or full names (clearer).

## Specific Module Issues

### Risk Management Module ✅ CONFIRMED ISSUE
```
Status: ✅ SAME TIMESTAMP ISSUE CONFIRMED
Files: All with timestamp 2026_01_09_152841

Dependencies:
- risk_registers (parent) - MUST BE CREATED FIRST
- risks (references risk_registers)
- risk_assessments (references risks)
- risk_incidents (references risks)
- risk_monitoring (references risks)
- risk_responses (references risks)

Fix Required: Rename migrations to enforce order:
1. 2026_01_09_152841_create_risk_registers_table.php
2. 2026_01_09_152842_create_risks_table.php
3. 2026_01_09_152843_create_risk_assessments_table.php
4. etc...
```

### Accounts Receivable Module ✅ DEPENDENCIES OK
```
Status: ✅ NO ISSUES FOUND

Dependencies verified:
- clients (2026_01_03_114219) ✅ Created before
- currencies (2026_01_02_143849) ✅ Created before  
- bank_accounts (2026_01_03_200000) ✅ Created before
- a_r_receipts (2026_01_03_200918) ✅ References all correctly
```

### Payroll Module ✅ ORDER CORRECT
```
Status: ✅ CORRECT ORDER

Creation order:
1. payroll_periods (2026_01_03_200001)
2. payroll_entries (2026_01_03_200002) - references payroll_periods ✅
3. payroll_allowances (2026_01_03_200003) - references payroll_entries ✅
4. payroll_deductions (2026_01_03_200004) - references payroll_entries ✅
```

### BOQ Module ⚠️ SAME TIMESTAMP ISSUE
```
Status: ⚠️ SAME TIMESTAMP ISSUES

Files with timestamp 2026_01_04_203018:
- boq_headers (parent)
- boq_sections (references boq_headers) ⚠️
- boq_revisions (references boq_headers) ⚠️
- boq_item_resources (references boq_items)

Also note:
- boq_items (2026_01_02_122200) created BEFORE boq_headers!
  But doesn't reference boq_headers, so OK

Fix: Rename to ensure order:
1. 2026_01_04_203018_create_boq_headers_table.php
2. 2026_01_04_203019_create_boq_sections_table.php
3. 2026_01_04_203020_create_boq_revisions_table.php
4. 2026_01_04_203021_create_boq_item_resources_table.php
```

### Branches & Currencies Module ✅ ORDER CORRECT
```
Status: ✅ CORRECT ORDER

Creation order:
1. currencies (2026_01_02_143849) ✅
2. branches (2026_01_02_145000) ✅
3. add_currency_to_branches (2026_01_10_170005) ✅ Uses Schema::table() correctly
```

## Recommended Action Plan

### Phase 1: Fix Critical Foreign Key Ordering (Priority 1)
1. Fix same-timestamp dependencies (48 issues)
   - Risk Management module (5 files)
   - BOQ module (4 files)
   - Cost Plus module (5 files)
   - All other same-timestamp sets

2. Fix wrong creation order (8 issues)
   - photo_albums → photos
   - checks/promissory_notes → payment_templates
   - time_bar_events → correspondence

### Phase 2: Fix Missing Table References (Priority 2)
3. Fix incorrect table name references (17 issues)
   - Equipment references (use 'equipment' not 'equipments')
   - Site diary references (use 'site_diaries' not 'site_diarys')
   - Correspondence references (use 'correspondence' not 'correspondences')

### Phase 3: Standardize Data Types (Priority 3)
4. Standardize decimal precisions
5. Standardize string lengths

### Phase 4: Naming Conventions (Priority 4)
6. Decide on naming convention (abbreviated vs full)
7. Optionally rename inconsistent tables (requires code changes)

## Testing Strategy

After fixes:
1. Run migrations on fresh database: `php artisan migrate:fresh`
2. Verify no errors
3. Check foreign key constraints: `SHOW CREATE TABLE {table_name}`
4. Test cascade deletes work correctly
5. Run re-analysis: `php scripts/analyze-migrations.php`

## Tools Created

1. **`scripts/analyze-migrations.php`** - Comprehensive analyzer
2. **`reports/migration-analysis-report.json`** - Detailed JSON report
3. **`docs/database-standards.md`** - Database standards guide
4. **This document** - Human-readable summary

## Next Steps

1. Review this summary
2. Decide on approach for each issue category
3. Implement fixes systematically
4. Re-run analyzer to verify fixes
5. Test migrations on fresh database
6. Document any schema changes in application code
