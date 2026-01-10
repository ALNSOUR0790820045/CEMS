# Payroll Management Module

## Overview
Complete payroll system for managing employee salaries, allowances, deductions, loans, and WPS file generation for Saudi Arabia.

## Features

### 1. Payroll Period Management
- Create and manage payroll periods (monthly, weekly, daily)
- Calculate payroll for all employees in a period
- Approve payroll before payment
- Track payment status (open, calculated, approved, paid, closed)

### 2. Payroll Entry Management
- Individual employee salary entries per period
- Basic salary calculation
- Allowances (housing, transport, food, mobile, other)
- Deductions (tax, social insurance, loan, advance, penalty, other)
- Overtime hours and amount tracking
- Days worked and absent tracking
- Multiple payment methods (bank transfer, cash, check)

### 3. Employee Loan Management
- Create and track employee loans
- Automatic installment deduction from payroll
- Track paid and remaining installments
- Loan status management (active, completed, cancelled)

### 4. Bank Account Management
- Store employee bank account details
- Support for IBAN and SWIFT codes
- Primary account designation

### 5. WPS Integration
- Generate WPS files in Saudi Arabia format
- Bank transfer list generation
- Support for salary transfer automation

### 6. Payslip Generation
- PDF payslip generation with Arabic/English support
- Detailed breakdown of salary components
- Company branding

## Database Schema

### Tables Created
1. `bank_accounts` - Employee banking information
2. `payroll_periods` - Salary period management
3. `payroll_entries` - Individual employee salary records
4. `payroll_allowances` - Allowance details per entry
5. `payroll_deductions` - Deduction details per entry
6. `employee_loans` - Loan tracking

## API Endpoints

### Payroll Periods
```
GET    /api/payroll-periods              - List all payroll periods
POST   /api/payroll-periods              - Create new payroll period
GET    /api/payroll-periods/{id}         - Get payroll period details
PUT    /api/payroll-periods/{id}         - Update payroll period
DELETE /api/payroll-periods/{id}         - Delete payroll period
POST   /api/payroll-periods/{id}/calculate - Calculate payroll
POST   /api/payroll-periods/{id}/approve   - Approve payroll
```

### Payroll Entries
```
GET    /api/payroll-entries              - List all payroll entries
POST   /api/payroll-entries              - Create new payroll entry
GET    /api/payroll-entries/{id}         - Get payroll entry details
PUT    /api/payroll-entries/{id}         - Update payroll entry
GET    /api/payroll-entries/{id}/payslip - Download payslip PDF
```

### Employee Loans
```
GET    /api/employee-loans               - List all employee loans
POST   /api/employee-loans               - Create new employee loan
GET    /api/employee-loans/{id}          - Get loan details
PUT    /api/employee-loans/{id}          - Update loan
DELETE /api/employee-loans/{id}          - Delete loan
POST   /api/employee-loans/{id}/cancel   - Cancel loan
```

### WPS Export
```
POST   /api/payroll/wps-export           - Generate WPS file
POST   /api/payroll/bank-transfer-list   - Generate bank transfer list
```

## Usage Examples

### 1. Create Payroll Period
```bash
POST /api/payroll-periods
{
  "period_name": "January 2026",
  "period_type": "monthly",
  "start_date": "2026-01-01",
  "end_date": "2026-01-31",
  "payment_date": "2026-02-01"
}
```

### 2. Create Payroll Entry
```bash
POST /api/payroll-entries
{
  "payroll_period_id": 1,
  "employee_id": 5,
  "basic_salary": 5000.00,
  "days_worked": 30,
  "days_absent": 0,
  "overtime_hours": 5.0,
  "overtime_amount": 250.00,
  "payment_method": "bank_transfer",
  "bank_account_id": 1,
  "allowances": [
    {
      "allowance_type": "housing",
      "allowance_name": "Housing Allowance",
      "amount": 1000.00,
      "is_taxable": true
    }
  ],
  "deductions": [
    {
      "deduction_type": "tax",
      "deduction_name": "Income Tax",
      "amount": 500.00
    }
  ]
}
```

