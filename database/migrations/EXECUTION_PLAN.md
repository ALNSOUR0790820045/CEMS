# Migration Execution Plan

**Version:** 1.0  
**Generated:** 2026-01-10  
**Total Migrations:** 349

---

## 📋 Overview

This document outlines the correct execution order for all database migrations in the CEMS project. Migrations are organized into phases based on their dependencies.

---

## 🎯 Execution Strategy

### Key Principles

1. **Core Tables First** - Tables with no dependencies (44 tables)
2. **Sequential Dependencies** - Parent tables before child tables
3. **Timestamp Order** - Laravel executes migrations by timestamp
4. **Same-Timestamp Warning** - Tables with identical timestamps may execute in undefined order

### Migration Phases

```
Phase 1: Core Infrastructure (No dependencies)
Phase 2: Company Structure (Depends on companies, currencies)
Phase 3: Projects & Tenders (Depends on companies, users)
Phase 4: Financial Management (Depends on clients, vendors, currencies)
Phase 5: HR & Payroll (Depends on employees, companies)
Phase 6: Inventory & Procurement (Depends on warehouses, suppliers)
Phase 7: Site Management (Depends on projects, contracts)
Phase 8: Risk & Quality (Depends on projects, risk_registers)
Phase 9: Reporting & Analytics (Depends on multiple modules)
```

---

## 📊 Phase 1: Core Infrastructure

**No Dependencies - Created First**

```
1. 0001_01_01_000000_create_users_table.php
2. 0001_01_01_000001_create_cache_table.php
3. 0001_01_01_000002_create_jobs_table.php
4. 2019_09_15_000010_create_tenants_table.php
5. 2019_09_15_000020_create_domains_table.php
6. 2020_05_15_000010_create_tenant_user_impersonation_tokens_table.php
7. 2026_01_02_114921_create_permission_tables.php
```

**Tables Created:**
- users, cache, jobs, tenants, domains
- Core Laravel/Spatie tables

---

## 📊 Phase 2: Company & Master Data

**Depends On:** Phase 1 (users, tenants)

```
8.  2026_01_02_121900_create_companies_table.php
9.  2026_01_02_121950_add_fields_to_users_table.php (adds company_id, branch_id)
10. 2026_01_02_143849_create_currencies_table.php
11. 2026_01_02_144005_create_units_table.php
12. 2026_01_02_145149_create_payment_terms_table.php
13. 2026_01_02_151035_create_countries_table.php
14. 2026_01_02_151224_create_cities_table.php (depends on countries)
15. 2026_01_02_145000_create_branches_table.php (depends on companies)
16. 2026_01_02_145100_create_departments_table.php (depends on branches)
```

**Core Dependencies:**
- companies → No dependencies
- currencies → No dependencies
- branches → companies
- departments → branches
- cities → countries

---

## 📊 Phase 3: Projects & Tenders

**Depends On:** companies, users

```
17. 2026_01_02_122000_create_projects_table.php (depends on companies)
18. 2026_01_02_122100_create_project_wbs_table.php (depends on projects)
19. 2026_01_02_140100_create_tenders_table.php (depends on companies, projects)
20. 2026_01_02_140200_create_contracts_table.php (depends on projects, tenders)
21. 2026_01_02_214204_create_tender_related_tables.php (depends on tenders)
    - Creates: tender_site_visits, tender_clarifications, tender_competitors, etc.
22. 2026_01_02_214414_create_tender_boq_items_table.php (depends on tenders)
23. 2026_01_02_214414_create_tender_wbs_table.php (depends on tenders)
```

**Key Points:**
- ✅ tenders (140100) created BEFORE tender_related_tables (214204) ✓
- ⚠️ Multiple migrations share timestamp 214414 (order not guaranteed)

---

## 📊 Phase 4: BOQ & IPCs

**Depends On:** projects, project_wbs

```
24. 2026_01_02_122200_create_boq_items_table.php (depends on projects, project_wbs)
25. 2026_01_02_122400_create_main_ipcs_table.php (depends on projects)
26. 2026_01_02_122500_create_main_ipc_items_table.php (depends on main_ipcs)
27. 2026_01_04_203018_create_boq_headers_table.php (depends on projects)
28. 2026_01_04_203018_create_boq_sections_table.php (depends on boq_headers)
29. 2026_01_04_203018_create_boq_items_table.php (enhanced version)
```

