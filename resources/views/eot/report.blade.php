@extends('layouts.app')

@section('content')
<style>
    .eot-report {
        max-width: 1400px;
        margin: 0 auto;
    }
    
    .page-header {
        background: white;
        border-radius: 12px;
        padding: 30px;
        margin-bottom: 30px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .page-header h1 {
        font-size: 1.8rem;
        font-weight: 700;
        margin: 0;
        color: #1d1d1f;
    }
    
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }
    
    .stat-card {
        background: white;
        border-radius: 12px;
        padding: 25px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    }
    
    .stat-label {
        font-size: 0.85rem;
        color: #86868b;
        margin-bottom: 10px;
    }
    
    .stat-value {
        font-size: 2rem;
        font-weight: 700;
        color: #1d1d1f;
    }
    
    .card {
        background: white;
        border-radius: 12px;
        padding: 30px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        margin-bottom: 20px;
    }
    
    .card-title {
        font-size: 1.3rem;
        font-weight: 600;
        margin-bottom: 25px;
        color: #1d1d1f;
    }
    
    .report-table {
        width: 100%;
        border-collapse: collapse;
    }
    
    .report-table th {
        background: #f5f5f7;
        padding: 12px;
        text-align: right;
        font-size: 0.85rem;
        font-weight: 600;
        color: #1d1d1f;
        border-bottom: 2px solid #e5e5e7;
    }
    
    .report-table td {
        padding: 12px;
        border-bottom: 1px solid #f5f5f7;
        font-size: 0.9rem;
    }
    
    .report-table tr:hover {
        background: #f9f9fb;
    }
    
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
        display: inline-flex;
        align-items: center;
        gap: 8px;
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
        background: #0071e3;
        transition: width 0.3s;
    }
    
    .cause-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 15px 0;
        border-bottom: 1px solid #f5f5f7;
    }
    
    .cause-name {
        font-weight: 500;
    }
    
    .cause-stats {
        display: flex;
        gap: 30px;
        font-size: 0.9rem;
    }
</style>

