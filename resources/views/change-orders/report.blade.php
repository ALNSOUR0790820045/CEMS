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
    .stat-value {
        font-size: 2rem;
        font-weight: 700;
        margin: 10px 0;
    }
    .stat-label {
        font-size: 0.9rem;
        opacity: 0.9;
    }
    .chart-container {
        margin: 20px 0;
        padding: 20px;
        background: #f8f9fa;
        border-radius: 8px;
    }
    table {
        width: 100%;
        border-collapse: collapse;
    }
    th, td {
        padding: 10px;
        text-align: right;
        border-bottom: 1px solid #eee;
    }
    th {
        background: #f8f9fa;
        font-weight: 600;
    }
    .btn {
        padding: 10px 20px;
        border-radius: 6px;
        border: none;
        cursor: pointer;
        font-family: 'Cairo', sans-serif;
        text-decoration: none;
        display: inline-block;
    }
    .btn-primary {
        background: #0071e3;
        color: white;
    }
</style>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
    <h1 style="font-size: 1.8rem; font-weight: 700; color: #1d1d1f;">تقرير أوامر التغيير</h1>
    <a href="{{ route('change-orders.index') }}" class="btn btn-primary">العودة</a>
</div>

<div class="stats-grid">
    <div class="stat-card" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
        <div class="stat-label">إجمالي الأوامر</div>
        <div class="stat-value">{{ $changeOrders->count() }}</div>
    </div>
    <div class="stat-card" style="background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);">
        <div class="stat-label">القيمة الإيجابية</div>
        <div class="stat-value">{{ number_format($changeOrders->where('total_amount', '>', 0)->sum('total_amount'), 0) }}</div>
    </div>
    <div class="stat-card" style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);">
        <div class="stat-label">القيمة السلبية</div>
        <div class="stat-value">{{ number_format($changeOrders->where('total_amount', '<', 0)->sum('total_amount'), 0) }}</div>
    </div>
    <div class="stat-card" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
        <div class="stat-label">إجمالي الرسوم</div>
        <div class="stat-value">{{ number_format($changeOrders->sum('total_fees'), 0) }}</div>
    </div>
</div>

<div class="card">
    <h2 style="font-size: 1.3rem; font-weight: 600; margin-bottom: 20px;">الإحصائيات حسب النوع</h2>
    <table>
        <thead>
            <tr>
                <th>النوع</th>
                <th>العدد</th>
                <th>إجمالي القيمة</th>
            </tr>
        </thead>
        <tbody>
            @foreach($byType as $type)
            <tr>
                <td>{{ match($type->type) {
                    'scope_change' => 'تغيير في النطاق',
                    'quantity_change' => 'تغيير في الكميات',
                    'design_change' => 'تغيير في التصميم',
                    'specification_change' => 'تغيير في المواصفات',
                    default => 'أخرى'
                } }}</td>
                <td>{{ $type->count }}</td>
                <td>{{ number_format($type->total, 2) }} ر.س</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<div class="card">
    <h2 style="font-size: 1.3rem; font-weight: 600; margin-bottom: 20px;">الإحصائيات حسب الحالة</h2>
    <table>
        <thead>
            <tr>
                <th>الحالة</th>
                <th>العدد</th>
            </tr>
        </thead>
        <tbody>
            @foreach($byStatus as $status)
            <tr>
                <td>{{ match($status->status) {
                    'draft' => 'مسودة',
                    'pending_pm' => 'بانتظار PM',
                    'pending_technical' => 'بانتظار الفني',
                    'pending_consultant' => 'بانتظار الاستشاري',
                    'pending_client' => 'بانتظار العميل',
                    'approved' => 'معتمد',
                    'rejected' => 'مرفوض',
                    default => 'أخرى'
                } }}</td>
                <td>{{ $status->count }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<div class="card">
    <h2 style="font-size: 1.3rem; font-weight: 600; margin-bottom: 20px;">الاتجاه الشهري</h2>
    <table>
        <thead>
            <tr>
                <th>الشهر</th>
                <th>العدد</th>
                <th>إجمالي القيمة</th>
            </tr>
        </thead>
        <tbody>
            @foreach($monthlyTrend as $month)
            <tr>
                <td>{{ $month->month }}</td>
                <td>{{ $month->count }}</td>
                <td>{{ number_format($month->total, 2) }} ر.س</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

@push('scripts')
<script>
    lucide.createIcons();
</script>
@endpush
@endsection
