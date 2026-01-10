@extends('layouts.app')

@section('content')
<style>
    .eot-dashboard {
        max-width: 1400px;
        margin: 0 auto;
    }
    
    .page-header {
        background: white;
        border-radius: 12px;
        padding: 30px;
        margin-bottom: 30px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    }
    
    .page-header h1 {
        font-size: 2rem;
        font-weight: 700;
        margin: 0 0 10px 0;
        color: #1d1d1f;
    }
    
    .page-header p {
        color: #86868b;
        margin: 0;
    }
    
    .kpi-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }
    
    .kpi-card {
        background: white;
        border-radius: 12px;
        padding: 25px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        transition: transform 0.2s;
    }
    
    .kpi-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 4px 16px rgba(0,0,0,0.1);
    }
    
    .kpi-label {
        font-size: 0.85rem;
        color: #86868b;
        margin-bottom: 10px;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    
    .kpi-value {
        font-size: 2rem;
        font-weight: 700;
        color: #1d1d1f;
        margin-bottom: 5px;
    }
    
    .kpi-subtitle {
        font-size: 0.8rem;
        color: #86868b;
    }
    
    .charts-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }
    
    .chart-card {
        background: white;
        border-radius: 12px;
        padding: 25px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    }
    
    .chart-title {
        font-size: 1.1rem;
        font-weight: 600;
        margin-bottom: 20px;
        color: #1d1d1f;
    }
    
    .recent-claims {
        background: white;
        border-radius: 12px;
        padding: 25px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    }
    
    .claims-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 15px;
    }
    
    .claims-table th {
        background: #f5f5f7;
        padding: 12px;
        text-align: right;
        font-size: 0.85rem;
        font-weight: 600;
        color: #1d1d1f;
        border-bottom: 2px solid #e5e5e7;
    }
    
    .claims-table td {
        padding: 12px;
        border-bottom: 1px solid #f5f5f7;
        font-size: 0.9rem;
    }
    
    .claims-table tr:hover {
        background: #f9f9fb;
    }
    
    .status-badge {
        display: inline-block;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
    }
    
    .status-draft { background: #f5f5f7; color: #86868b; }
    .status-submitted { background: #e3f2fd; color: #1976d2; }
    .status-approved { background: #e8f5e9; color: #388e3c; }
    .status-rejected { background: #ffebee; color: #d32f2f; }
    .status-review { background: #fff3e0; color: #f57c00; }
    
    .btn-primary {
        background: #0071e3;
        color: white;
        border: none;
        padding: 10px 24px;
        border-radius: 8px;
        font-size: 0.9rem;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s;
        text-decoration: none;
        display: inline-block;
    }
    
    .btn-primary:hover {
        background: #0077ed;
        transform: translateY(-2px);
    }
    
    .action-bar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }
    
    .cause-chart-item {
        display: flex;
        justify-content: space-between;
        padding: 10px 0;
        border-bottom: 1px solid #f5f5f7;
    }
    
    .cause-name {
        font-size: 0.9rem;
        color: #1d1d1f;
    }
    
    .cause-count {
        font-weight: 600;
        color: #0071e3;
    }
</style>

<div class="eot-dashboard">
    <div class="page-header">
        <div class="action-bar">
            <div>
                <h1>لوحة تحكم EOT والإطالة</h1>
                <p>نظام إدارة مطالبات تمديد الوقت وتكاليف الإطالة</p>
            </div>
            <div>
                <a href="{{ route('eot.create') }}" class="btn-primary">
                    <i data-lucide="plus" style="width: 16px; height: 16px;"></i>
                    مطالبة جديدة
                </a>
                <a href="{{ route('eot.report') }}" class="btn-primary" style="background: #6c757d; margin-right: 10px;">
                    <i data-lucide="file-text" style="width: 16px; height: 16px;"></i>
                    التقارير
                </a>
            </div>
        </div>
    </div>

    <!-- KPIs -->
    <div class="kpi-grid">
        <div class="kpi-card">
            <div class="kpi-label">
                <i data-lucide="file-text" style="width: 18px; height: 18px; color: #0071e3;"></i>
                إجمالي المطالبات
            </div>
            <div class="kpi-value">{{ $totalClaims }}</div>
            <div class="kpi-subtitle">مطالبة EOT</div>
        </div>

        <div class="kpi-card">
            <div class="kpi-label">
                <i data-lucide="calendar" style="width: 18px; height: 18px; color: #f57c00;"></i>
                الأيام المطلوبة
            </div>
            <div class="kpi-value">{{ number_format($requestedDays) }}</div>
            <div class="kpi-subtitle">يوم</div>
        </div>

        <div class="kpi-card">
            <div class="kpi-label">
                <i data-lucide="check-circle" style="width: 18px; height: 18px; color: #388e3c;"></i>
                الأيام المعتمدة
            </div>
            <div class="kpi-value">{{ number_format($approvedDays) }}</div>
            <div class="kpi-subtitle">يوم ({{ number_format($approvalRate, 1) }}%)</div>
        </div>

        <div class="kpi-card">
            <div class="kpi-label">
                <i data-lucide="dollar-sign" style="width: 18px; height: 18px; color: #d32f2f;"></i>
                التكاليف المطالب بها
            </div>
            <div class="kpi-value">{{ number_format($totalCostsClaimed, 0) }}</div>
            <div class="kpi-subtitle">دينار أردني</div>
        </div>
    </div>

    <!-- Charts -->
    <div class="charts-grid">
        <div class="chart-card">
            <div class="chart-title">المطالبات حسب السبب</div>
            @foreach($eotByCause as $cause)
            <div class="cause-chart-item">
                <span class="cause-name">
                    @php
                        $labels = [
                            'client_delay' => 'تأخير المالك',
                            'consultant_delay' => 'تأخير الاستشاري',
                            'variations' => 'أوامر تغييرية',
                            'unforeseeable_conditions' => 'ظروف غير منظورة',
                            'force_majeure' => 'قوة قاهرة',
                            'weather' => 'طقس استثنائي',
                            'delays_by_others' => 'تأخير الآخرين',
                            'suspension' => 'إيقاف الأعمال',
                            'late_drawings' => 'تأخر المخططات',
                            'late_approvals' => 'تأخر الموافقات',
                            'other' => 'أخرى',
                        ];
                    @endphp
                    {{ $labels[$cause->cause_category] ?? $cause->cause_category }}
                </span>
                <span class="cause-count">{{ $cause->count }}</span>
            </div>
            @endforeach
        </div>

        <div class="chart-card">
            <div class="chart-title">نسبة الموافقة</div>
            <div style="text-align: center; padding: 40px 0;">
                <div style="font-size: 3rem; font-weight: 700; color: #0071e3;">
                    {{ number_format($approvalRate, 1) }}%
                </div>
                <div style="color: #86868b; margin-top: 10px;">
                    من {{ number_format($requestedDays) }} يوم مطلوب
                </div>
                <div style="margin-top: 20px; display: flex; justify-content: center; gap: 20px;">
                    <div>
                        <div style="font-size: 1.5rem; font-weight: 600; color: #388e3c;">
                            {{ number_format($approvedDays) }}
                        </div>
                        <div style="font-size: 0.8rem; color: #86868b;">معتمد</div>
                    </div>
                    <div>
                        <div style="font-size: 1.5rem; font-weight: 600; color: #d32f2f;">
                            {{ number_format($requestedDays - $approvedDays) }}
                        </div>
                        <div style="font-size: 0.8rem; color: #86868b;">مرفوض/معلق</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Claims -->
    <div class="recent-claims">
        <h3 class="chart-title">المطالبات الأخيرة</h3>
        <table class="claims-table">
            <thead>
                <tr>
                    <th>رقم EOT</th>
                    <th>المشروع</th>
                    <th>السبب</th>
                    <th>الأيام المطلوبة</th>
                    <th>الأيام المعتمدة</th>
                    <th>الحالة</th>
                    <th>التاريخ</th>
                    <th>إجراءات</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentClaims as $claim)
                <tr>
                    <td><strong>{{ $claim->eot_number }}</strong></td>
                    <td>{{ $claim->project->name }}</td>
                    <td>{{ $claim->cause_category_label }}</td>
                    <td>{{ $claim->requested_days }} يوم</td>
                    <td>{{ $claim->approved_days ?? '-' }}</td>
                    <td>
                        <span class="status-badge status-{{ str_replace('_', '-', $claim->status) }}">
                            {{ $claim->status_badge }}
                        </span>
                    </td>
                    <td>{{ $claim->created_at->format('Y-m-d') }}</td>
                    <td>
                        <a href="{{ route('eot.show', $claim) }}" style="color: #0071e3; text-decoration: none;">
                            عرض
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" style="text-align: center; padding: 30px; color: #86868b;">
                        لا توجد مطالبات حتى الآن
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<script>
    lucide.createIcons();
</script>
@endsection
