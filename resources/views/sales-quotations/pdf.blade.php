<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $salesQuotation->quotation_number }}</title>
    <style>
        @page {
            margin: 20px;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            direction: rtl;
            color: #333;
            line-height: 1.6;
        }

        .container {
            padding: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 3px solid #0071e3;
        }

        .header h1 {
            font-size: 28px;
            color: #0071e3;
            margin-bottom: 5px;
        }

        .header p {
            color: #666;
            font-size: 12px;
        }

        .quotation-info {
            display: table;
            width: 100%;
            margin-bottom: 30px;
        }

        .quotation-info .section {
            display: table-cell;
            width: 50%;
            padding: 15px;
            vertical-align: top;
        }

        .quotation-info .section h3 {
            font-size: 14px;
            color: #0071e3;
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .quotation-info .section p {
            font-size: 12px;
            margin: 5px 0;
            color: #333;
        }

        .quotation-number {
            text-align: center;
            font-size: 20px;
            font-weight: bold;
            color: #0071e3;
            margin-bottom: 20px;
            padding: 10px;
            background: #f0f8ff;
            border-radius: 5px;
        }

        .status {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 15px;
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .status-draft {
            background: #e3f2fd;
            color: #1976d2;
        }

        .status-sent {
            background: #fff3e0;
            color: #f57c00;
        }

        .status-accepted {
            background: #e8f5e9;
            color: #388e3c;
        }

        .status-rejected {
            background: #ffebee;
            color: #d32f2f;
        }

        .status-expired {
            background: #f5f5f5;
            color: #616161;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }

        thead {
            background: #f5f5f5;
        }

        th {
            padding: 12px 8px;
            text-align: right;
            font-size: 11px;
            font-weight: bold;
            color: #666;
            text-transform: uppercase;
            border-bottom: 2px solid #ddd;
        }

        td {
            padding: 10px 8px;
            text-align: right;
            font-size: 12px;
            border-bottom: 1px solid #eee;
        }

        tbody tr:nth-child(even) {
            background: #fafafa;
        }

        .totals {
            margin-top: 20px;
            float: left;
            width: 300px;
        }

        .totals table {
            margin: 0;
        }

        .totals td {
            padding: 8px;
        }

        .totals td:first-child {
            font-weight: bold;
            color: #666;
        }

        .totals td:last-child {
            text-align: left;
            font-weight: bold;
        }

        .total-row {
            background: #f0f8ff !important;
            border-top: 2px solid #0071e3 !important;
        }

        .total-row td {
            font-size: 16px;
            color: #0071e3 !important;
        }

        .notes {
            clear: both;
            margin-top: 40px;
            padding-top: 20px;
            border-top: 2px solid #eee;
        }

        .notes h4 {
            font-size: 14px;
            color: #0071e3;
            margin-bottom: 10px;
        }

        .notes p {
            font-size: 11px;
            color: #666;
            line-height: 1.8;
        }

        .footer {
            margin-top: 50px;
            padding-top: 20px;
            border-top: 2px solid #eee;
            text-align: center;
            font-size: 10px;
            color: #999;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>CEMS ERP</h1>
            <p>نظام إدارة المشاريع المتكامل</p>
            <p>المملكة العربية السعودية</p>
        </div>

        <div class="quotation-number">
            {{ $salesQuotation->quotation_number }}
            <span class="status status-{{ $salesQuotation->status }}">
                @switch($salesQuotation->status)
                    @case('draft') مسودة @break
                    @case('sent') مرسل @break
                    @case('accepted') مقبول @break
                    @case('rejected') مرفوض @break
                    @case('expired') منتهي @break
                @endswitch
            </span>
        </div>

        <div class="quotation-info">
            <div class="section">
                <h3>معلومات العميل</h3>
                <p><strong>{{ $salesQuotation->customer->name }}</strong></p>
                @if($salesQuotation->customer->email)
                    <p>البريد: {{ $salesQuotation->customer->email }}</p>
                @endif
                @if($salesQuotation->customer->phone)
                    <p>الهاتف: {{ $salesQuotation->customer->phone }}</p>
                @endif
                @if($salesQuotation->customer->address)
                    <p>العنوان: {{ $salesQuotation->customer->address }}</p>
                @endif
                @if($salesQuotation->customer->tax_number)
                    <p>الرقم الضريبي: {{ $salesQuotation->customer->tax_number }}</p>
                @endif
            </div>

            <div class="section">
                <h3>تفاصيل العرض</h3>
                <p><strong>تاريخ العرض:</strong> {{ $salesQuotation->quotation_date->format('Y-m-d') }}</p>
                <p><strong>صالح حتى:</strong> {{ $salesQuotation->valid_until->format('Y-m-d') }}</p>
                <p><strong>أنشئ بواسطة:</strong> {{ $salesQuotation->creator->name }}</p>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th style="width: 5%;">#</th>
                    <th style="width: 30%;">المنتج</th>
                    <th style="width: 10%;">الكمية</th>
                    <th style="width: 15%;">سعر الوحدة</th>
                    <th style="width: 10%;">الخصم</th>
                    <th style="width: 15%;">الضريبة</th>
                    <th style="width: 15%;">الإجمالي</th>
                </tr>
            </thead>
            <tbody>
                @foreach($salesQuotation->items as $index => $item)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $item->product->name }}</td>
                        <td>{{ number_format($item->quantity, 3) }}</td>
                        <td>{{ number_format($item->unit_price, 2) }} ر.س</td>
                        <td>{{ number_format($item->discount, 2) }} ر.س</td>
                        <td>{{ number_format($item->tax_amount, 2) }} ر.س<br><small>({{ $item->tax_rate }}%)</small></td>
                        <td><strong>{{ number_format($item->total, 2) }} ر.س</strong></td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="totals">
            <table>
                <tr>
                    <td>المجموع الفرعي:</td>
                    <td>{{ number_format($salesQuotation->subtotal, 2) }} ر.س</td>
                </tr>
                <tr>
                    <td>الخصم:</td>
                    <td>{{ number_format($salesQuotation->discount, 2) }} ر.س</td>
                </tr>
                <tr>
                    <td>الضريبة:</td>
                    <td>{{ number_format($salesQuotation->tax_amount, 2) }} ر.س</td>
                </tr>
                <tr class="total-row">
                    <td>الإجمالي:</td>
                    <td>{{ number_format($salesQuotation->total, 2) }} ر.س</td>
                </tr>
            </table>
        </div>

        @if($salesQuotation->terms_conditions)
            <div class="notes">
                <h4>الشروط والأحكام</h4>
                <p>{{ $salesQuotation->terms_conditions }}</p>
            </div>
        @endif

        @if($salesQuotation->notes)
            <div class="notes">
                <h4>ملاحظات</h4>
                <p>{{ $salesQuotation->notes }}</p>
            </div>
        @endif

        <div class="footer">
            <p>تم إنشاء هذا العرض بواسطة CEMS ERP - نظام إدارة المشاريع المتكامل</p>
            <p>{{ now()->format('Y-m-d H:i:s') }}</p>
        </div>
    </div>
</body>
</html>
