<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>كشف راتب - Payslip</title>
    <style>
        * {
            font-family: 'DejaVu Sans', sans-serif;
        }
        body {
            margin: 0;
            padding: 20px;
            font-size: 12px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .company-name {
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .document-title {
            font-size: 16px;
            margin-top: 10px;
        }
        .info-section {
            margin-bottom: 20px;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
        }
        .label {
            font-weight: bold;
            width: 40%;
        }
        .value {
            width: 60%;
            text-align: left;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table th, table td {
            border: 1px solid #000;
            padding: 8px;
            text-align: right;
        }
        table th {
            background-color: #f0f0f0;
            font-weight: bold;
        }
        .total-row {
            font-weight: bold;
            background-color: #f9f9f9;
        }
        .net-salary {
            font-size: 16px;
            font-weight: bold;
            text-align: center;
            margin-top: 20px;
            padding: 15px;
            background-color: #e8f5e9;
            border: 2px solid #4caf50;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="company-name">{{ $entry->company->name }}</div>
        <div>{{ $entry->company->address ?? '' }}</div>
        <div>{{ $entry->company->phone ?? '' }}</div>
        <div class="document-title">كشف راتب - Payslip</div>
    </div>

    <div class="info-section">
        <div class="info-row">
            <span class="label">اسم الموظف / Employee Name:</span>
            <span class="value">{{ $entry->employee->name }}</span>
        </div>
        <div class="info-row">
            <span class="label">رقم الموظف / Employee ID:</span>
            <span class="value">{{ $entry->employee->employee_id ?? $entry->employee->id }}</span>
        </div>
        <div class="info-row">
            <span class="label">المسمى الوظيفي / Job Title:</span>
            <span class="value">{{ $entry->employee->job_title ?? 'N/A' }}</span>
        </div>
        <div class="info-row">
            <span class="label">فترة الراتب / Pay Period:</span>
            <span class="value">{{ $entry->payrollPeriod->period_name }}</span>
        </div>
        <div class="info-row">
            <span class="label">تاريخ الدفع / Payment Date:</span>
            <span class="value">{{ $entry->payrollPeriod->payment_date->format('Y-m-d') }}</span>
        </div>
        <div class="info-row">
            <span class="label">أيام العمل / Days Worked:</span>
            <span class="value">{{ $entry->days_worked }}</span>
        </div>
        @if($entry->days_absent > 0)
        <div class="info-row">
            <span class="label">أيام الغياب / Days Absent:</span>
            <span class="value">{{ $entry->days_absent }}</span>
        </div>
        @endif
    </div>

    <table>
        <thead>
            <tr>
                <th>البند / Item</th>
                <th>المبلغ / Amount (SAR)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>الراتب الأساسي / Basic Salary</td>
                <td>{{ number_format($entry->basic_salary, 2) }}</td>
            </tr>
            
            @if($entry->allowances->count() > 0)
            <tr>
                <td colspan="2" style="background-color: #f0f0f0; font-weight: bold;">البدلات / Allowances</td>
            </tr>
            @foreach($entry->allowances as $allowance)
            <tr>
                <td style="padding-right: 20px;">{{ $allowance->allowance_name }}</td>
                <td>{{ number_format($allowance->amount, 2) }}</td>
            </tr>
            @endforeach
            @endif

            @if($entry->overtime_hours > 0)
            <tr>
                <td>ساعات إضافية / Overtime ({{ $entry->overtime_hours }} hrs)</td>
                <td>{{ number_format($entry->overtime_amount, 2) }}</td>
            </tr>
            @endif

            <tr class="total-row">
                <td>إجمالي المستحقات / Gross Salary</td>
                <td>{{ number_format($entry->gross_salary, 2) }}</td>
            </tr>

            @if($entry->deductions->count() > 0)
            <tr>
                <td colspan="2" style="background-color: #f0f0f0; font-weight: bold;">الخصومات / Deductions</td>
            </tr>
            @foreach($entry->deductions as $deduction)
            <tr>
                <td style="padding-right: 20px;">{{ $deduction->deduction_name }}</td>
                <td>({{ number_format($deduction->amount, 2) }})</td>
            </tr>
            @endforeach

            <tr class="total-row">
                <td>إجمالي الخصومات / Total Deductions</td>
                <td>({{ number_format($entry->total_deductions, 2) }})</td>
            </tr>
            @endif
        </tbody>
    </table>

    <div class="net-salary">
        صافي الراتب / Net Salary: {{ number_format($entry->net_salary, 2) }} SAR
    </div>

    @if($entry->notes)
    <div style="margin-top: 20px; padding: 10px; border: 1px solid #ccc;">
        <strong>ملاحظات / Notes:</strong><br>
        {{ $entry->notes }}
    </div>
    @endif
</body>
</html>
