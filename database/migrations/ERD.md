# Entity Relationship Diagram (ERD)

**Version:** 1.0  
**Generated:** 2026-01-10  
**Total Tables:** 350

---

## 📋 Overview

This document provides Entity Relationship Diagrams (ERDs) for the CEMS (Construction Enterprise Management System) database. Due to the large number of tables (350), the ERD is organized into logical modules.

---

## 🏗️ Core Architecture

```mermaid
erDiagram
    COMPANIES ||--o{ BRANCHES : has
    COMPANIES ||--o{ PROJECTS : owns
    COMPANIES ||--o{ USERS : employs
    COMPANIES ||--o{ TENDERS : creates
    COMPANIES ||--o{ CLIENTS : manages
    COMPANIES ||--o{ VENDORS : works_with
    
    USERS }o--|| BRANCHES : "works at"
    USERS }o--|| DEPARTMENTS : "belongs to"
    
    BRANCHES ||--o{ DEPARTMENTS : contains
    BRANCHES }o--|| CURRENCIES : "uses currency"
    
    COUNTRIES ||--o{ CITIES : contains
```

---

## 🎯 Module 1: Project Management

```mermaid
erDiagram
    COMPANIES ||--o{ PROJECTS : owns
    PROJECTS ||--o{ PROJECT_WBS : "has structure"
    PROJECTS ||--o{ PROJECT_ACTIVITIES : "has activities"
    PROJECTS ||--o{ PROJECT_MILESTONES : "has milestones"
    PROJECTS ||--o{ PROJECT_BASELINES : "has baselines"
    PROJECTS }o--|| USERS : "managed by"
    
    PROJECT_ACTIVITIES ||--o{ ACTIVITY_DEPENDENCIES : "has dependencies"
    PROJECT_ACTIVITIES }o--o{ PROJECT_ACTIVITIES : "depends on"
    
    PROJECT_WBS ||--o{ BOQ_ITEMS : "links to"
    PROJECTS ||--o{ BOQ_HEADERS : has
    BOQ_HEADERS ||--o{ BOQ_SECTIONS : contains
    BOQ_SECTIONS ||--o{ BOQ_ITEMS : contains
    BOQ_ITEMS }o--|| UNITS : measured_in
    
    PROJECTS ||--o{ CONTRACTS : generates
    PROJECTS ||--o{ DAILY_REPORTS : tracks
    PROJECTS ||--o{ SITE_DIARIES : documents
```

---

## 📋 Module 2: Tender Management

```mermaid
erDiagram
    COMPANIES ||--o{ TENDERS : creates
    TENDERS }o--|| PROJECTS : "related to"
    
    TENDERS ||--o{ TENDER_SITE_VISITS : requires
    TENDERS ||--o{ TENDER_CLARIFICATIONS : answers
    TENDERS ||--o{ TENDER_COMPETITORS : tracks
    TENDERS ||--o{ TENDER_COMMITTEE_DECISIONS : decides
    TENDERS ||--o{ TENDER_DOCUMENTS : contains
    TENDERS ||--o{ TENDER_QUESTIONS : receives
    
    TENDERS ||--o{ TENDER_WBS : structures
    TENDER_WBS ||--o{ TENDER_ACTIVITIES : schedules
    TENDER_ACTIVITIES ||--o{ TENDER_ACTIVITY_DEPENDENCIES : links
    
    TENDERS ||--o{ TENDER_BOQ_ITEMS : prices
    TENDER_WBS ||--o{ TENDER_WBS_BOQ_MAPPING : maps
    TENDER_BOQ_ITEMS ||--o{ TENDER_WBS_BOQ_MAPPING : maps
    
    TENDERS ||--o{ TENDER_RESOURCES : plans
    TENDER_RESOURCES ||--o{ TENDER_RESOURCE_ASSIGNMENTS : assigns
    
    TENDERS ||--o{ TENDER_RISKS : identifies
    TENDER_RISKS ||--o{ TENDER_RISK_EVENTS : tracks
    TENDER_RISKS ||--o{ TENDER_CONTINGENCY_RESERVES : reserves
    
    TENDERS ||--o{ CONTRACTS : "awarded as"
```

