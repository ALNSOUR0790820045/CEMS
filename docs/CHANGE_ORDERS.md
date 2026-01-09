# Change Orders Management Module

## نظام إدارة أوامر التغيير

Comprehensive Change Order Management for construction projects with automatic calculations and a 4-level signature approval workflow.

## Key Features

### 1. Auto-Calculations
- Fee Calculation (default 0.3%)
- Stamp Duty (50-10,000 SAR)
- VAT (15%)
- Updated Contract Value
- New Completion Date
- Item Amounts

### 2. 4-Level Signature Workflow
Draft → PM → Technical → Consultant → Client → Approved

### 3. Complete CRUD Operations
- Create, Read, Update, Delete
- Approval workflow
- PDF export
- Reporting and analytics

### 4. User Interface
- Modern Apple-inspired design
- RTL support
- Real-time calculations
- Visual signature timeline
- Filterable lists
- Statistics dashboard

## Files Created

### Migrations
- 2026_01_02_140000_create_projects_table.php
- 2026_01_02_140100_create_tenders_table.php
- 2026_01_02_140200_create_contracts_table.php
- 2026_01_02_140300_create_project_wbs_table.php
- 2026_01_02_140400_create_change_orders_table.php
- 2026_01_02_140500_create_change_order_items_table.php

### Models
- Project.php
- Tender.php
- Contract.php
- ProjectWbs.php
- ChangeOrder.php (with auto-calculations)
- ChangeOrderItem.php (with auto-calculations)

### Controllers
- ChangeOrderController.php (full CRUD + workflow)

### Views
- change-orders/index.blade.php
- change-orders/create.blade.php
- change-orders/edit.blade.php
- change-orders/show.blade.php
- change-orders/approve.blade.php
- change-orders/report.blade.php
- change-orders/pdf.blade.php

### Routes
- Resource routes for CRUD
- Workflow routes (submit, approve)
- Report and export routes

## Quick Start

1. Run migrations: `php artisan migrate`
2. Link storage: `php artisan storage:link`
3. Navigate to المالية > العقود > أوامر التغيير
4. Start creating change orders!

## Technical Requirements

- Laravel 12.x
- PostgreSQL
- barryvdh/laravel-dompdf
- spatie/laravel-permission

For detailed documentation, see the full feature specification in the problem statement.