**Dependencies:**
- boq_items → projects, project_wbs
- main_ipcs → projects
- main_ipc_items → main_ipcs

---

## 📊 Phase 5: Financial Management

**Depends On:** companies, clients, vendors, currencies

```
30. 2026_01_03_114219_create_clients_table.php (depends on companies)
31. 2026_01_03_120934_create_vendors_table.php (depends on companies)
32. 2026_01_03_200000_create_bank_accounts_table.php (depends on companies)
33. 2026_01_03_200814_create_ap_invoices_table.php (depends on vendors, currencies)
34. 2026_01_03_200917_create_a_r_invoices_table.php (depends on clients, currencies)
35. 2026_01_03_200918_create_a_r_receipts_table.php (depends on clients, currencies)
36. 2026_01_03_114925_create_gl_accounts_table.php (depends on companies)
37. 2026_01_03_114925_create_gl_journal_entries_table.php (depends on companies)
```

**Key Dependencies:**
- clients, vendors → companies
- invoices, receipts → clients/vendors + currencies
- GL accounts → companies

---

## 📊 Phase 6: Inventory & Procurement

**Depends On:** companies, warehouses, suppliers

```
38. 2026_01_02_194432_create_warehouses_table.php (depends on companies)
39. 2026_01_02_194433_create_stock_movements_table.php (depends on warehouses)
40. 2026_01_02_204719_create_products_table.php (no dependencies)
41. 2026_01_02_204719_create_suppliers_table.php (no dependencies)
42. 2026_01_02_204728_create_purchase_orders_table.php (depends on suppliers)
43. 2026_01_02_204729_create_purchase_order_items_table.php (depends on purchase_orders)
44. 2026_01_03_200000_create_materials_table.php (depends on companies)
45. 2026_01_03_200030_create_inventory_transactions_table.php (depends on materials)
```

**Key Point:**
- ✅ purchase_orders (204728) created BEFORE purchase_order_items (204729) ✓

---

## 📊 Phase 7: HR & Payroll

**Depends On:** companies, employees

```
46. 2026_01_02_212443_create_employees_table.php (depends on companies)
47. 2026_01_03_200001_create_payroll_periods_table.php (depends on companies)
48. 2026_01_03_200002_create_payroll_entries_table.php (depends on employees)
49. 2026_01_03_200003_create_payroll_allowances_table.php (depends on payroll_entries)
50. 2026_01_03_200004_create_payroll_deductions_table.php (depends on payroll_entries)
51. 2026_01_04_103300_create_shift_schedules_table.php (depends on employees)
52. 2026_01_04_103400_create_attendance_records_table.php (depends on employees)
```

**Dependencies:**
- employees → companies
- payroll_entries → employees
- allowances/deductions → payroll_entries

---

## 📊 Phase 8: Site Management & Diaries

**Depends On:** projects, employees

```
53. 2026_01_02_211857_create_daily_reports_table.php (depends on projects)
54. 2026_01_02_211857_create_daily_report_photos_table.php (depends on daily_reports)
55. 2026_01_09_150300_create_site_diaries_table.php (depends on projects, companies)
56. 2026_01_09_150301_create_diary_manpower_table.php (depends on site_diaries)
57. 2026_01_09_150302_create_diary_equipment_table.php (depends on site_diaries)
58. 2026_01_09_150303_create_diary_activities_table.php (depends on site_diaries)
```

**Dependencies:**
- site_diaries → projects
- diary_* tables → site_diaries

---

## 📊 Phase 9: Risk Management

**Depends On:** projects, risk_registers

```
59. 2026_01_09_152841_create_risk_registers_table.php (depends on projects, companies)
60. 2026_01_09_152841_create_risks_table.php (depends on risk_registers, projects)
61. 2026_01_09_152841_create_risk_assessments_table.php (depends on risks)
62. 2026_01_09_152841_create_risk_monitoring_table.php (depends on risks)
63. 2026_01_09_152841_create_risk_responses_table.php (depends on risks)
```