---

## 📊 Module 3: Contract Management

```mermaid
erDiagram
    PROJECTS ||--o{ CONTRACTS : executes
    TENDERS }o--|| CONTRACTS : "awarded as"
    CLIENTS }o--|| CONTRACTS : signs
    CURRENCIES }o--|| CONTRACTS : denominated_in
    
    CONTRACTS ||--o{ CONTRACT_AMENDMENTS : modifies
    CONTRACTS ||--o{ CONTRACT_CHANGE_ORDERS : changes
    CONTRACTS ||--o{ CONTRACT_MILESTONES : tracks
    CONTRACTS ||--o{ CONTRACT_CLAUSES : defines
    
    CONTRACTS ||--o{ CHANGE_ORDERS : generates
    CHANGE_ORDERS ||--o{ CHANGE_ORDER_ITEMS : contains
    
    CONTRACTS ||--o{ VARIATION_ORDERS : issues
    CONTRACTS ||--o{ EOT_CLAIMS : extends
    CONTRACTS ||--o{ CLAIMS : disputes
```

---

## 💰 Module 4: Financial Management

### Accounts Receivable (AR)

```mermaid
erDiagram
    COMPANIES ||--o{ CLIENTS : manages
    CLIENTS ||--o{ A_R_INVOICES : billed_by
    CLIENTS ||--o{ A_R_RECEIPTS : pays
    
    A_R_INVOICES }o--|| CURRENCIES : denominated_in
    A_R_INVOICES ||--o{ A_R_INVOICE_ITEMS : contains
    
    A_R_RECEIPTS }o--|| CURRENCIES : denominated_in
    A_R_RECEIPTS }o--|| BANK_ACCOUNTS : "deposited in"
    A_R_RECEIPTS ||--o{ A_R_RECEIPT_ALLOCATIONS : allocates
    A_R_INVOICE_ITEMS }o--|| A_R_RECEIPT_ALLOCATIONS : "paid by"
```

### Accounts Payable (AP)

```mermaid
erDiagram
    COMPANIES ||--o{ VENDORS : works_with
    VENDORS ||--o{ AP_INVOICES : bills
    VENDORS ||--o{ AP_PAYMENTS : "receives payment"
    
    AP_INVOICES }o--|| CURRENCIES : denominated_in
    AP_INVOICES ||--o{ AP_INVOICE_ITEMS : contains
    
    AP_PAYMENTS }o--|| CURRENCIES : denominated_in
    AP_PAYMENTS }o--|| BANK_ACCOUNTS : "paid from"
    AP_PAYMENTS ||--o{ AP_PAYMENT_ALLOCATIONS : allocates
    AP_INVOICE_ITEMS }o--|| AP_PAYMENT_ALLOCATIONS : "paid by"
```

### General Ledger (GL)

```mermaid
erDiagram
    COMPANIES ||--o{ GL_ACCOUNTS : maintains
    COMPANIES ||--o{ GL_FISCAL_YEARS : operates_in
    GL_FISCAL_YEARS ||--o{ GL_PERIODS : divides_into
    
    COMPANIES ||--o{ GL_JOURNAL_ENTRIES : records
    GL_JOURNAL_ENTRIES ||--o{ GL_JOURNAL_ENTRY_LINES : contains
    GL_JOURNAL_ENTRY_LINES }o--|| GL_ACCOUNTS : "posts to"
    GL_JOURNAL_ENTRY_LINES }o--|| COST_CENTERS : "allocates to"
```

---

## 🔨 Module 5: Procurement & Inventory

