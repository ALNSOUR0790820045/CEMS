@extends('layouts.app')

@section('content')
<style>
    .card {
        background: white;
        border-radius: 12px;
        padding: 24px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        margin-bottom: 20px;
    }
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }
    .stat-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 20px;
        border-radius: 12px;
        text-align: center;
    }
    .stat-card.green {
        background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
    }
    .stat-card.orange {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    }
    .stat-card.red {
        background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
    }
    .stat-value {
        font-size: 2rem;
        font-weight: 700;
        margin: 10px 0;
    }
    .stat-label {
        font-size: 0.9rem;
        opacity: 0.9;
    }
    .filters {
        display: flex;
        gap: 15px;
        margin-bottom: 20px;
        flex-wrap: wrap;
    }
    .filters select,
    .filters input {
        padding: 8px 12px;
        border: 1px solid #ddd;
        border-radius: 6px;
        font-family: 'Cairo', sans-serif;
    }
    .table-container {
        overflow-x: auto;
    }
    table {
        width: 100%;
        border-collapse: collapse;
    }
    th, td {
        padding: 12px;
        text-align: right;
        border-bottom: 1px solid #eee;
    }
    th {
        background: #f8f9fa;
        font-weight: 600;
        color: #333;
    }
    .badge {
        padding: 4px 12px;
        border-radius: 12px;
        font-size: 0.85rem;
        font-weight: 500;
        display: inline-block;
    }
    .badge-green { background: #d4edda; color: #155724; }
    .badge-blue { background: #cce5ff; color: #004085; }
    .badge-yellow { background: #fff3cd; color: #856404; }
    .badge-purple { background: #e7d6f7; color: #6c2b9c; }
    .badge-orange { background: #ffe5cc; color: #cc5200; }
    .badge-red { background: #f8d7da; color: #721c24; }
    .badge-gray { background: #e2e3e5; color: #383d41; }
    .btn {
        padding: 8px 16px;
        border-radius: 6px;
        border: none;
        cursor: pointer;
        font-family: 'Cairo', sans-serif;
        text-decoration: none;
        display: inline-block;
        transition: all 0.2s;
    }
    .btn-primary {
        background: #0071e3;
        color: white;
    }
    .btn-primary:hover {
        background: #0059b3;
    }
    .signature-icons {
        display: flex;
        gap: 5px;
    }
    .signature-icon {
        width: 24px;
        height: 24px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
    }
    .signature-icon.approved { background: #28a745; color: white; }
    .signature-icon.pending { background: #ffc107; color: white; }
    .signature-icon.rejected { background: #dc3545; color: white; }
    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 30px;
    }
    .page-title {
        font-size: 1.8rem;
        font-weight: 700;
        color: #1d1d1f;
    }
</style>

<div class="page-header">
    <h1 class="page-title">أوامر التغيير</h1>
    <div>
        <a href="{{ route('change-orders.create') }}" class="btn btn-primary">
            <i data-lucide="plus" style="width: 16px; height: 16px; vertical-align: middle;"></i>
            إنشاء أمر تغيير جديد
        </a>
        <a href="{{ route('change-orders.report') }}" class="btn" style="background: #6c757d; color: white; margin-right: 10px;">
            <i data-lucide="bar-chart" style="width: 16px; height: 16px; vertical-align: middle;"></i>
            التقارير
        </a>
    </div>
</div>

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-label">إجمالي الأوامر</div>
        <div class="stat-value">{{ $stats['total'] }}</div>
    </div>
    <div class="stat-card green">
        <div class="stat-label">المعتمدة</div>
        <div class="stat-value">{{ $stats['approved'] }}</div>
    </div>
    <div class="stat-card orange">
        <div class="stat-label">المعلقة</div>
        <div class="stat-value">{{ $stats['pending'] }}</div>
    </div>
    <div class="stat-card red">
        <div class="stat-label">المرفوضة</div>
        <div class="stat-value">{{ $stats['rejected'] }}</div>
    </div>
</div>

<div class="card">
    <form method="GET" action="{{ route('change-orders.index') }}">
        <div class="filters">
            <select name="status" onchange="this.form.submit()">
                <option value="">كل الحالات</option>
                <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>مسودة</option>
                <option value="pending_pm" {{ request('status') === 'pending_pm' ? 'selected' : '' }}>بانتظار PM</option>
                <option value="pending_technical" {{ request('status') === 'pending_technical' ? 'selected' : '' }}>بانتظار الفني</option>
                <option value="pending_consultant" {{ request('status') === 'pending_consultant' ? 'selected' : '' }}>بانتظار الاستشاري</option>
                <option value="pending_client" {{ request('status') === 'pending_client' ? 'selected' : '' }}>بانتظار العميل</option>
                <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>معتمد</option>
                <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>مرفوض</option>
            </select>

            <select name="type" onchange="this.form.submit()">
                <option value="">كل الأنواع</option>
                <option value="scope_change" {{ request('type') === 'scope_change' ? 'selected' : '' }}>تغيير النطاق</option>
                <option value="quantity_change" {{ request('type') === 'quantity_change' ? 'selected' : '' }}>تغيير الكميات</option>
                <option value="design_change" {{ request('type') === 'design_change' ? 'selected' : '' }}>تغيير التصميم</option>
                <option value="specification_change" {{ request('type') === 'specification_change' ? 'selected' : '' }}>تغيير المواصفات</option>
            </select>

            <input type="date" name="date_from" value="{{ request('date_from') }}" placeholder="من تاريخ" onchange="this.form.submit()">
            <input type="date" name="date_to" value="{{ request('date_to') }}" placeholder="إلى تاريخ" onchange="this.form.submit()">

            @if(request()->hasAny(['status', 'type', 'date_from', 'date_to']))
                <a href="{{ route('change-orders.index') }}" class="btn" style="background: #6c757d; color: white;">
                    إعادة تعيين
                </a>
            @endif
        </div>
    </form>

    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>رقم CO</th>
                    <th>العنوان</th>
                    <th>المشروع</th>
                    <th>التاريخ</th>
                    <th>النوع</th>
                    <th>القيمة</th>
                    <th>الرسوم</th>
                    <th>التمديد</th>
                    <th>الحالة</th>
                    <th>التوقيعات</th>
                    <th>الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                @forelse($changeOrders as $co)
                <tr>
                    <td><strong>{{ $co->co_number }}</strong></td>
                    <td>{{ $co->title }}</td>
                    <td>{{ $co->project->name ?? '-' }}</td>
                    <td>{{ $co->issue_date->format('Y-m-d') }}</td>
                    <td>{{ $co->type_label }}</td>
                    <td>{{ number_format($co->total_amount, 2) }} ر.س</td>
                    <td>{{ number_format($co->total_fees, 2) }} ر.س</td>
                    <td>{{ $co->time_extension_days }} يوم</td>
                    <td>
                        <span class="badge badge-{{ $co->status_color }}">
                            {{ $co->status_label }}
                        </span>
                    </td>
                    <td>
                        <div class="signature-icons">
                            <div class="signature-icon {{ $co->pm_decision === 'approved' ? 'approved' : ($co->pm_decision === 'rejected' ? 'rejected' : 'pending') }}" title="مدير المشروع">1</div>
                            <div class="signature-icon {{ $co->technical_decision === 'approved' ? 'approved' : ($co->technical_decision === 'rejected' ? 'rejected' : 'pending') }}" title="المدير الفني">2</div>
                            <div class="signature-icon {{ $co->consultant_decision === 'approved' ? 'approved' : ($co->consultant_decision === 'rejected' ? 'rejected' : 'pending') }}" title="الاستشاري">3</div>
                            <div class="signature-icon {{ $co->client_decision === 'approved' ? 'approved' : ($co->client_decision === 'rejected' ? 'rejected' : 'pending') }}" title="العميل">4</div>
                        </div>
                    </td>
                    <td>
                        <a href="{{ route('change-orders.show', $co) }}" style="color: #0071e3; text-decoration: none;">
                            <i data-lucide="eye" style="width: 18px; height: 18px; vertical-align: middle;"></i>
                        </a>
                        @if($co->status === 'draft')
                        <a href="{{ route('change-orders.edit', $co) }}" style="color: #0071e3; text-decoration: none; margin-right: 10px;">
                            <i data-lucide="edit" style="width: 18px; height: 18px; vertical-align: middle;"></i>
                        </a>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="11" style="text-align: center; padding: 40px; color: #999;">
                        <i data-lucide="inbox" style="width: 48px; height: 48px; margin-bottom: 10px;"></i>
                        <div>لا توجد أوامر تغيير</div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top: 20px;">
        {{ $changeOrders->links() }}
    </div>
</div>

@push('scripts')
<script>
    lucide.createIcons();
</script>
@endpush
@endsection
