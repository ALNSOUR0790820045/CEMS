<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>أمر التغيير {{ $variationOrder->vo_number }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Cairo', sans-serif;
            padding: 40px;
            background: white;
            color: #1d1d1f;
        }
        
        .header {
            text-align: center;
            margin-bottom: 40px;
            border-bottom: 3px solid #0071e3;
            padding-bottom: 20px;
        }
        
        .header h1 {
            font-size: 2rem;
            margin-bottom: 10px;
            color: #0071e3;
        }
        
        .header .vo-number {
            font-size: 1.5rem;
            font-weight: 700;
            color: #1d1d1f;
        }
        
        .section {
            margin-bottom: 30px;
            page-break-inside: avoid;
        }
        
        .section-title {
            background: #0071e3;
            color: white;
            padding: 10px 15px;
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 15px;
            border-radius: 5px;
        }
        
        .info-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
            margin-bottom: 20px;
        }
        
        .info-item {
            padding: 10px;
            background: #f5f5f7;
            border-radius: 5px;
        }
        
        .info-label {
            color: #666;
            font-size: 0.9rem;
            margin-bottom: 5px;
        }
        
        .info-value {
            font-weight: 600;
            font-size: 1.1rem;
        }
        
        .description-box {
            padding: 15px;
            background: #f5f5f7;
            border-radius: 5px;
            line-height: 1.8;
            margin-bottom: 15px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        
        table th, table td {
            padding: 12px;
            text-align: right;
            border: 1px solid #ddd;
        }
        
        table th {
            background: #f5f5f7;
            font-weight: 600;
        }
        
        .timeline-entry {
            padding: 12px;
            border-right: 3px solid #0071e3;
            background: #f5f5f7;
            margin-bottom: 10px;
            border-radius: 3px;
        }
        
        .timeline-entry .date {
            color: #999;
            font-size: 0.85rem;
        }
        
        .footer {
            margin-top: 50px;
            padding-top: 20px;
            border-top: 2px solid #ddd;
            text-align: center;
            color: #999;
            font-size: 0.9rem;
        }
        
        @media print {
            body {
                padding: 20px;
            }
            
            .no-print {
                display: none;
            }
            
            .section {
                page-break-inside: avoid;
            }
        }
    </style>
</head>
<body>
    <div class="no-print" style="margin-bottom: 20px; text-align: left;">
        <button onclick="window.print()" style="background: #0071e3; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; font-family: 'Cairo', sans-serif; font-weight: 600;">
            طباعة
        </button>
        <button onclick="window.close()" style="background: #6c757d; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; font-family: 'Cairo', sans-serif; font-weight: 600; margin-right: 10px;">
            إغلاق
        </button>
    </div>

    <div class="header">
        <h1>أمر التغيير - Variation Order</h1>
        <div class="vo-number">{{ $variationOrder->vo_number }}</div>
    </div>

    <!-- Basic Information -->
    <div class="section">
        <div class="section-title">المعلومات الأساسية - Basic Information</div>
        <div class="info-grid">
            <div class="info-item">
                <div class="info-label">المشروع - Project</div>
                <div class="info-value">{{ $variationOrder->project->name }}</div>
            </div>
            @if($variationOrder->contract)
                <div class="info-item">
                    <div class="info-label">العقد - Contract</div>
                    <div class="info-value">{{ $variationOrder->contract->title }}</div>
                </div>
            @endif
            <div class="info-item">
                <div class="info-label">النوع - Type</div>
                <div class="info-value">
                    @switch($variationOrder->type)
                        @case('addition') إضافة أعمال - Addition @break
                        @case('omission') حذف أعمال - Omission @break
                        @case('modification') تعديل أعمال - Modification @break
                        @case('substitution') استبدال - Substitution @break
                    @endswitch
                </div>
            </div>
            <div class="info-item">
                <div class="info-label">المصدر - Source</div>
                <div class="info-value">
                    @switch($variationOrder->source)
                        @case('client') طلب العميل - Client Request @break
                        @case('consultant') طلب الاستشاري - Consultant Request @break
                        @case('contractor') طلب المقاول - Contractor Request @break
                        @case('design_change') تغيير التصميم - Design Change @break
                        @case('site_condition') ظروف الموقع - Site Condition @break
                    @endswitch
                </div>
            </div>
            <div class="info-item">
                <div class="info-label">الحالة - Status</div>
                <div class="info-value">
                    @php
                        $statusLabels = [
                            'identified' => 'تم تحديده - Identified',
                            'draft' => 'مسودة - Draft',
                            'submitted' => 'مقدم - Submitted',
                            'under_review' => 'قيد المراجعة - Under Review',
                            'approved' => 'معتمد - Approved',
                            'rejected' => 'مرفوض - Rejected',
                        ];
                    @endphp
                    {{ $statusLabels[$variationOrder->status] ?? $variationOrder->status }}
                </div>
            </div>
            <div class="info-item">
                <div class="info-label">الأولوية - Priority</div>
                <div class="info-value">
                    @php
                        $priorityLabels = [
                            'low' => 'منخفضة - Low',
                            'medium' => 'متوسطة - Medium',
                            'high' => 'عالية - High',
                            'critical' => 'حرجة - Critical',
                        ];
                    @endphp
                    {{ $priorityLabels[$variationOrder->priority] }}
                </div>
            </div>
        </div>
    </div>

    <!-- Title and Description -->
    <div class="section">
        <div class="section-title">العنوان والوصف - Title & Description</div>
        <div class="info-item" style="margin-bottom: 15px;">
            <div class="info-label">العنوان - Title</div>
            <div class="info-value">{{ $variationOrder->title }}</div>
        </div>
        <div class="description-box">
            <strong>الوصف - Description:</strong><br>
            {{ $variationOrder->description }}
        </div>
        @if($variationOrder->justification)
            <div class="description-box">
                <strong>المبررات - Justification:</strong><br>
                {{ $variationOrder->justification }}
            </div>
        @endif
    </div>

    <!-- Financial Information -->
    <div class="section">
        <div class="section-title">المعلومات المالية - Financial Information</div>
        <div class="info-grid">
            <div class="info-item">
                <div class="info-label">القيمة المقدرة - Estimated Value</div>
                <div class="info-value" style="color: #0071e3;">
                    {{ number_format($variationOrder->estimated_value, 2) }} {{ $variationOrder->currency }}
                </div>
            </div>
            <div class="info-item">
                <div class="info-label">القيمة المقتبسة - Quoted Value</div>
                <div class="info-value">
                    {{ number_format($variationOrder->quoted_value, 2) }} {{ $variationOrder->currency }}
                </div>
            </div>
            <div class="info-item">
                <div class="info-label">القيمة المعتمدة - Approved Value</div>
                <div class="info-value" style="color: #28a745;">
                    {{ number_format($variationOrder->approved_value, 2) }} {{ $variationOrder->currency }}
                </div>
            </div>
            <div class="info-item">
                <div class="info-label">القيمة المنفذة - Executed Value</div>
                <div class="info-value">
                    {{ number_format($variationOrder->executed_value, 2) }} {{ $variationOrder->currency }}
                </div>
            </div>
        </div>
    </div>

    <!-- Time Impact -->
    <div class="section">
        <div class="section-title">التأثير على المدة - Time Impact</div>
        <div class="info-grid">
            <div class="info-item">
                <div class="info-label">التأثير على المدة - Time Impact</div>
                <div class="info-value">{{ $variationOrder->time_impact_days }} يوم - Days</div>
            </div>
            <div class="info-item">
                <div class="info-label">تمديد معتمد - Extension Approved</div>
                <div class="info-value">{{ $variationOrder->extension_approved ? 'نعم - Yes' : 'لا - No' }}</div>
            </div>
            @if($variationOrder->extension_approved)
                <div class="info-item">
                    <div class="info-label">أيام التمديد المعتمدة - Approved Extension Days</div>
                    <div class="info-value">{{ $variationOrder->approved_extension_days }} يوم - Days</div>
                </div>
            @endif
        </div>
    </div>

    <!-- Dates -->
    <div class="section">
        <div class="section-title">التواريخ - Dates</div>
        <table>
            <tr>
                <th>الحدث - Event</th>
                <th>التاريخ - Date</th>
            </tr>
            <tr>
                <td>تاريخ التحديد - Identification Date</td>
                <td>{{ $variationOrder->identification_date?->format('Y-m-d') ?? 'N/A' }}</td>
            </tr>
            @if($variationOrder->submission_date)
                <tr>
                    <td>تاريخ التقديم - Submission Date</td>
                    <td>{{ $variationOrder->submission_date?->format('Y-m-d') }}</td>
                </tr>
            @endif
            @if($variationOrder->approval_date)
                <tr>
                    <td>تاريخ الاعتماد - Approval Date</td>
                    <td>{{ $variationOrder->approval_date?->format('Y-m-d') }}</td>
                </tr>
            @endif
            @if($variationOrder->execution_start_date)
                <tr>
                    <td>تاريخ بدء التنفيذ - Execution Start Date</td>
                    <td>{{ $variationOrder->execution_start_date?->format('Y-m-d') }}</td>
                </tr>
            @endif
            @if($variationOrder->execution_end_date)
                <tr>
                    <td>تاريخ انتهاء التنفيذ - Execution End Date</td>
                    <td>{{ $variationOrder->execution_end_date?->format('Y-m-d') }}</td>
                </tr>
            @endif
        </table>
    </div>

    <!-- Approvers -->
    <div class="section">
        <div class="section-title">الموافقات - Approvals</div>
        <div class="info-grid">
            <div class="info-item">
                <div class="info-label">مقدم الطلب - Requested By</div>
                <div class="info-value">{{ $variationOrder->requestedBy->name }}</div>
            </div>
            @if($variationOrder->preparedBy)
                <div class="info-item">
                    <div class="info-label">معد بواسطة - Prepared By</div>
                    <div class="info-value">{{ $variationOrder->preparedBy->name }}</div>
                </div>
            @endif
            @if($variationOrder->approvedBy)
                <div class="info-item">
                    <div class="info-label">معتمد بواسطة - Approved By</div>
                    <div class="info-value">{{ $variationOrder->approvedBy->name }}</div>
                </div>
            @endif
        </div>
        @if($variationOrder->rejection_reason)
            <div class="description-box" style="background: #f8d7da; border-right: 3px solid #dc3545;">
                <strong style="color: #721c24;">سبب الرفض - Rejection Reason:</strong><br>
                <span style="color: #721c24;">{{ $variationOrder->rejection_reason }}</span>
            </div>
        @endif
    </div>

    <!-- Timeline -->
    <div class="section">
        <div class="section-title">السجل الزمني - Timeline</div>
        @forelse($variationOrder->timeline as $entry)
            <div class="timeline-entry">
                <div style="display: flex; justify-content: space-between; margin-bottom: 5px;">
                    <strong>{{ $entry->action }}</strong>
                    <span class="date">{{ $entry->created_at->format('Y-m-d H:i') }}</span>
                </div>
                @if($entry->from_status || $entry->to_status)
                    <div style="color: #666; font-size: 0.9rem;">
                        @if($entry->from_status) من - From: {{ $entry->from_status }} @endif
                        @if($entry->to_status) إلى - To: {{ $entry->to_status }} @endif
                    </div>
                @endif
                @if($entry->notes)
                    <div style="margin-top: 5px;">{{ $entry->notes }}</div>
                @endif
                <div style="color: #999; font-size: 0.85rem; margin-top: 5px;">
                    بواسطة - By: {{ $entry->performedBy->name }}
                </div>
            </div>
        @empty
            <p style="color: #999; text-align: center; padding: 20px;">لا توجد أحداث - No entries</p>
        @endforelse
    </div>

    <div class="footer">
        <p>تم الطباعة في - Printed on: {{ now()->format('Y-m-d H:i') }}</p>
        <p>نظام إدارة المشاريع الإنشائية - Construction Engineering Management System (CEMS)</p>
    </div>
</body>
</html>