```mermaid
erDiagram
    COMPANIES ||--o{ SUPPLIERS : sources_from
    SUPPLIERS }o--|| CURRENCIES : "transacts in"
    
    PROJECTS ||--o{ PURCHASE_REQUISITIONS : requests
    PURCHASE_REQUISITIONS ||--o{ PR_ITEMS : contains
    PR_ITEMS ||--o{ PR_QUOTES : receives
    
    SUPPLIERS ||--o{ PURCHASE_ORDERS : receives
    PURCHASE_ORDERS ||--o{ PURCHASE_ORDER_ITEMS : contains
    PURCHASE_ORDERS ||--o{ PO_RECEIPTS : fulfills
    PO_RECEIPTS ||--o{ PO_RECEIPT_ITEMS : contains
    
    PURCHASE_ORDERS ||--o{ GRNS : delivers
    GRNS ||--o{ GRN_ITEMS : contains
    
    COMPANIES ||--o{ WAREHOUSES : operates
    WAREHOUSES ||--o{ INVENTORY_BALANCES : stocks
    WAREHOUSES ||--o{ STOCK_MOVEMENTS : tracks
    WAREHOUSES ||--o{ STOCK_TRANSFERS : "transfers between"
    
    COMPANIES ||--o{ MATERIALS : uses
    MATERIALS }o--|| MATERIAL_CATEGORIES : categorized_by
    MATERIALS ||--o{ INVENTORY_TRANSACTIONS : transacts
```

---

## 👷 Module 6: HR & Payroll

```mermaid
erDiagram
    COMPANIES ||--o{ EMPLOYEES : employs
    EMPLOYEES }o--|| DEPARTMENTS : "works in"
    EMPLOYEES }o--|| BRANCHES : "based at"
    
    EMPLOYEES ||--o{ ATTENDANCE_RECORDS : tracks
    EMPLOYEES ||--o{ SHIFT_SCHEDULES : schedules
    EMPLOYEES ||--o{ LEAVE_REQUESTS : requests
    EMPLOYEES ||--o{ PROJECT_TIMESHEETS : logs
    
    COMPANIES ||--o{ PAYROLL_PERIODS : processes
    EMPLOYEES ||--o{ PAYROLL_ENTRIES : receives
    PAYROLL_ENTRIES ||--o{ PAYROLL_ALLOWANCES : includes
    PAYROLL_ENTRIES ||--o{ PAYROLL_DEDUCTIONS : deducts
    
    EMPLOYEES ||--o{ EMPLOYEE_LOANS : borrows
```

---

## ⚠️ Module 7: Risk Management

```mermaid
erDiagram
    PROJECTS ||--o{ RISK_REGISTERS : maintains
    COMPANIES ||--o{ RISK_REGISTERS : owns
    
    RISK_REGISTERS ||--o{ RISKS : contains
    RISKS }o--|| USERS : "identified by"
    RISKS }o--|| USERS : "owned by"
    RISKS }o--|| PROJECTS : "affects"
    
    RISKS ||--o{ RISK_ASSESSMENTS : evaluated_by
    RISKS ||--o{ RISK_RESPONSES : mitigated_by
    RISKS ||--o{ RISK_MONITORING : monitored_by
    RISKS ||--o{ RISK_INCIDENTS : "manifests as"
    
    COMPANIES ||--o{ RISK_CATEGORIES : defines
    COMPANIES ||--o{ RISK_MATRIX_SETTINGS : configures
```

---

## 🏗️ Module 8: Subcontractor Management

```mermaid
erDiagram
    COMPANIES ||--o{ SUBCONTRACTORS : engages
    SUBCONTRACTORS ||--o{ SUBCONTRACTOR_CONTACTS : employs
    
    SUBCONTRACTORS ||--o{ SUBCONTRACTOR_AGREEMENTS : signs
    SUBCONTRACTOR_AGREEMENTS }o--|| PROJECTS : "for"
    SUBCONTRACTOR_AGREEMENTS }o--|| CURRENCIES : denominated_in
    
    SUBCONTRACTOR_AGREEMENTS ||--o{ SUBCONTRACTOR_WORK_ORDERS : issues
    SUBCONTRACTOR_AGREEMENTS ||--o{ SUBCONTRACTOR_IPCS : bills
    SUBCONTRACTOR_IPCS ||--o{ SUBCONTRACTOR_IPC_ITEMS : contains
    
    SUBCONTRACTORS ||--o{ SUBCONTRACTOR_EVALUATIONS : rates
```