### 3. Create Employee Loan
```bash
POST /api/employee-loans
{
  "employee_id": 5,
  "loan_date": "2026-01-01",
  "loan_amount": 10000.00,
  "installment_amount": 1000.00,
  "total_installments": 10,
  "notes": "Emergency loan"
}
```

### 4. Calculate Payroll
```bash
POST /api/payroll-periods/1/calculate
```

### 5. Approve Payroll
```bash
POST /api/payroll-periods/1/approve
```

### 6. Generate WPS File
```bash
POST /api/payroll/wps-export
{
  "payroll_period_id": 1
}
```

## Business Logic

### Payroll Calculation Flow
1. Create payroll period
2. Add payroll entries for all employees
3. System automatically:
   - Calculates total allowances
   - Calculates total deductions
   - Adds loan installments as deductions
   - Calculates gross salary (basic + allowances)
   - Calculates net salary (gross - deductions)
4. Calculate payroll period (updates status to 'calculated')
5. Approve payroll period (updates status to 'approved')

### Automatic Loan Deduction
- When calculating a payroll entry, the system automatically:
  1. Finds all active loans for the employee
  2. Creates a deduction for each loan installment
  3. Increments the loan's paid installments
  4. Marks loan as 'completed' when all installments are paid

### Authorization
- All endpoints require authentication via Sanctum
- Users can only access data from their own company
- Policies enforce company-level data isolation

## WPS File Format (Saudi Arabia)

The WPS export generates a text file with two record types:

### Header Record (SCR)
```
SCR + Commercial Registration (10 chars) + Company Name (140 chars) + 
Payment Date (8 digits) + Record Count (7 digits) + Total Amount (15 digits) + 
Payment Type (2 digits) + Reserved (8 chars)
```

### Employee Record (EDR)
```
EDR + Employee ID (14 chars) + Account Number (24 chars) + 
Routing Code (2 chars) + Bank Name (23 chars) + Salary Amount (15 digits) + 
Payment Date (8 digits) + Days Absent (3 chars) + Extra Days (3 chars) + 
Reference (24 chars) + Reserved (6 chars)
```

## Models

### PayrollPeriod
- Manages salary periods
- Methods: `calculate()`, `approve()`, `recalculateTotals()`
- Relationships: entries, company, calculatedBy, approvedBy

### PayrollEntry
- Individual employee salary record
- Methods: `calculateTotals()`, `addLoanDeductions()`
- Accessors: `gross_salary`, `net_salary`
- Relationships: payrollPeriod, employee, allowances, deductions, bankAccount

### EmployeeLoan
- Tracks employee loans and installments
- Accessors: `remaining_balance`, `remaining_installments`
- Relationships: employee, company

### BankAccount
- Stores employee banking information
- Relationships: user, company

## Testing

### Running Tests
```bash
php artisan test --filter Payroll
php artisan test --filter EmployeeLoan
```

### Test Coverage
- PayrollPeriodTest: 13 tests
- PayrollEntryTest: 8 tests
- EmployeeLoanTest: 11 tests

All tests cover:
- CRUD operations
- Authorization
- Business logic validation
- Multi-company data isolation

## Security Features

1. **Authentication**: All endpoints require Sanctum authentication
2. **Authorization**: Company-level policies prevent cross-company access
3. **Input Validation**: All inputs validated using Laravel validation
4. **Filename Sanitization**: WPS export filenames are sanitized
5. **Transaction Safety**: Loan deductions wrapped in database transactions
6. **SQL Injection Prevention**: Using Eloquent ORM and parameterized queries

## Notes

- All currency amounts are stored as decimal(10,2)
- Dates follow ISO 8601 format (YYYY-MM-DD)
- The system supports Arabic and English languages
- Payslips are generated in bilingual format (Arabic/English)