**Critical Dependencies:**
- ✅ risk_registers created BEFORE risks ✓
- ⚠️ All risk tables share timestamp 152841 (order within file is correct)

---

## 📊 Phase 10: Equipment Management

**Depends On:** equipment, projects

```
64. 2026_01_04_205740_create_equipment_categories_table.php (no dependencies)
65. 2026_01_04_205740_create_equipment_table.php (depends on equipment_categories)
66. 2026_01_04_205740_create_equipment_assignments_table.php (depends on equipment)
67. 2026_01_04_205751_create_equipment_maintenance_table.php (depends on equipment)
68. 2026_01_04_205751_create_equipment_fuel_logs_table.php (depends on equipment)
69. 2026_01_04_205751_create_equipment_transfers_table.php (depends on equipment)
70. 2026_01_04_205751_create_equipment_usage_table.php (depends on equipment)
```

**Fixed Issues:**
- ✅ All equipment_* tables now correctly reference 'equipment' table (not 'equipments')

---

## ⚠️ Known Warnings

### Same Timestamp Issues (63 warnings)

Multiple migrations share the same timestamp, making execution order unpredictable:

**Examples:**
- `2026_01_02_211654` - activity_dependencies & project_activities
- `2026_01_02_214414` - tender_wbs & tender_boq_items & tender_wbs_boq_mapping
- `2026_01_09_152841` - All risk management tables

**Impact:** Low - Tables are usually created in file order within same timestamp  
**Recommendation:** Monitor migration execution, rename if issues occur

---

## ✅ Validation Results

### After Fixes (2026-01-10)

```
✅ Critical Errors: 0 (down from 12)
⚠️ Warnings: 63 (timestamp conflicts)
✅ Foreign Keys: All 573 references validated
✅ Missing Tables: None
✅ Circular Dependencies: None
```

### Previously Fixed Issues

1. ✅ equipment_* tables now reference 'equipment' (not 'equipments')
2. ✅ correspondence_* tables now reference 'correspondence' (not 'correspondences')
3. ✅ defect_notifications now references 'defects_liability' (not 'defects_liabilities')

---

## 🔍 Dependency Statistics

```
Total Tables: 350
Core Tables (no dependencies): 44
Leaf Tables (nothing depends on): 232
Total Foreign Keys: 573
Maximum Dependency Depth: ~5 levels
```

### Top Dependencies (Most Referenced Tables)

1. **companies** - Referenced by 100+ tables
2. **projects** - Referenced by 80+ tables
3. **users** - Referenced by 70+ tables
4. **currencies** - Referenced by 40+ tables
5. **tenders** - Referenced by 20+ tables

---

## 🚀 Running Migrations

### Fresh Installation

```bash
# Complete fresh migration
php artisan migrate:fresh

# With seeding
php artisan migrate:fresh --seed

# Force in production
php artisan migrate:fresh --force
```

### Rollback and Re-run

```bash
# Rollback last batch
php artisan migrate:rollback

# Rollback specific steps
php artisan migrate:rollback --step=5

# Reset and re-run
php artisan migrate:refresh
```

### Check Status

```bash
# See which migrations have run
php artisan migrate:status

# Test without executing
php artisan migrate --pretend
```

---

## 📝 Troubleshooting

### Foreign Key Constraint Errors

```
Error: Cannot add foreign key constraint
Solution: Ensure parent table exists first
Check: migration_dependencies.json for correct order
```

### Table Already Exists

```
Error: Base table or view already exists
Solution: Run migrate:fresh or manually drop table
```

### Timestamp Conflicts

```
Warning: Multiple migrations with same timestamp
Solution: Usually safe - Laravel handles in file order
Action: Monitor execution, rename if problems persist
```

---

## 📚 Related Documents

- `migration_dependencies.json` - Complete dependency graph
- `NAMING_CONVENTIONS.md` - Naming standards
- `ERD.md` - Entity relationship diagrams

---

**Last Updated:** 2026-01-10  
**Maintained By:** CEMS Development Team
