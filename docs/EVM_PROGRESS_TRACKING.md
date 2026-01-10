# Progress Tracking & Earned Value Management (EVM) System

This module implements a comprehensive Progress Tracking and Earned Value Management system for the CEMS ERP platform.

## Features Implemented

### 1. Database Layer ✅
- **Projects Table**: Core project information with budget, dates, and status
- **Employees Table**: Employee management with hourly rates for cost calculation
- **Project Activities Table**: Detailed activity breakdown with planning and actual data
- **Project Progress Snapshots Table**: Historical EVM metrics tracking
- **Project Timesheets Table**: Daily work tracking with approval workflow
- **Project Baselines Table**: Baseline management for comparing planned vs actual

### 2. Business Logic Layer ✅

#### EVM Calculations
- PV (Planned Value) = Planned % × BAC
- EV (Earned Value) = Actual % × BAC
- AC (Actual Cost) from timesheets
- SV (Schedule Variance) = EV - PV
- CV (Cost Variance) = EV - AC
- SPI (Schedule Performance Index) = EV / PV
- CPI (Cost Performance Index) = EV / AC
- EAC (Estimate at Completion) = BAC / CPI
- ETC (Estimate to Complete) = EAC - AC
- VAC (Variance at Completion) = BAC - EAC
- TCPI (To Complete Performance Index)

### 3. User Interface ✅
- **Dashboard**: KPIs, Charts (S-Curve, Performance, Variance), Alerts
- **Progress Update**: Form with real-time EVM calculations
- **Timesheets**: Daily entry and approval workflow
- **Baseline**: Create and compare baselines
- **Variance Analysis**: Top delayed and over-budget activities
- **Forecasting**: Three scenarios with custom calculator

### 4. Charts & Visualizations ✅
- S-Curve (PV, EV, AC)
- Performance Indexes (SPI, CPI)
- Variance trends (SV, CV)
- Color-coded KPIs (Green/Yellow/Red)

## Installation

```bash
php artisan migrate
```

## Access
Navigate to: `/progress/dashboard`

## Color Coding
- **Green**: ≥ 0.95 (Good)
- **Yellow**: 0.85 - 0.95 (Warning)  
- **Red**: < 0.85 (Critical)
