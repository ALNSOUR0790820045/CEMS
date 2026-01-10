# Daily Reports Module - CEMS ERP

## Overview
Complete daily reporting system with GPS-enabled photo documentation and multi-level signature workflow for construction projects.

## Features

### 1. Daily Report Management
- **Comprehensive Report Creation**: Multi-tab form covering all aspects of daily operations
- **Report Numbering**: Auto-generated sequential numbering (DR-YYYY-###)
- **Date Tracking**: Automatic date management with unique constraint per project/date
- **Status Workflow**: Draft → Submitted → Approved/Rejected

### 2. Weather Documentation
- Weather condition tracking (Clear, Cloudy, Rainy, Stormy)
- Temperature and humidity recording
- Site conditions documentation
- **EOT Support**: Weather log provides evidence for Extension of Time claims

### 3. Labor Management
- Total worker count tracking
- Breakdown by category (Engineers, Technicians, Workers)
- Attendance notes
- Work hours logging (start time, end time, total hours)

### 4. GPS-Enabled Photo Documentation
- **Up to 24 photos per day**
- **GPS Metadata**: Automatic latitude/longitude capture
- **Timestamp**: Exact capture time
- **Blockchain Hash**: SHA-256 verification for authenticity
- **Categorization**: Progress, Problem, Safety, Quality, Material, Equipment, General

### 5. Multi-Level Signature Workflow
```
1. Site Engineer → Prepares & Signs
2. Project Manager → Reviews & Signs
3. Consultant → Approves & Signs
4. Client (Optional) → Final Signature
```

## Database Schema

### daily_reports
- Project linkage and metadata
- Weather data
- Work and labor data
- Problems and delays
- Four-level signature system

### daily_report_photos
- Photo storage with GPS
- Blockchain hash verification
- Category and activity linking

## Views

1. **Index**: Filterable list with search
2. **Create/Edit**: 8-tab comprehensive form
3. **Show**: Full report with photo gallery
4. **Sign**: Signature workflow page
5. **Photos**: Gallery with GPS and filters
6. **Weather Log**: Weather tracking for EOT claims

## Golden Rule
❌ **No payment without signed daily report!**

## Installation

```bash
php artisan migrate
php artisan db:seed --class=DailyReportsSeeder
php artisan storage:link
```

## Usage

1. Create daily report with all sections filled
2. Upload photos (GPS auto-captured)
3. Submit for approval
4. Sign at each level (Site Engineer → PM → Consultant → Client)
5. Use approved reports for financial disbursements
