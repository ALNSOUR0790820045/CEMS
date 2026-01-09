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

    .kpi-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }

    .kpi-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 12px;
        padding: 24px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }

    .kpi-card.green {
        background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
    }

    .kpi-card.blue {
        background: linear-gradient(135deg, #0071e3 0%, #00c4cc 100%);
    }

    .kpi-card.orange {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    }

    .kpi-value {
        font-size: 2.5rem;
        font-weight: 700;
        margin: 10px 0;
    }

    .kpi-label {
        font-size: 0.9rem;
        opacity: 0.9;
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

    .badge {
        display: inline-block;
        padding: 4px 12px;
        border-radius: 12px;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .badge-critical {
        background: #fee;
        color: #c00;
    }

    .badge-warning {
        background: #fff3cd;
        color: #856404;
    }

    .badge-safe {
        background: #d4edda;
        color: #155724;
    }

    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 30px;
    }

    .page-title {
        font-size: 2rem;
        font-weight: 700;
        color: #1d1d1f;
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

    .btn-primary:hover {
        background: #0077ed;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 113, 227, 0.3);
    }

    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: #86868b;
    }

    .empty-state i {
        width: 64px;
        height: 64px;
        margin-bottom: 20px;
        color: #d2d2d7;
    }
</style>

<div class="page-header">
    <h1 class="page-title">لوحة العطاءات</h1>
    <a href="{{ route('tenders.create') }}" class="btn btn-primary">
        <i data-lucide="plus" style="width: 18px; height: 18px;"></i>
        إضافة عطاء جديد
    </a>
</div>

<!-- KPIs -->
<div class="kpi-grid">
    <div class="kpi-card">
        <div class="kpi-label">عطاءات نشطة</div>
        <div class="kpi-value">{{ $activeTenders }}</div>
    </div>

    <div class="kpi-card green">
        <div class="kpi-label">قيد التحضير</div>
        <div class="kpi-value">{{ $preparingTenders }}</div>
    </div>

    <div class="kpi-card blue">
        <div class="kpi-label">نسبة الفوز</div>
        <div class="kpi-value">{{ $winRate }}%</div>
    </div>

    <div class="kpi-card orange">
        <div class="kpi-label">القيمة الإجمالية</div>
        <div class="kpi-value" style="font-size: 1.8rem;">{{ number_format($pipelineValue, 0) }}</div>
    </div>
</div>

<!-- Upcoming Deadlines -->
<div class="card">
    <h2 style="font-size: 1.5rem; font-weight: 700; margin-bottom: 20px;">
        <i data-lucide="calendar-clock" style="width: 24px; height: 24px; vertical-align: middle;"></i>
        المواعيد القادمة
    </h2>

    @if($upcomingTenders->count() > 0)
        <table class="table">
            <thead>
                <tr>
                    <th>رقم العطاء</th>
                    <th>اسم العطاء</th>
                    <th>الجهة المالكة</th>
                    <th>موعد التقديم</th>
                    <th>الأيام المتبقية</th>
                    <th>الحالة</th>
                    <th>الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                @foreach($upcomingTenders as $tender)
                <tr>
                    <td>{{ $tender->tender_number }}</td>
                    <td>{{ $tender->tender_name }}</td>
                    <td>{{ $tender->owner_name }}</td>
                    <td>{{ $tender->submission_deadline->format('Y-m-d') }}</td>
                    <td>
                        @php
                            $days = $tender->getDaysUntilSubmission();
                            $urgency = $tender->getDeadlineUrgency();
                        @endphp
                        <span class="badge badge-{{ $urgency }}">
                            {{ $days }} {{ $days == 1 ? 'يوم' : 'أيام' }}
                        </span>
                    </td>
                    <td>
                        @switch($tender->status)
                            @case('announced') معلن @break
                            @case('evaluating') قيد التقييم @break
                            @case('decision_pending') قيد اتخاذ القرار @break
                            @case('preparing') قيد التحضير @break
                            @default {{ $tender->status }}
                        @endswitch
                    </td>
                    <td>
                        <a href="{{ route('tenders.show', $tender) }}" style="color: #0071e3; text-decoration: none;">
                            عرض
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div class="empty-state">
            <i data-lucide="inbox"></i>
            <p>لا توجد عطاءات قادمة</p>
        </div>
    @endif
</div>

<!-- Recent Tenders -->
<div class="card">
    <h2 style="font-size: 1.5rem; font-weight: 700; margin-bottom: 20px;">
        <i data-lucide="file-text" style="width: 24px; height: 24px; vertical-align: middle;"></i>
        العطاءات الأخيرة
    </h2>

    @if($recentTenders->count() > 0)
        <table class="table">
            <thead>
                <tr>
                    <th>رقم العطاء</th>
                    <th>اسم العطاء</th>
                    <th>الجهة المالكة</th>
                    <th>القيمة التقديرية</th>
                    <th>تاريخ الإضافة</th>
                    <th>الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                @foreach($recentTenders as $tender)
                <tr>
                    <td>{{ $tender->tender_number }}</td>
                    <td>{{ $tender->tender_name }}</td>
                    <td>{{ $tender->owner_name }}</td>
                    <td>
                        {{ number_format($tender->estimated_value ?? 0, 0) }}
                        {{ $tender->currency->code ?? '' }}
                    </td>
                    <td>{{ $tender->created_at->format('Y-m-d') }}</td>
                    <td>
                        <a href="{{ route('tenders.show', $tender) }}" style="color: #0071e3; text-decoration: none;">
                            عرض
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div class="empty-state">
            <i data-lucide="inbox"></i>
            <p>لا توجد عطاءات</p>
        </div>
    @endif

    <div style="margin-top: 20px; text-align: center;">
        <a href="{{ route('tenders.index') }}" class="btn btn-primary">
            عرض جميع العطاءات
        </a>
    </div>
</div>

<script>
    lucide.createIcons();
</script>
@endsection
