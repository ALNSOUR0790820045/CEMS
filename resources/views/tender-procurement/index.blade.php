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

    .breadcrumb {
        font-size: 0.85rem;
        color: #86868b;
        display: flex;
        gap: 8px;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
        margin-bottom: 25px;
    }

    .stat-card {
        background: white;
        padding: 20px;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    }

    .stat-label {
        font-size: 0.8rem;
        color: #86868b;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 8px;
    }

    .stat-value {
        font-size: 1.8rem;
        font-weight: 700;
        color: var(--text);
    }

    .filters-bar {
        background: white;
        padding: 20px;
        border-radius: 12px;
        margin-bottom: 20px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    }

    .filters-row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 15px;
    }

    .filter-group label {
        display: block;
        font-size: 0.85rem;
        color: #86868b;
        margin-bottom: 5px;
        font-weight: 600;
    }

    .filter-group select {
        width: 100%;
        padding: 10px 12px;
        border: 1px solid var(--border);
        border-radius: 8px;
        font-size: 0.9rem;
        font-family: 'Cairo', sans-serif;
        background: white;
    }

    .packages-table {
        background: white;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
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

    .badge {
        display: inline-block;
        padding: 4px 10px;
        border-radius: 12px;
        font-size: 0.75rem;
        font-weight: 600;
        white-space: nowrap;
    }

    .badge-planned { background: #e3f2fd; color: #1976d2; }
    .badge-rfq { background: #fff3e0; color: #f57c00; }
    .badge-quotations { background: #f3e5f5; color: #7b1fa2; }
    .badge-evaluated { background: #e0f2f1; color: #00796b; }
    .badge-approved { background: #e8f5e9; color: #388e3c; }

    .badge-materials { background: #e3f2fd; color: #1976d2; }
    .badge-equipment { background: #fce4ec; color: #c2185b; }
    .badge-subcontract { background: #fff3e0; color: #f57c00; }
    .badge-services { background: #f3e5f5; color: #7b1fa2; }
    .badge-rental { background: #e0f2f1; color: #00796b; }

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

    .btn-primary:hover {
        background: #005bb5;
    }

    .btn-sm {
        padding: 6px 12px;
        font-size: 0.8rem;
    }

    .actions-group {
        display: flex;
        gap: 8px;
        justify-content: flex-end;
    }

    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: #86868b;
    }

    .empty-state i {
        width: 60px;
        height: 60px;
        margin-bottom: 15px;
        color: #d0d0d0;
    }

    .alert {
        padding: 15px 20px;
        border-radius: 8px;
        margin-bottom: 20px;
        font-size: 0.9rem;
    }

    .alert-success {
        background: #e8f5e9;
        color: #2e7d32;
        border: 1px solid #a5d6a7;
    }
</style>

<div class="page-header">
    <h1 class="page-title">خطة الشراء - {{ $tender->name }}</h1>
    <div class="breadcrumb">
        <span>العطاءات</span>
        <span>/</span>
        <span>{{ $tender->tender_number }}</span>
        <span>/</span>
        <span>خطة الشراء</span>
    </div>
</div>

@if(session('success'))
<div class="alert alert-success">
    {{ session('success') }}
</div>
@endif

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-label">إجمالي الحزم</div>
        <div class="stat-value">{{ $packages->count() }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">القيمة الإجمالية</div>
        <div class="stat-value">{{ number_format($totalValue, 2) }} ريال</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">حزم مخططة</div>
        <div class="stat-value">{{ $packages->where('status', 'planned')->count() }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">حزم معتمدة</div>
        <div class="stat-value">{{ $packages->where('status', 'approved')->count() }}</div>
    </div>
</div>

<div class="filters-bar">
    <form method="GET" action="{{ route('tender-procurement.index', $tender->id) }}">
        <div class="filters-row">
            <div class="filter-group">
                <label>نوع الشراء</label>
                <select name="procurement_type" onchange="this.form.submit()">
                    <option value="">الكل</option>
                    <option value="materials" {{ request('procurement_type') == 'materials' ? 'selected' : '' }}>مواد</option>
                    <option value="equipment" {{ request('procurement_type') == 'equipment' ? 'selected' : '' }}>معدات</option>
                    <option value="subcontract" {{ request('procurement_type') == 'subcontract' ? 'selected' : '' }}>مقاولة فرعية</option>
                    <option value="services" {{ request('procurement_type') == 'services' ? 'selected' : '' }}>خدمات</option>
                    <option value="rental" {{ request('procurement_type') == 'rental' ? 'selected' : '' }}>إيجار</option>
                </select>
            </div>

            <div class="filter-group">
                <label>التصنيف</label>
                <select name="category" onchange="this.form.submit()">
                    <option value="">الكل</option>
                    <option value="civil" {{ request('category') == 'civil' ? 'selected' : '' }}>مدني</option>
                    <option value="structural" {{ request('category') == 'structural' ? 'selected' : '' }}>إنشائي</option>
                    <option value="architectural" {{ request('category') == 'architectural' ? 'selected' : '' }}>معماري</option>
                    <option value="electrical" {{ request('category') == 'electrical' ? 'selected' : '' }}>كهربائي</option>
                    <option value="mechanical" {{ request('category') == 'mechanical' ? 'selected' : '' }}>ميكانيكي</option>
                    <option value="plumbing" {{ request('category') == 'plumbing' ? 'selected' : '' }}>صحي</option>
                    <option value="finishing" {{ request('category') == 'finishing' ? 'selected' : '' }}>تشطيبات</option>
                </select>
            </div>

            <div class="filter-group">
                <label>الحالة</label>
                <select name="status" onchange="this.form.submit()">
                    <option value="">الكل</option>
                    <option value="planned" {{ request('status') == 'planned' ? 'selected' : '' }}>مخطط</option>
                    <option value="rfq_prepared" {{ request('status') == 'rfq_prepared' ? 'selected' : '' }}>تم إعداد الطلب</option>
                    <option value="quotations_received" {{ request('status') == 'quotations_received' ? 'selected' : '' }}>تم استلام العروض</option>
                    <option value="evaluated" {{ request('status') == 'evaluated' ? 'selected' : '' }}>تم التقييم</option>
                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>معتمد</option>
                </select>
            </div>

            <div class="filter-group" style="display: flex; align-items: flex-end;">
                <a href="{{ route('tender-procurement.create', $tender->id) }}" class="btn btn-primary">
                    + إضافة حزمة
                </a>
            </div>
        </div>
    </form>
</div>

<div class="packages-table">
    @if($packages->isEmpty())
        <div class="empty-state">
            <i data-lucide="package"></i>
            <p>لا توجد حزم شراء</p>
            <a href="{{ route('tender-procurement.create', $tender->id) }}" class="btn btn-primary">إضافة حزمة جديدة</a>
        </div>
    @else
        <table class="table">
            <thead>
                <tr>
                    <th>الكود</th>
                    <th>الاسم</th>
                    <th>النوع</th>
                    <th>التصنيف</th>
                    <th>القيمة</th>
                    <th>تاريخ الحاجة</th>
                    <th>Lead Time</th>
                    <th>الحالة</th>
                    <th>المسؤول</th>
                    <th>الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                @foreach($packages as $package)
                <tr>
                    <td><strong>{{ $package->package_code }}</strong></td>
                    <td>{{ $package->package_name }}</td>
                    <td>
                        <span class="badge badge-{{ $package->procurement_type }}">
                            {{ $package->getProcurementTypeLabel() }}
                        </span>
                    </td>
                    <td>{{ $package->getCategoryLabel() }}</td>
                    <td><strong>{{ number_format($package->estimated_value, 2) }}</strong></td>
                    <td>{{ $package->required_by_date?->format('Y-m-d') ?? '-' }}</td>
                    <td>{{ $package->lead_time_days ?? '-' }} يوم</td>
                    <td>
                        <span class="badge badge-{{ str_replace('_', '-', $package->status) }}">
                            {{ $package->status_label }}
                        </span>
                    </td>
                    <td>{{ $package->responsible?->name ?? '-' }}</td>
                    <td>
                        <div class="actions-group">
                            <a href="{{ route('tender-procurement.show', [$tender->id, $package->id]) }}" 
                               class="btn btn-sm btn-primary">عرض</a>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>

<div style="margin-top: 20px; display: flex; gap: 15px;">
    <a href="{{ route('tender-procurement.timeline', $tender->id) }}" class="btn btn-primary">
        <i data-lucide="gantt-chart" style="width: 16px; height: 16px;"></i>
        الجدول الزمني
    </a>
    <a href="{{ route('tender-procurement.long-lead-items', $tender->id) }}" class="btn btn-primary">
        <i data-lucide="clock" style="width: 16px; height: 16px;"></i>
        البنود طويلة الأجل
    </a>
</div>

<script>
    lucide.createIcons();
</script>
@endsection