<div class="eot-report">
    <div class="page-header">
        <h1>تقرير EOT الشامل</h1>
        <div>
            <a href="{{ route('eot.dashboard') }}" class="btn-primary">
                <i data-lucide="layout-dashboard" style="width: 16px; height: 16px;"></i>
                لوحة التحكم
            </a>
        </div>
    </div>

    <!-- الإحصائيات الرئيسية -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-label">إجمالي المطالبات</div>
            <div class="stat-value">{{ $statistics['total_claims'] }}</div>
        </div>
        
        <div class="stat-card">
            <div class="stat-label">الأيام المطلوبة</div>
            <div class="stat-value" style="color: #1976d2;">{{ number_format($statistics['requested_days']) }}</div>
        </div>
        
        <div class="stat-card">
            <div class="stat-label">الأيام المعتمدة</div>
            <div class="stat-value" style="color: #388e3c;">{{ number_format($statistics['approved_days']) }}</div>
        </div>
        
        <div class="stat-card">
            <div class="stat-label">الأيام المرفوضة</div>
            <div class="stat-value" style="color: #d32f2f;">{{ number_format($statistics['rejected_days']) }}</div>
        </div>
        
        <div class="stat-card">
            <div class="stat-label">إجمالي التكاليف</div>
            <div class="stat-value" style="color: #f57c00; font-size: 1.5rem;">
                {{ number_format($statistics['total_costs'], 0) }} د.أ
            </div>
        </div>
    </div>

    <!-- المطالبات حسب السبب -->
    <div class="card">
        <h3 class="card-title">التحليل حسب السبب</h3>
        
        @foreach($byCause as $category => $data)
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
        <div class="cause-row">
            <div>
                <div class="cause-name">{{ $labels[$category] ?? $category }}</div>
                <div class="progress-bar" style="width: 200px;">
                    <div class="progress-fill" style="width: {{ $data['approval_rate'] }}%;"></div>
                </div>
            </div>
            <div class="cause-stats">
                <div>
                    <strong>{{ $data['count'] }}</strong>
                    <span style="color: #86868b; font-size: 0.8rem;">مطالبة</span>
                </div>
                <div>
                    <strong style="color: #1976d2;">{{ $data['requested_days'] }}</strong>
                    <span style="color: #86868b; font-size: 0.8rem;">مطلوب</span>
                </div>
                <div>
                    <strong style="color: #388e3c;">{{ $data['approved_days'] }}</strong>
                    <span style="color: #86868b; font-size: 0.8rem;">معتمد</span>
                </div>
                <div>
                    <strong style="color: #0071e3;">{{ number_format($data['approval_rate'], 1) }}%</strong>
                    <span style="color: #86868b; font-size: 0.8rem;">نسبة</span>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- المطالبات حسب الحالة -->
    <div class="card">
        <h3 class="card-title">التوزيع حسب الحالة</h3>
        
        @php
            $statusLabels = [
                'draft' => 'مسودة',
                'submitted' => 'مقدم',
                'under_review_consultant' => 'قيد مراجعة الاستشاري',
                'under_review_client' => 'قيد مراجعة العميل',
                'partially_approved' => 'موافقة جزئية',
                'approved' => 'معتمد',
                'rejected' => 'مرفوض',
                'disputed' => 'متنازع عليه',
            ];
        @endphp
        
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 20px;">
            @foreach($byStatus as $status => $count)
            <div style="text-align: center; padding: 20px; background: #f5f5f7; border-radius: 8px;">
                <div style="font-size: 2rem; font-weight: 700; color: #0071e3;">{{ $count }}</div>
                <div style="font-size: 0.85rem; color: #86868b; margin-top: 5px;">
                    {{ $statusLabels[$status] ?? $status }}
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <!-- جدول المطالبات التفصيلي -->
    <div class="card">
        <h3 class="card-title">جدول المطالبات التفصيلي</h3>
        
        <table class="report-table">
            <thead>
                <tr>
                    <th>رقم EOT</th>
                    <th>المشروع</th>
                    <th>التاريخ</th>
                    <th>السبب</th>
                    <th>المطلوب</th>
                    <th>المعتمد</th>
                    <th>المرفوض</th>
                    <th>النسبة</th>
                    <th>التكاليف</th>
                    <th>الحالة</th>
                </tr>
            </thead>
            <tbody>
                @foreach($eotClaims as $claim)
                <tr>
                    <td><strong>{{ $claim->eot_number }}</strong></td>
                    <td>{{ $claim->project->name }}</td>
                    <td>{{ $claim->claim_date->format('Y-m-d') }}</td>
                    <td>{{ $claim->cause_category_label }}</td>
                    <td>{{ $claim->requested_days }}</td>
                    <td style="color: #388e3c; font-weight: 600;">
                        {{ $claim->approved_days ?? '-' }}
                    </td>
                    <td style="color: #d32f2f;">
                        {{ $claim->rejected_days ?? '-' }}
                    </td>
                    <td>
                        @if($claim->approved_days && $claim->requested_days)
                            {{ number_format(($claim->approved_days / $claim->requested_days) * 100, 1) }}%
                        @else
                            -
                        @endif
                    </td>
                    <td>
                        @if($claim->has_prolongation_costs)
                            {{ number_format($claim->total_prolongation_cost, 0) }} د.أ
                        @else
                            -
                        @endif
                    </td>
                    <td>{{ $claim->status_badge }}</td>
                </tr>
                @endforeach
                
                @if($eotClaims->count() > 0)
                <tr style="background: #f5f5f7; font-weight: 700;">
                    <td colspan="4">الإجمالي</td>
                    <td>{{ $statistics['requested_days'] }}</td>
                    <td style="color: #388e3c;">{{ $statistics['approved_days'] }}</td>
                    <td style="color: #d32f2f;">{{ $statistics['rejected_days'] }}</td>
                    <td>
                        @if($statistics['requested_days'] > 0)
                            {{ number_format(($statistics['approved_days'] / $statistics['requested_days']) * 100, 1) }}%
                        @else
                            -
                        @endif
                    </td>
                    <td>{{ number_format($statistics['total_costs'], 0) }} د.أ</td>
                    <td></td>
                </tr>
                @endif
            </tbody>
        </table>
    </div>
</div>

<script>
    lucide.createIcons();
</script>
@endsection
