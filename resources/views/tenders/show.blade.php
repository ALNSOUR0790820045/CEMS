@extends('layouts.app')

@section('content')
<style>
    .card {
        background: white;
        border-radius: 12px;
        padding: 24px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        margin-bottom: 20px;
    }

    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 30px;
    }

    .page-title {
        font-size: 2rem;
        font-weight: 700;
        color: #1d1d1f;
    }

    .tender-number {
        font-size: 1rem;
        color: #86868b;
        font-weight: 400;
        margin-top: 5px;
    }

    .badge {
        display: inline-block;
        padding: 6px 16px;
        border-radius: 20px;
        font-size: 0.85rem;
        font-weight: 600;
    }

    .badge-status {
        background: #e3f2fd;
        color: #1976d2;
    }

    .btn {
        padding: 10px 20px;
        border-radius: 8px;
        border: none;
        font-size: 0.9rem;
        font-weight: 600;
        cursor: pointer;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.2s;
    }

    .btn-primary {
        background: #0071e3;
        color: white;
    }

    .btn-success {
        background: #34c759;
        color: white;
    }

    .btn-warning {
        background: #ff9500;
        color: white;
    }

    .info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
        margin-bottom: 20px;
    }

    .info-item {
        display: flex;
        flex-direction: column;
        gap: 5px;
    }

    .info-label {
        font-size: 0.75rem;
        color: #86868b;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .info-value {
        font-size: 1rem;
        color: #1d1d1f;
        font-weight: 500;
    }

    .timeline {
        border-right: 3px solid #e5e5e7;
        padding-right: 20px;
        margin-right: 20px;
    }

    .timeline-item {
        position: relative;
        padding-bottom: 20px;
    }

    .timeline-item::before {
        content: '';
        position: absolute;
        right: -31px;
        top: 5px;
        width: 16px;
        height: 16px;
        border-radius: 50%;
        background: #0071e3;
        border: 3px solid white;
        box-shadow: 0 0 0 2px #e5e5e7;
    }

    .timeline-date {
        font-size: 0.85rem;
        color: #1d1d1f;
        font-weight: 600;
        margin-bottom: 5px;
    }

    .timeline-label {
        font-size: 0.8rem;
        color: #86868b;
    }

    .timeline-days {
        font-size: 0.75rem;
        color: #ff3b30;
        font-weight: 600;
        margin-top: 3px;
    }

    .section-title {
        font-size: 1.3rem;
        font-weight: 700;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .table {
        width: 100%;
        border-collapse: collapse;
    }

    .table th {
        background: #f5f5f7;
        padding: 12px;
        text-align: right;
        font-size: 0.85rem;
        font-weight: 600;
        border-bottom: 2px solid #e5e5e7;
    }

    .table td {
        padding: 12px;
        border-bottom: 1px solid #e5e5e7;
        font-size: 0.9rem;
    }

    .action-buttons {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }

    .empty-state {
        text-align: center;
        padding: 40px 20px;
        color: #86868b;
    }

    .description-box {
        background: #f5f5f7;
        padding: 20px;
        border-radius: 8px;
        line-height: 1.6;
        white-space: pre-wrap;
    }

    .deadline-warning {
        background: #fff3cd;
        border: 2px solid #ff9500;
        padding: 20px;
        border-radius: 8px;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .deadline-critical {
        background: #fee;
        border-color: #ff3b30;
    }
</style>

<div class="page-header">
    <div>
        <h1 class="page-title">{{ $tender->tender_name }}</h1>
        <div class="tender-number">{{ $tender->tender_number }}</div>
        @if($tender->reference_number)
            <div class="tender-number">الرقم المرجعي: {{ $tender->reference_number }}</div>
        @endif
    </div>
    <div style="display: flex; gap: 10px;">
        <span class="badge badge-status">
            @switch($tender->status)
                @case('announced') معلن @break
                @case('evaluating') قيد التقييم @break
                @case('decision_pending') قيد اتخاذ القرار @break
                @case('preparing') قيد التحضير @break
                @case('submitted') تم التقديم @break
                @case('awarded') تمت الترسية @break
                @case('lost') خسرنا @break
                @case('cancelled') ألغي @break
                @case('passed') لم نتقدم @break
            @endswitch
        </span>
    </div>
</div>

@php
    $days = $tender->getDaysUntilSubmission();
    $urgency = $tender->getDeadlineUrgency();
@endphp

@if($days >= 0 && $days <= 30)
<div class="deadline-warning {{ $days <= 15 ? 'deadline-critical' : '' }}">
    <i data-lucide="alert-triangle" style="width: 32px; height: 32px; color: {{ $days <= 15 ? '#ff3b30' : '#ff9500' }};"></i>
    <div>
        <strong style="font-size: 1.1rem;">تنبيه موعد التقديم</strong>
        <p style="margin: 5px 0 0 0;">باقي <strong>{{ $days }}</strong> {{ $days == 1 ? 'يوم' : 'أيام' }} على آخر موعد للتقديم ({{ $tender->submission_deadline->format('Y-m-d') }})</p>
    </div>
</div>
@endif

<!-- Overview Section -->
<div class="card">
    <h2 class="section-title">
        <i data-lucide="info"></i>
        نظرة عامة
    </h2>

    <div class="info-grid">
        <div class="info-item">
            <span class="info-label">الجهة المالكة</span>
            <span class="info-value">{{ $tender->owner_name }}</span>
        </div>

        <div class="info-item">
            <span class="info-label">نوع العطاء</span>
            <span class="info-value">
                @switch($tender->tender_type)
                    @case('construction') إنشاءات @break
                    @case('infrastructure') بنية تحتية @break
                    @case('buildings') مباني @break
                    @case('roads') طرق @break
                    @case('bridges') جسور @break
                    @case('water') مياه وصرف صحي @break
                    @case('electrical') كهرباء @break
                    @case('mechanical') ميكانيكا @break
                    @case('maintenance') صيانة @break
                    @case('consultancy') استشارات @break
                    @default {{ $tender->tender_type }}
                @endswitch
            </span>
        </div>

        <div class="info-item">
            <span class="info-label">نوع العقد</span>
            <span class="info-value">
                @switch($tender->contract_type)
                    @case('lump_sum') مقطوعية @break
                    @case('unit_price') أسعار وحدات @break
                    @case('cost_plus') تكلفة + ربح @break
                    @case('time_material') مياومة @break
                    @case('design_build') تصميم وتنفيذ @break
                    @case('epc') EPC @break
                    @case('bot') BOT @break
                    @default {{ $tender->contract_type }}
                @endswitch
            </span>
        </div>

        <div class="info-item">
            <span class="info-label">القيمة التقديرية</span>
            <span class="info-value">{{ number_format($tender->estimated_value ?? 0, 2) }} {{ $tender->currency->code ?? '' }}</span>
        </div>

        <div class="info-item">
            <span class="info-label">المدة المقدرة</span>
            <span class="info-value">{{ $tender->estimated_duration_months ?? '-' }} شهر</span>
        </div>

        <div class="info-item">
            <span class="info-label">الموقع</span>
            <span class="info-value">{{ $tender->country->name ?? '' }}{{ $tender->city ? ', ' . $tender->city->name : '' }}</span>
        </div>

        <div class="info-item">
            <span class="info-label">المسؤول</span>
            <span class="info-value">{{ $tender->assignedUser->name ?? '-' }}</span>
        </div>
    </div>

    <div style="margin-top: 20px;">
        <span class="info-label">الوصف</span>
        <div class="description-box">{{ $tender->description }}</div>
    </div>
</div>

<!-- Timeline Section -->
<div class="card">
    <h2 class="section-title">
        <i data-lucide="calendar"></i>
        الجدول الزمني
    </h2>

    <div class="timeline">
        @if($tender->announcement_date)
        <div class="timeline-item">
            <div class="timeline-date">📅 {{ $tender->announcement_date->format('Y-m-d') }}</div>
            <div class="timeline-label">الإعلان</div>
        </div>
        @endif

        @if($tender->site_visit_date)
        <div class="timeline-item">
            <div class="timeline-date">📅 {{ $tender->site_visit_date->format('Y-m-d') }}</div>
            <div class="timeline-label">زيارة الموقع</div>
        </div>
        @endif

        @if($tender->questions_deadline)
        <div class="timeline-item">
            <div class="timeline-date">📅 {{ $tender->questions_deadline->format('Y-m-d') }}</div>
            <div class="timeline-label">آخر موعد للاستفسارات</div>
        </div>
        @endif

        <div class="timeline-item">
            <div class="timeline-date" style="color: #ff3b30; font-size: 1.1rem;">📅 {{ $tender->submission_deadline->format('Y-m-d') }}</div>
            <div class="timeline-label" style="font-weight: 700;">آخر موعد للتقديم</div>
            @if($days >= 0)
                <div class="timeline-days">⏰ باقي {{ $days }} {{ $days == 1 ? 'يوم' : 'أيام' }}</div>
            @endif
        </div>

        @if($tender->opening_date)
        <div class="timeline-item">
            <div class="timeline-date">📅 {{ $tender->opening_date->format('Y-m-d') }}</div>
            <div class="timeline-label">موعد الفتح</div>
        </div>
        @endif
    </div>
</div>

<!-- Decision Section -->
<div class="card">
    <h2 class="section-title">
        <i data-lucide="target"></i>
        قرار المشاركة
    </h2>

    @if($tender->participate !== null)
        <div style="padding: 20px; background: {{ $tender->participate ? '#e8f5e9' : '#ffebee' }}; border-radius: 8px;">
            <div style="font-size: 1.2rem; font-weight: 700; margin-bottom: 10px;">
                القرار: <strong>{{ $tender->participate ? 'المشاركة في العطاء' : 'عدم المشاركة' }}</strong>
            </div>
            @if($tender->participation_decision_notes)
                <div style="margin-top: 10px;">
                    <strong>الأسباب:</strong>
                    <p style="margin-top: 5px;">{{ $tender->participation_decision_notes }}</p>
                </div>
            @endif
            @if($tender->decider)
                <div style="margin-top: 10px; font-size: 0.9rem; color: #86868b;">
                    تم اتخاذ القرار بواسطة: {{ $tender->decider->name }} في {{ $tender->decision_date->format('Y-m-d') }}
                </div>
            @endif
        </div>
    @else
        <div class="empty-state">
            <p>لم يتم اتخاذ قرار بعد</p>
            <a href="{{ route('tenders.decision', $tender) }}" class="btn btn-primary" style="margin-top: 15px;">
                <i data-lucide="check-circle" style="width: 18px; height: 18px;"></i>
                اتخاذ القرار (Go/No-Go)
            </a>
        </div>
    @endif

    @if($tender->committeeDecisions->count() > 0)
        <h3 style="font-size: 1.1rem; font-weight: 700; margin: 30px 0 15px 0;">قرارات اللجنة</h3>
        <table class="table">
            <thead>
                <tr>
                    <th>تاريخ الاجتماع</th>
                    <th>القرار</th>
                    <th>الميزانية المعتمدة</th>
                    <th>رئيس اللجنة</th>
                </tr>
            </thead>
            <tbody>
                @foreach($tender->committeeDecisions as $decision)
                <tr>
                    <td>{{ $decision->meeting_date->format('Y-m-d') }}</td>
                    <td>
                        @switch($decision->decision)
                            @case('go') المضي قدماً @break
                            @case('no_go') عدم المشاركة @break
                            @case('pending') قيد الدراسة @break
                        @endswitch
                    </td>
                    <td>{{ number_format($decision->approved_budget ?? 0, 2) }}</td>
                    <td>{{ $decision->chairman->name ?? '' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>

<!-- Site Visits Section -->
<div class="card">
    <h2 class="section-title">
        <i data-lucide="map-pin"></i>
        زيارات الموقع
    </h2>

    @if($tender->siteVisits->count() > 0)
        <table class="table">
            <thead>
                <tr>
                    <th>التاريخ</th>
                    <th>الوقت</th>
                    <th>الملاحظات</th>
                    <th>المقرر</th>
                </tr>
            </thead>
            <tbody>
                @foreach($tender->siteVisits as $visit)
                <tr>
                    <td>{{ $visit->visit_date->format('Y-m-d') }}</td>
                    <td>{{ $visit->visit_time ?? '-' }}</td>
                    <td>{{ Str::limit($visit->observations ?? '', 100) }}</td>
                    <td>{{ $visit->reporter->name ?? '' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div class="empty-state">
            <p>لم يتم تسجيل زيارات موقع بعد</p>
        </div>
    @endif

    <div style="margin-top: 20px;">
        <a href="{{ route('tenders.site-visit', $tender) }}" class="btn btn-primary">
            <i data-lucide="map" style="width: 18px; height: 18px;"></i>
            تسجيل زيارة موقع
        </a>
    </div>
</div>

<!-- Clarifications Section -->
<div class="card">
    <h2 class="section-title">
        <i data-lucide="message-circle"></i>
        الاستفسارات
    </h2>

    @if($tender->clarifications->count() > 0)
        <table class="table">
            <thead>
                <tr>
                    <th>التاريخ</th>
                    <th>السؤال</th>
                    <th>الإجابة</th>
                    <th>الحالة</th>
                </tr>
            </thead>
            <tbody>
                @foreach($tender->clarifications as $clarification)
                <tr>
                    <td>{{ $clarification->question_date->format('Y-m-d') }}</td>
                    <td>{{ Str::limit($clarification->question, 100) }}</td>
                    <td>{{ $clarification->answer ? Str::limit($clarification->answer, 100) : '-' }}</td>
                    <td>
                        <span class="badge" style="background: {{ $clarification->status == 'answered' ? '#e8f5e9' : '#fff3e0' }}; color: {{ $clarification->status == 'answered' ? '#388e3c' : '#f57c00' }};">
                            {{ $clarification->status == 'answered' ? 'تمت الإجابة' : 'معلق' }}
                        </span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div class="empty-state">
            <p>لا توجد استفسارات</p>
        </div>
    @endif
</div>

<!-- Competitors Section -->
<div class="card">
    <h2 class="section-title">
        <i data-lucide="users"></i>
        تحليل المنافسين
    </h2>

    @if($tender->competitors->count() > 0)
        <table class="table">
            <thead>
                <tr>
                    <th>الشركة</th>
                    <th>التصنيف</th>
                    <th>السعر المتوقع</th>
                    <th>نقاط القوة</th>
                    <th>نقاط الضعف</th>
                </tr>
            </thead>
            <tbody>
                @foreach($tender->competitors as $competitor)
                <tr>
                    <td><strong>{{ $competitor->company_name }}</strong></td>
                    <td>
                        <span class="badge" style="background: 
                            {{ $competitor->classification == 'strong' ? '#ffebee' : ($competitor->classification == 'medium' ? '#fff3e0' : '#e8f5e9') }};
                            color: {{ $competitor->classification == 'strong' ? '#d32f2f' : ($competitor->classification == 'medium' ? '#f57c00' : '#388e3c') }};">
                            {{ $competitor->classification == 'strong' ? 'قوي' : ($competitor->classification == 'medium' ? 'متوسط' : 'ضعيف') }}
                        </span>
                    </td>
                    <td>{{ number_format($competitor->estimated_price ?? 0, 2) }}</td>
                    <td>{{ Str::limit($competitor->strengths ?? '', 50) }}</td>
                    <td>{{ Str::limit($competitor->weaknesses ?? '', 50) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div class="empty-state">
            <p>لم يتم إضافة منافسين بعد</p>
        </div>
    @endif

    <div style="margin-top: 20px;">
        <a href="{{ route('tenders.competitors', $tender) }}" class="btn btn-primary">
            <i data-lucide="user-plus" style="width: 18px; height: 18px;"></i>
            إضافة / إدارة المنافسين
        </a>
    </div>
</div>

<!-- Actions -->
<div class="card">
    <h2 class="section-title">
        <i data-lucide="zap"></i>
        الإجراءات
    </h2>

    <div class="action-buttons">
        <a href="{{ route('tenders.edit', $tender) }}" class="btn btn-primary">
            <i data-lucide="edit" style="width: 18px; height: 18px;"></i>
            تعديل العطاء
        </a>

        @if($tender->participate === null)
            <a href="{{ route('tenders.decision', $tender) }}" class="btn btn-warning">
                <i data-lucide="check-circle" style="width: 18px; height: 18px;"></i>
                اتخاذ قرار المشاركة
            </a>
        @endif

        @if($tender->status == 'preparing')
            <button class="btn btn-success">
                <i data-lucide="file-text" style="width: 18px; height: 18px;"></i>
                تحضير العرض الفني
            </button>

            <button class="btn btn-success">
                <i data-lucide="dollar-sign" style="width: 18px; height: 18px;"></i>
                تحضير العرض المالي
            </button>
        @endif

        @if($tender->status == 'awarded')
            <button class="btn btn-success">
                <i data-lucide="folder-plus" style="width: 18px; height: 18px;"></i>
                إنشاء مشروع
            </button>
        @endif

        <a href="{{ route('tenders.index') }}" class="btn" style="background: #f5f5f7; color: #1d1d1f;">
            <i data-lucide="arrow-right" style="width: 18px; height: 18px;"></i>
            رجوع للقائمة
        </a>
    </div>
</div>

<script>
    lucide.createIcons();
</script>
@endsection