---

## 📦 Module 9: Equipment Management

```mermaid
erDiagram
    COMPANIES ||--o{ EQUIPMENT_CATEGORIES : defines
    EQUIPMENT_CATEGORIES ||--o{ EQUIPMENT : categorizes
    
    EQUIPMENT ||--o{ EQUIPMENT_ASSIGNMENTS : "assigned via"
    EQUIPMENT_ASSIGNMENTS }o--|| PROJECTS : "assigned to"
    EQUIPMENT_ASSIGNMENTS }o--|| EMPLOYEES : "operated by"
    
    EQUIPMENT ||--o{ EQUIPMENT_USAGE : tracks
    EQUIPMENT ||--o{ EQUIPMENT_MAINTENANCE : maintains
    EQUIPMENT ||--o{ EQUIPMENT_FUEL_LOGS : fuels
    EQUIPMENT ||--o{ EQUIPMENT_TRANSFERS : moves
```

---

## 📝 Module 10: Site Documentation

```mermaid
erDiagram
    PROJECTS ||--o{ SITE_DIARIES : documents
    COMPANIES ||--o{ SITE_DIARIES : owns
    
    SITE_DIARIES ||--o{ DIARY_MANPOWER : logs
    SITE_DIARIES ||--o{ DIARY_EQUIPMENT : uses
    SITE_DIARIES ||--o{ DIARY_ACTIVITIES : performs
    SITE_DIARIES ||--o{ DIARY_MATERIALS : consumes
    SITE_DIARIES ||--o{ DIARY_VISITORS : receives
    SITE_DIARIES ||--o{ DIARY_INCIDENTS : reports
    SITE_DIARIES ||--o{ DIARY_INSTRUCTIONS : records
    SITE_DIARIES ||--o{ DIARY_PHOTOS : captures
    
    PROJECTS ||--o{ DAILY_REPORTS : generates
    DAILY_REPORTS ||--o{ DAILY_REPORT_PHOTOS : includes
```

---

## 📸 Module 11: Photo Management

```mermaid
erDiagram
    COMPANIES ||--o{ PHOTO_ALBUMS : organizes
    PHOTO_ALBUMS ||--o{ PHOTOS : contains
    
    PHOTOS }o--|| PROJECTS : "documents"
    PHOTOS ||--o{ PHOTO_ANNOTATIONS : annotates
    PHOTOS ||--o{ PHOTO_LOCATIONS : geo_tags
    PHOTOS ||--o{ PHOTO_TAGS : tags
    
    PHOTOS }o--o{ PHOTOS : "compared with"
    PHOTO_COMPARISONS }o--|| PHOTOS : before
    PHOTO_COMPARISONS }o--|| PHOTOS : after
    
    COMPANIES ||--o{ PHOTO_REPORTS : generates
    PHOTO_REPORTS ||--o{ PHOTO_REPORT_ITEMS : includes
```

---

## 🔍 Module 12: Inspection & Quality

```mermaid
erDiagram
    COMPANIES ||--o{ INSPECTION_TYPES : defines
    INSPECTION_TYPES ||--o{ INSPECTION_TEMPLATES : uses
    INSPECTION_TEMPLATES ||--o{ TEMPLATE_ITEMS : contains
    
    PROJECTS ||--o{ INSPECTION_REQUESTS : requests
    INSPECTION_REQUESTS ||--o{ INSPECTIONS : conducts
    INSPECTIONS ||--o{ INSPECTION_ITEMS : checks
    INSPECTIONS ||--o{ INSPECTION_ACTIONS : requires
    INSPECTIONS ||--o{ INSPECTION_PHOTOS : documents
    
    PROJECTS ||--o{ PUNCH_LISTS : maintains
    PUNCH_LISTS ||--o{ PUNCH_ITEMS : tracks
    PUNCH_ITEMS ||--o{ PUNCH_ITEM_COMMENTS : discusses
    PUNCH_ITEMS ||--o{ PUNCH_ITEM_HISTORY : logs
    
    COMPANIES ||--o{ PUNCH_CATEGORIES : defines
    COMPANIES ||--o{ PUNCH_TEMPLATES : creates
```

