@extends('layouts.app')

@section('content')
<style>
    .page-header {
        background: white;
        padding: 25px 30px;
        border-radius: 12px;
        margin-bottom: 25px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    }

    .page-title {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--text);
        margin: 0 0 5px 0;
    }

    .alert-banner {
        background: linear-gradient(135deg, #ff3b30, #ff6b5a);
        color: white;
        padding: 20px 25px;
        border-radius: 12px;
        margin-bottom: 25px;
        display: flex;
        align-items: center;
        gap: 15px;
        box-shadow: 0 4px 12px rgba(255, 59, 48, 0.3);
    }

    .alert-icon {
        width: 40px;
        height: 40px;
    }

    .alert-content h3 {
        margin: 0 0 5px 0;
        font-size: 1.1rem;
        font-weight: 600;
    }

    .alert-content p {
        margin: 0;
        font-size: 0.9rem;
        opacity: 0.9;
    }

    .items-table {
        background: white;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        margin-bottom: 25px;
    }

    .table {
        width: 100%;
        border-collapse: collapse;
    }

    .table thead {
        background: #f5f5f7;
    }

    .table th {
        padding: 15px;
        text-align: right;
        font-size: 0.85rem;
        font-weight: 600;
        color: #86868b;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .table td {
        padding: 15px;
        border-top: 1px solid var(--border);
        font-size: 0.9rem;
    }

    .table tbody tr:hover {
        background: #f9f9f9;
    }

    .table tbody tr.critical {
        background: #fff5f5;
    }

    .table tbody tr.critical:hover {
        background: #ffebee;
    }

    .badge {
        display: inline-block;
        padding: 4px 10px;
        border-radius: 12px;
        font-size: 0.75rem;
        font-weight: 600;
        white-space: nowrap;
    }

    .badge-critical {
        background: #ffebee;
        color: #d32f2f;
    }

    .badge-warning {
        background: #fff3e0;
        color: #f57c00;
    }

    .badge-normal {
        background: #e8f5e9;
        color: #388e3c;
    }

    .badge-overdue {
        background: #d32f2f;
        color: white;
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.7; }
    }

    .progress-bar {
        height: 8px;
        background: #f5f5f7;
        border-radius: 4px;
        overflow: hidden;
        margin-top: 5px;
    }

    .progress-fill {
        height: 100%;
        background: linear-gradient(90deg, var(--accent), #00c4cc);
        border-radius: 4px;
        transition: width 0.3s;
    }

    .progress-fill.danger {
        background: linear-gradient(90deg, #ff3b30, #ff6b5a);
    }

    .progress-fill.warning {
        background: linear-gradient(90deg, #f57c00, #ffb74d);
    }

    .btn {
        padding: 10px 20px;
        border: none;
        border-radius: 8px;
        font-size: 0.9rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
        text-decoration: none;
        display: inline-block;
        font-family: 'Cairo', sans-serif;
    }

    .btn-primary {
        background: var(--accent);
        color: white;
    }

    .btn-secondary {
        background: #f5f5f7;
        color: var(--text);
    }

    .add-item-form {
        background: white;
        padding: 25px;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        margin-bottom: 25px;
        display: none;
    }

    .add-item-form.active {
        display: block;
    }

    .form-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 15px;
        margin-bottom: 20px;
    }

    .form-group label {
        display: block;
        font-size: 0.85rem;
        font-weight: 600;
        color: var(--text);
        margin-bottom: 5px;
    }

    .form-group input,
    .form-group select,
    .form-group textarea {
        width: 100%;
        padding: 10px;
        border: 1px solid var(--border);
        border-radius: 8px;
        font-size: 0.9rem;
        font-family: 'Cairo', sans-serif;
    }

    .form-group textarea {
        resize: vertical;
        min-height: 80px;
    }

    .checkbox-group {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 15px;
    }

    .checkbox-group input[type="checkbox"] {
        width: 18px;
        height: 18px;
    }

    .calendar-view {
        background: white;
        padding: 30px;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        margin-bottom: 25px;
    }

    .calendar-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        gap: 15px;
    }

    .calendar-card {
        background: #f9f9f9;
        padding: 15px;
        border-radius: 8px;
        border-right: 4px solid var(--accent);
    }

    .calendar-card.critical {
        border-right-color: #ff3b30;
        background: #fff5f5;
    }

    .calendar-card.overdue {
        border-right-color: #d32f2f;
        background: #ffebee;
    }

    .calendar-date {
        font-size: 0.8rem;
        color: #86868b;
        margin-bottom: 8px;
    }

    .calendar-item-name {
        font-weight: 600;
        color: var(--text);
        margin-bottom: 5px;
    }

    .calendar-lead-time {
        font-size: 0.85rem;
        color: #86868b;
    }
</style>

<div class="page-header">
    <h1 class="page-title">البنود طويلة الأجل - {{ $tender->name }}</h1>
    <p style="color: #86868b; font-size: 0.9rem; margin: 5px 0 0 0;">{{ $tender->tender_number }}</p>
</div>

@php
    $criticalItems = $longLeadItems->where('is_critical', true)->filter(function($item) {
        return $item->must_order_by->diffInDays(now(), false) <= 30;
    });
    $overdueItems = $longLeadItems->filter(function($item) {
        return $item->must_order_by->isPast();
    });
@endphp

@if($criticalItems->isNotEmpty() || $overdueItems->isNotEmpty())
<div class="alert-banner">
    <i data-lucide="alert-triangle" class="alert-icon"></i>
    <div class="alert-content">
        <h3>تنبيه: بنود تحتاج اهتمام فوري</h3>
        <p>
            @if($overdueItems->isNotEmpty())
                {{ $overdueItems->count() }} بند متأخر •
            @endif
            @if($criticalItems->isNotEmpty())
                {{ $criticalItems->count() }} بند حرج يجب طلبه خلال 30 يوم
            @endif
        </p>
    </div>
</div>
@endif

<div style="margin-bottom: 20px; display: flex; gap: 10px;">
    <a href="{{ route('tender-procurement.index', $tender->id) }}" class="btn btn-secondary">رجوع</a>
    <button onclick="toggleAddForm()" class="btn btn-primary">+ إضافة بند</button>
</div>

<div id="addItemForm" class="add-item-form">
    <h3 style="margin: 0 0 20px 0; font-size: 1.1rem; font-weight: 600;">إضافة بند طويل الأجل</h3>
    <form method="POST" action="{{ route('tender-procurement.long-lead-items.store', $tender->id) }}">
        @csrf
        <div class="form-grid">
            <div class="form-group">
                <label>اسم البند</label>
                <input type="text" name="item_name" required placeholder="اسم البند">
            </div>

            <div class="form-group">
                <label>مدة التوريد (أسابيع)</label>
                <input type="number" name="lead_time_weeks" required min="1" placeholder="عدد الأسابيع">
            </div>

            <div class="form-group">
                <label>يجب الطلب قبل</label>
                <input type="date" name="must_order_by" required>
            </div>

            <div class="form-group">
                <label>التكلفة المقدرة (ريال)</label>
                <input type="number" name="estimated_cost" required step="0.01" min="0">
            </div>
        </div>

        <div class="form-group">
            <label>الوصف</label>
            <textarea name="description" required placeholder="وصف تفصيلي للبند"></textarea>
        </div>

        <div class="form-group">
            <label>خطة التخفيف</label>
            <textarea name="mitigation_plan" placeholder="خطة بديلة في حالة التأخير"></textarea>
        </div>

        <div class="checkbox-group">
            <input type="checkbox" name="is_critical" id="is_critical" value="1">
            <label for="is_critical">بند حرج</label>
        </div>

        <div style="margin-top: 20px; display: flex; gap: 10px;">
            <button type="submit" class="btn btn-primary">حفظ البند</button>
            <button type="button" onclick="toggleAddForm()" class="btn btn-secondary">إلغاء</button>
        </div>
    </form>
</div>

<div class="items-table">
    @if($longLeadItems->isEmpty())
        <div style="text-align: center; padding: 60px 20px; color: #86868b;">
            <i data-lucide="clock" style="width: 60px; height: 60px; margin-bottom: 15px;"></i>
            <p>لا توجد بنود طويلة الأجل</p>
            <button onclick="toggleAddForm()" class="btn btn-primary" style="margin-top: 15px;">إضافة بند جديد</button>
        </div>
    @else
        <table class="table">
            <thead>
                <tr>
                    <th>البند</th>
                    <th>الحزمة</th>
                    <th>مدة التوريد</th>
                    <th>يجب الطلب قبل</th>
                    <th>الوقت المتبقي</th>
                    <th>التكلفة</th>
                    <th>الحالة</th>
                </tr>
            </thead>
            <tbody>
                @foreach($longLeadItems->sortBy('must_order_by') as $item)
                    @php
                        $daysRemaining = $item->must_order_by->diffInDays(now(), false);
                        $isOverdue = $item->must_order_by->isPast();
                        $progressPercent = 0;
                        
                        if (!$isOverdue && $item->lead_time_weeks > 0) {
                            $totalDays = $item->lead_time_weeks * 7;
                            $progressPercent = max(0, min(100, (($totalDays - $daysRemaining) / $totalDays) * 100));
                        } else if ($isOverdue) {
                            $progressPercent = 100;
                        }
                    @endphp
                    <tr class="{{ $item->is_critical ? 'critical' : '' }}">
                        <td>
                            <div>
                                <strong>{{ $item->item_name }}</strong>
                                <div style="font-size: 0.8rem; color: #86868b; margin-top: 3px;">
                                    {{ Str::limit($item->description, 60) }}
                                </div>
                            </div>
                        </td>
                        <td>
                            @if($item->procurementPackage)
                                <div>{{ $item->procurementPackage->package_name }}</div>
                                <div style="font-size: 0.75rem; color: #86868b;">
                                    {{ $item->procurementPackage->package_code }}
                                </div>
                            @else
                                <span style="color: #86868b;">-</span>
                            @endif
                        </td>
                        <td><strong>{{ $item->lead_time_weeks }}</strong> أسبوع</td>
                        <td>{{ $item->must_order_by->format('Y-m-d') }}</td>
                        <td>
                            @if($isOverdue)
                                <div>
                                    <span class="badge badge-overdue">متأخر بـ {{ abs($daysRemaining) }} يوم</span>
                                </div>
                            @else
                                <div>
                                    {{ $daysRemaining }} يوم
                                    <div class="progress-bar">
                                        <div class="progress-fill {{ $daysRemaining <= 7 ? 'danger' : ($daysRemaining <= 30 ? 'warning' : '') }}" 
                                             style="width: {{ $progressPercent }}%"></div>
                                    </div>
                                </div>
                            @endif
                        </td>
                        <td><strong>{{ number_format($item->estimated_cost, 2) }}</strong></td>
                        <td>
                            @if($isOverdue)
                                <span class="badge badge-overdue">⚠ متأخر</span>
                            @elseif($item->is_critical)
                                <span class="badge badge-critical">🔴 حرج</span>
                            @elseif($daysRemaining <= 30)
                                <span class="badge badge-warning">⚠ قريب</span>
                            @else
                                <span class="badge badge-normal">✓ عادي</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>

@if($longLeadItems->isNotEmpty())
<div class="calendar-view">
    <h3 style="margin: 0 0 25px 0; font-size: 1.2rem; font-weight: 600;">عرض التقويم</h3>
    
    <div class="calendar-grid">
        @foreach($longLeadItems->sortBy('must_order_by') as $item)
            @php
                $isOverdue = $item->must_order_by->isPast();
                $daysRemaining = $item->must_order_by->diffInDays(now(), false);
            @endphp
            <div class="calendar-card {{ $isOverdue ? 'overdue' : ($item->is_critical ? 'critical' : '') }}">
                <div class="calendar-date">
                    {{ $item->must_order_by->format('d M Y') }}
                    @if($isOverdue)
                        - متأخر
                    @elseif($daysRemaining <= 7)
                        - {{ $daysRemaining }} يوم متبقي
                    @endif
                </div>
                <div class="calendar-item-name">{{ $item->item_name }}</div>
                <div class="calendar-lead-time">
                    مدة التوريد: {{ $item->lead_time_weeks }} أسبوع
                </div>
                @if($item->is_critical)
                    <div style="margin-top: 8px;">
                        <span class="badge badge-critical">حرج</span>
                    </div>
                @endif
            </div>
        @endforeach
    </div>
</div>
@endif

<script>
    lucide.createIcons();

    function toggleAddForm() {
        const form = document.getElementById('addItemForm');
        form.classList.toggle('active');
        
        if (form.classList.contains('active')) {
            form.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    }
</script>
@endsection
