<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تقرير مؤشر الندم المالي - {{ $analysis->analysis_number }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            direction: rtl;
            text-align: right;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        .section {
            margin-bottom: 20px;
        }
        .section-title {
            background-color: #f0f0f0;
            padding: 8px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        table th, table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: right;
        }
        table th {
            background-color: #f2f2f2;
        }
        .total-row {
            font-weight: bold;
            background-color: #e8e8e8;
        }
        .result-box {
            background-color: #f9f9f9;
            border: 2px solid #333;
            padding: 15px;
            margin: 20px 0;
            text-align: center;
        }
        .result-box .value {
            font-size: 24px;
            font-weight: bold;
            color: #333;
        }
        .recommendation {
            background-color: #e8f4f8;
            padding: 10px;
            border-right: 4px solid #2196F3;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>تقرير مؤشر الندم المالي</h1>
        <p>رقم التحليل: {{ $analysis->analysis_number }}</p>
        <p>تاريخ التحليل: {{ $analysis->analysis_date->format('Y-m-d') }}</p>
    </div>

    <div class="section">
        <div class="section-title">معلومات المشروع</div>
        <table>
            <tr>
                <th>المشروع</th>
                <td>{{ $analysis->project->name }}</td>
            </tr>
            <tr>
                <th>المقاول</th>
                <td>{{ $analysis->contract->contractor_name }}</td>
            </tr>
            <tr>
                <th>قيمة العقد</th>
                <td>{{ number_format($analysis->contract_value, 2) }} {{ $analysis->currency }}</td>
            </tr>
            <tr>
                <th>نسبة الإنجاز</th>
                <td>{{ number_format($analysis->work_completed_percentage, 1) }}%</td>
            </tr>
        </table>
    </div>

    <div class="section">
        <div class="section-title">تكاليف الاستمرار مع المقاول الحالي</div>
        <table>
            <tr>
                <th>تكلفة الأعمال المتبقية</th>
                <td>{{ number_format($analysis->continuation_remaining_cost, 2) }} {{ $analysis->currency }}</td>
            </tr>
            <tr>
                <th>المطالبات المتوقعة</th>
                <td>{{ number_format($analysis->continuation_claims_estimate, 2) }} {{ $analysis->currency }}</td>
            </tr>
            <tr>
                <th>التعديلات</th>
                <td>{{ number_format($analysis->continuation_variations, 2) }} {{ $analysis->currency }}</td>
            </tr>
            <tr class="total-row">
                <th>الإجمالي</th>
                <td>{{ number_format($analysis->continuation_total, 2) }} {{ $analysis->currency }}</td>
            </tr>
        </table>
    </div>

    <div class="section">
        <div class="section-title">تكاليف إنهاء العقد</div>
        <table>
            <tr>
                <th>المستحقات للمقاول</th>
                <td>{{ number_format($analysis->termination_payment_due, 2) }} {{ $analysis->currency }}</td>
            </tr>
            <tr>
                <th>تكاليف الإخلاء</th>
                <td>{{ number_format($analysis->termination_demobilization, 2) }} {{ $analysis->currency }}</td>
            </tr>
            <tr>
                <th>المطالبات المتوقعة</th>
                <td>{{ number_format($analysis->termination_claims, 2) }} {{ $analysis->currency }}</td>
            </tr>
            <tr>
                <th>التكاليف القانونية</th>
                <td>{{ number_format($analysis->termination_legal_costs, 2) }} {{ $analysis->currency }}</td>
            </tr>
            <tr class="total-row">
                <th>الإجمالي</th>
                <td>{{ number_format($analysis->termination_total, 2) }} {{ $analysis->currency }}</td>
            </tr>
        </table>
    </div>

    <div class="section">
        <div class="section-title">تكاليف مقاول جديد</div>
        <table>
            <tr>
                <th>تكاليف التعبئة</th>
                <td>{{ number_format($analysis->new_contractor_mobilization, 2) }} {{ $analysis->currency }}</td>
            </tr>
            <tr>
                <th>تكلفة منحنى التعلم</th>
                <td>{{ number_format($analysis->new_contractor_learning_curve, 2) }} {{ $analysis->currency }}</td>
            </tr>
            <tr>
                <th>علاوة الدخول</th>
                <td>{{ number_format($analysis->new_contractor_premium, 2) }} {{ $analysis->currency }}</td>
            </tr>
            <tr>
                <th>تكلفة الأعمال المتبقية</th>
                <td>{{ number_format($analysis->new_contractor_remaining_work, 2) }} {{ $analysis->currency }}</td>
            </tr>
            <tr class="total-row">
                <th>الإجمالي</th>
                <td>{{ number_format($analysis->new_contractor_total, 2) }} {{ $analysis->currency }}</td>
            </tr>
        </table>
    </div>

    <div class="section">
        <div class="section-title">تكاليف التأخير</div>
        <table>
            <tr>
                <th>الأيام المتوقعة للتأخير</th>
                <td>{{ $analysis->estimated_delay_days }} يوم</td>
            </tr>
            <tr>
                <th>التكلفة اليومية</th>
                <td>{{ number_format($analysis->delay_cost_per_day, 2) }} {{ $analysis->currency }}</td>
            </tr>
            <tr class="total-row">
                <th>إجمالي تكلفة التأخير</th>
                <td>{{ number_format($analysis->total_delay_cost, 2) }} {{ $analysis->currency }}</td>
            </tr>
        </table>
    </div>

    <div class="result-box">
        <h2>النتيجة النهائية</h2>
        <table>
            <tr>
                <th>إجمالي تكلفة الاستمرار</th>
                <td>{{ number_format($analysis->cost_to_continue, 2) }} {{ $analysis->currency }}</td>
            </tr>
            <tr>
                <th>إجمالي تكلفة الإنهاء</th>
                <td>{{ number_format($analysis->cost_to_terminate, 2) }} {{ $analysis->currency }}</td>
            </tr>
            <tr class="total-row">
                <th>مؤشر الندم المالي</th>
                <td class="value">{{ number_format($analysis->regret_index, 2) }} {{ $analysis->currency }}</td>
            </tr>
            <tr>
                <th>نسبة الندم</th>
                <td class="value">{{ number_format($analysis->regret_percentage, 1) }}%</td>
            </tr>
        </table>

        <div class="recommendation">
            <strong>التوصية:</strong>
            @if($analysis->recommendation === 'continue')
                يُوصى بالاستمرار مع المقاول الحالي
            @elseif($analysis->recommendation === 'negotiate')
                يُوصى بإعادة التفاوض
            @else
                يتطلب مراجعة دقيقة
            @endif
        </div>
    </div>

    @if($analysis->negotiation_points)
    <div class="section">
        <div class="section-title">نقاط التفاوض</div>
        <p style="white-space: pre-line;">{{ $analysis->negotiation_points }}</p>
    </div>
    @endif

    @if($analysis->analysis_notes)
    <div class="section">
        <div class="section-title">ملاحظات التحليل</div>
        <p style="white-space: pre-line;">{{ $analysis->analysis_notes }}</p>
    </div>
    @endif

    @if($analysis->scenarios->count() > 0)
    <div class="section">
        <div class="section-title">السيناريوهات</div>
        <table>
            <thead>
                <tr>
                    <th>اسم السيناريو</th>
                    <th>النوع</th>
                    <th>مؤشر الندم</th>
                </tr>
            </thead>
            <tbody>
                @foreach($analysis->scenarios as $scenario)
                <tr>
                    <td>{{ $scenario->scenario_name }}</td>
                    <td>
                        @if($scenario->scenario_type === 'optimistic')
                            متفائل
                        @elseif($scenario->scenario_type === 'realistic')
                            واقعي
                        @else
                            متشائم
                        @endif
                    </td>
                    <td>{{ number_format($scenario->regret_index, 2) }} {{ $analysis->currency }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <div style="margin-top: 40px; text-align: center; font-size: 12px; color: #666;">
        <p>تم إعداده بواسطة: {{ $analysis->preparedBy->name }}</p>
        @if($analysis->reviewedBy)
        <p>تمت المراجعة بواسطة: {{ $analysis->reviewedBy->name }}</p>
        @endif
        <p>تاريخ الإعداد: {{ $analysis->created_at->format('Y-m-d H:i') }}</p>
    </div>
</body>
</html>