---

## 💼 Module 13: Correspondence

```mermaid
erDiagram
    CORRESPONDENCE }o--o{ CORRESPONDENCE : "replies to"
    CORRESPONDENCE }o--o{ CORRESPONDENCE : "parent of"
    
    CORRESPONDENCE }o--|| PROJECTS : "relates to"
    CORRESPONDENCE }o--|| CONTRACTS : "relates to"
    CORRESPONDENCE }o--|| TENDERS : "relates to"
    CORRESPONDENCE }o--|| CLIENTS : "from/to"
    CORRESPONDENCE }o--|| VENDORS : "from/to"
    
    CORRESPONDENCE ||--o{ CORRESPONDENCE_ATTACHMENTS : includes
    CORRESPONDENCE ||--o{ CORRESPONDENCE_DISTRIBUTION : "distributed to"
    CORRESPONDENCE_DISTRIBUTION }o--|| USERS : "sent to"
    
    CORRESPONDENCE ||--o{ CORRESPONDENCE_ACTIONS : tracks
    CORRESPONDENCE_ACTIONS }o--|| USERS : "performed by"
```

---

## 💵 Module 14: Progress Billing & Retention

```mermaid
erDiagram
    PROJECTS ||--o{ PROGRESS_BILLS : issues
    CONTRACTS ||--o{ PROGRESS_BILLS : "billed under"
    PROGRESS_BILLS }o--|| CURRENCIES : denominated_in
    
    CONTRACTS ||--o{ RETENTIONS : holds
    RETENTIONS ||--o{ RETENTION_RELEASES : releases
    RETENTIONS ||--o{ RETENTION_GUARANTEES : secures
    
    CONTRACTS ||--o{ ADVANCE_PAYMENTS : receives
    ADVANCE_PAYMENTS }o--|| CURRENCIES : denominated_in
    
    CONTRACTS ||--o{ DEFECTS_LIABILITY : manages
    DEFECTS_LIABILITY ||--o{ DEFECT_NOTIFICATIONS : issues
```

---

## 💰 Module 15: Project Cost Control

```mermaid
erDiagram
    PROJECTS ||--o{ PROJECT_BUDGETS : plans
    PROJECT_BUDGETS }o--|| CURRENCIES : budgeted_in
    
    COMPANIES ||--o{ COST_CODES : defines
    COMPANIES ||--o{ COST_CATEGORIES : categorizes
    
    PROJECTS ||--o{ ACTUAL_COSTS : incurs
    ACTUAL_COSTS }o--|| COST_CODES : "coded as"
    ACTUAL_COSTS }o--|| CURRENCIES : spent_in
    
    PROJECTS ||--o{ COMMITTED_COSTS : commits
    COMMITTED_COSTS }o--|| CURRENCIES : committed_in
    
    PROJECTS ||--o{ COST_FORECASTS : forecasts
    PROJECTS ||--o{ VARIANCE_ANALYSIS : analyzes
    PROJECTS ||--o{ COST_REPORTS : reports
```

---

## 🏦 Module 16: Banking & Cash Management

```mermaid
erDiagram
    COMPANIES ||--o{ BANKS : "banks with"
    BANKS ||--o{ BANK_ACCOUNTS : provides
    BANK_ACCOUNTS }o--|| BRANCHES : "assigned to"
    BANK_ACCOUNTS }o--|| CURRENCIES : denominated_in
    
    BANK_ACCOUNTS ||--o{ BANK_STATEMENTS : issues
    BANK_STATEMENTS ||--o{ BANK_STATEMENT_LINES : contains
    
    BANK_ACCOUNTS ||--o{ BANK_RECONCILIATIONS : reconciles
    BANK_RECONCILIATIONS ||--o{ RECONCILIATION_ITEMS : matches
    
    BANK_ACCOUNTS ||--o{ CHECKS : issues
    BANK_ACCOUNTS ||--o{ PROMISSORY_NOTES : holds
    
    COMPANIES ||--o{ CASH_ACCOUNTS : maintains
    CASH_ACCOUNTS ||--o{ CASH_TRANSACTIONS : records
    CASH_ACCOUNTS ||--o{ CASH_TRANSFERS : "transfers between"
    CASH_ACCOUNTS ||--o{ DAILY_CASH_POSITIONS : tracks
```

