<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>أمر تغيير {{ $changeOrder->co_number }}</title>
    <style>
        @page {
            margin: 2cm;
        }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            direction: rtl;
            text-align: right;
            font-size: 12pt;
            line-height: 1.6;
        }
        h1 {
            text-align: center;
            color: #1d1d1f;
            border-bottom: 3px solid #0071e3;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .section {
            margin-bottom: 25px;
            page-break-inside: avoid;
        }
        .section-title {
            font-size: 14pt;
            font-weight: bold;
            color: #0071e3;
            border-bottom: 2px solid #eee;
            padding-bottom: 5px;
            margin-bottom: 15px;
        }
        .info-grid {
            display: table;
            width: 100%;
            margin-bottom: 15px;
        }
        .info-row {
            display: table-row;
        }
        .info-label {
            display: table-cell;
            font-weight: bold;
            width: 30%;
            padding: 5px;
            background-color: #f8f9fa;
        }
        .info-value {
            display: table-cell;
            width: 70%;
            padding: 5px;
            border-bottom: 1px solid #eee;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: right;
        }
        th {
            background-color: #f8f9fa;
            font-weight: bold;
        }
        .signature-box {
            margin-top: 30px;
            padding: 15px;
            border: 1px solid #ddd;
            page-break-inside: avoid;
        }
        .signature-title {
            font-weight: bold;
            margin-bottom: 10px;
        }
        .signature-line {
            margin-top: 40px;
            border-top: 1px solid #000;
            width: 200px;
            display: inline-block;
        }
        .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            text-align: center;
            font-size: 10pt;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>أمر تغيير - Change Order</h1>
        <p><strong>رقم الأمر:</strong> {{ $changeOrder->co_number }}</p>
        <p><strong>التاريخ:</strong> {{ $changeOrder->issue_date->format('Y-m-d') }}</p>
    </div>

    <div class="section">
        <div class="section-title">المعلومات الأساسية</div>
        <div class="info-grid">
            <div class="info-row">
                <div class="info-label">العنوان:</div>
                <div class="info-value">{{ $changeOrder->title }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">المشروع:</div>
                <div class="info-value">{{ $changeOrder->project->name ?? '-' }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">النوع:</div>
                <div class="info-value">{{ $changeOrder->type_label }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">السبب:</div>
                <div class="info-value">{{ $changeOrder->reason_label }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">الحالة:</div>
                <div class="info-value">{{ $changeOrder->status_label }}</div>
            </div>
        </div>
        <p><strong>الوصف:</strong></p>
        <p style="text-align: justify;">{{ $changeOrder->description }}</p>
        @if($changeOrder->justification)
        <p><strong>التبرير:</strong></p>
        <p style="text-align: justify;">{{ $changeOrder->justification }}</p>
        @endif
    </div>

    <div class="section">
        <div class="section-title">التحليل المالي</div>
        <div class="info-grid">
            <div class="info-row">
                <div class="info-label">قيمة العقد الأصلي:</div>
                <div class="info-value">{{ number_format($changeOrder->original_contract_value, 2) }} ر.س</div>
            </div>
            <div class="info-row">
                <div class="info-label">قيمة التغيير (net):</div>
                <div class="info-value">{{ number_format($changeOrder->net_amount, 2) }} ر.س</div>
            </div>
            <div class="info-row">
                <div class="info-label">الضريبة (15%):</div>
                <div class="info-value">{{ number_format($changeOrder->tax_amount, 2) }} ر.س</div>
            </div>
            <div class="info-row">
                <div class="info-label">الإجمالي:</div>
                <div class="info-value"><strong>{{ number_format($changeOrder->total_amount, 2) }} ر.س</strong></div>
            </div>
        </div>
        <div class="info-grid" style="margin-top: 15px;">
            <div class="info-row">
                <div class="info-label">الرسوم المحسوبة:</div>
                <div class="info-value">{{ number_format($changeOrder->calculated_fee, 2) }} ر.س</div>
            </div>
            <div class="info-row">
                <div class="info-label">رسوم الطوابع:</div>
                <div class="info-value">{{ number_format($changeOrder->stamp_duty, 2) }} ر.س</div>
            </div>
            <div class="info-row">
                <div class="info-label">إجمالي الرسوم:</div>
                <div class="info-value"><strong>{{ number_format($changeOrder->total_fees, 2) }} ر.س</strong></div>
            </div>
            <div class="info-row">
                <div class="info-label">القيمة المحدثة للعقد:</div>
                <div class="info-value"><strong>{{ number_format($changeOrder->updated_contract_value, 2) }} ر.س</strong></div>
            </div>
        </div>
    </div>

    @if($changeOrder->items->count() > 0)
    <div class="section">
        <div class="section-title">بنود التغيير</div>
        <table>
            <thead>
                <tr>
                    <th>الوصف</th>
                    <th>الكمية الأصلية</th>
                    <th>الكمية المعدلة</th>
                    <th>الفرق</th>
                    <th>الوحدة</th>
                    <th>سعر الوحدة</th>
                    <th>المبلغ</th>
                </tr>
            </thead>
            <tbody>
                @foreach($changeOrder->items as $item)
                <tr>
                    <td>{{ $item->description }}</td>
                    <td>{{ number_format($item->original_quantity, 3) }}</td>
                    <td>{{ number_format($item->changed_quantity, 3) }}</td>
                    <td>{{ number_format($item->quantity_difference, 3) }}</td>
                    <td>{{ $item->unit }}</td>
                    <td>{{ number_format($item->unit_price, 2) }}</td>
                    <td>{{ number_format($item->amount, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <div class="section">
        <div class="section-title">الأثر على الجدول الزمني</div>
        <div class="info-grid">
            <div class="info-row">
                <div class="info-label">عدد أيام التمديد:</div>
                <div class="info-value">{{ $changeOrder->time_extension_days }} يوم</div>
            </div>
            @if($changeOrder->new_completion_date)
            <div class="info-row">
                <div class="info-label">تاريخ الإنجاز الجديد:</div>
                <div class="info-value">{{ $changeOrder->new_completion_date->format('Y-m-d') }}</div>
            </div>
            @endif
        </div>
        @if($changeOrder->schedule_impact_description)
        <p>{{ $changeOrder->schedule_impact_description }}</p>
        @endif
    </div>

    <div class="section">
        <div class="section-title">التوقيعات</div>
        
        <div class="signature-box">
            <div class="signature-title">1. مدير المشروع</div>
            <p>الاسم: {{ $changeOrder->pmUser->name ?? '___________________' }}</p>
            @if($changeOrder->pm_signed_at)
            <p>التاريخ: {{ $changeOrder->pm_signed_at->format('Y-m-d H:i') }}</p>
            <p>القرار: {{ $changeOrder->pm_decision === 'approved' ? '✓ موافق' : ($changeOrder->pm_decision === 'rejected' ? '✗ مرفوض' : 'معلق') }}</p>
            @else
            <div class="signature-line"></div>
            @endif
        </div>

        <div class="signature-box">
            <div class="signature-title">2. المدير الفني</div>
            <p>الاسم: {{ $changeOrder->technicalUser->name ?? '___________________' }}</p>
            @if($changeOrder->technical_signed_at)
            <p>التاريخ: {{ $changeOrder->technical_signed_at->format('Y-m-d H:i') }}</p>
            <p>القرار: {{ $changeOrder->technical_decision === 'approved' ? '✓ موافق' : ($changeOrder->technical_decision === 'rejected' ? '✗ مرفوض' : 'معلق') }}</p>
            @else
            <div class="signature-line"></div>
            @endif
        </div>

        <div class="signature-box">
            <div class="signature-title">3. الاستشاري</div>
            <p>الاسم: {{ $changeOrder->consultantUser->name ?? '___________________' }}</p>
            @if($changeOrder->consultant_signed_at)
            <p>التاريخ: {{ $changeOrder->consultant_signed_at->format('Y-m-d H:i') }}</p>
            <p>القرار: {{ $changeOrder->consultant_decision === 'approved' ? '✓ موافق' : ($changeOrder->consultant_decision === 'rejected' ? '✗ مرفوض' : 'معلق') }}</p>
            @else
            <div class="signature-line"></div>
            @endif
        </div>

        <div class="signature-box">
            <div class="signature-title">4. العميل</div>
            <p>الاسم: {{ $changeOrder->clientUser->name ?? '___________________' }}</p>
            @if($changeOrder->client_signed_at)
            <p>التاريخ: {{ $changeOrder->client_signed_at->format('Y-m-d H:i') }}</p>
            <p>القرار: {{ $changeOrder->client_decision === 'approved' ? '✓ موافق' : ($changeOrder->client_decision === 'rejected' ? '✗ مرفوض' : 'معلق') }}</p>
            @else
            <div class="signature-line"></div>
            @endif
        </div>
    </div>

    <div class="footer">
        <p>تم إنشاء هذا المستند تلقائياً بواسطة نظام CEMS ERP في {{ now()->format('Y-m-d H:i') }}</p>
    </div>
</body>
</html>