---

## 🏆 Module 17: Fixed Assets

```mermaid
erDiagram
    COMPANIES ||--o{ ASSET_CATEGORIES : defines
    ASSET_CATEGORIES ||--o{ FIXED_ASSETS : categorizes
    
    FIXED_ASSETS }o--|| CURRENCIES : valued_in
    FIXED_ASSETS ||--o{ ASSET_DEPRECIATIONS : depreciates
    FIXED_ASSETS ||--o{ ASSET_MAINTENANCES : maintains
    FIXED_ASSETS ||--o{ ASSET_TRANSFERS : relocates
    FIXED_ASSETS ||--o{ ASSET_DISPOSALS : disposes
    FIXED_ASSETS ||--o{ ASSET_REVALUATIONS : revalues
```

---

## 📊 Module 18: Guarantees

```mermaid
erDiagram
    PROJECTS ||--o{ GUARANTEES : requires
    GUARANTEES }o--|| CURRENCIES : valued_in
    GUARANTEES }o--|| BANKS : issued_by
    
    GUARANTEES ||--o{ GUARANTEE_RENEWALS : renews
    GUARANTEES ||--o{ GUARANTEE_RELEASES : releases
    GUARANTEES ||--o{ GUARANTEE_CLAIMS : claims
```

---

## 🔔 Module 19: Notifications

```mermaid
erDiagram
    COMPANIES ||--o{ NOTIFICATIONS : generates
    USERS }o--|| NOTIFICATIONS : receives
    
    COMPANIES ||--o{ ALERT_RULES : configures
    COMPANIES ||--o{ SCHEDULED_NOTIFICATIONS : schedules
```

---

## 📈 Module 20: Exchange Rates & Multi-Currency

```mermaid
erDiagram
    CURRENCIES ||--o{ EXCHANGE_RATES : "converted via"
    EXCHANGE_RATES }o--|| CURRENCIES : "from currency"
    EXCHANGE_RATES }o--|| CURRENCIES : "to currency"
    
    BRANCHES }o--|| CURRENCIES : "primary currency"
    CONTRACTS }o--|| CURRENCIES : "contract currency"
    INVOICES }o--|| CURRENCIES : "billed in"
```

---

## 📝 Key Relationships Summary

### Most Connected Tables

1. **companies** → 100+ child tables
2. **projects** → 80+ child tables  
3. **users** → 70+ child tables
4. **currencies** → 40+ child tables
5. **contracts** → 30+ child tables
6. **tenders** → 20+ child tables

### Common Patterns

```
1. Tenant Pattern: Most tables have company_id
2. Audit Pattern: Most tables have created_at, updated_at
3. Soft Delete: Many tables have deleted_at
4. User Tracking: created_by_id, approved_by_id
5. Status Enums: status field in most tables
```

---

## 🔧 Foreign Key Conventions

```sql
-- Standard Pattern
{singular_table}_id → BIGINT UNSIGNED
REFERENCES {plural_table}(id)
ON DELETE CASCADE/NULL/RESTRICT

-- Examples
company_id → companies(id) ON DELETE CASCADE
project_id → projects(id) ON DELETE CASCADE  
user_id → users(id) ON DELETE RESTRICT
currency_id → currencies(id) ON DELETE RESTRICT
```

---

## 📚 Related Documents

- `migration_dependencies.json` - Machine-readable dependency graph
- `NAMING_CONVENTIONS.md` - Naming standards
- `EXECUTION_PLAN.md` - Migration execution order

---

**Generated:** 2026-01-10  
**Total Tables:** 350  
**Total Foreign Keys:** 573  
**Maintained By:** CEMS Development Team
