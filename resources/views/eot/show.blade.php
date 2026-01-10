@extends('layouts.app')

@section('content')
<style>
    .eot-show {
        max-width: 1200px;
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
        align-items: start;
    }
    
    .header-left h1 {
        font-size: 1.8rem;
        font-weight: 700;
        margin: 0 0 5px 0;
        color: #1d1d1f;
    }
    
    .header-left .subtitle {
        color: #86868b;
        font-size: 0.9rem;
    }
    
    .status-badge {
        display: inline-block;
        padding: 8px 20px;
        border-radius: 20px;
        font-size: 0.9rem;
        font-weight: 600;
    }
    
    .status-draft { background: #f5f5f7; color: #86868b; }
    .status-submitted { background: #e3f2fd; color: #1976d2; }
    .status-approved { background: #e8f5e9; color: #388e3c; }
    .status-rejected { background: #ffebee; color: #d32f2f; }
    .status-under-review-consultant,
    .status-under-review-client { background: #fff3e0; color: #f57c00; }
    .status-partially-approved { background: #fff9c4; color: #f57f17; }
    .status-disputed { background: #fce4ec; color: #c2185b; }
    
    .content-grid {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 20px;
        margin-bottom: 20px;
    }
    
    .card {
        background: white;
        border-radius: 12px;
        padding: 25px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        margin-bottom: 20px;
    }
    
    .card-title {
        font-size: 1.2rem;
        font-weight: 600;
        margin-bottom: 20px;
        color: #1d1d1f;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .info-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
    }
    
    .info-item {
        margin-bottom: 15px;
    }
    
    .info-label {
        font-size: 0.8rem;
        color: #86868b;
        margin-bottom: 5px;
    }
    
    .info-value {
        font-size: 1rem;
        color: #1d1d1f;
        font-weight: 500;
    }
    
    .days-summary {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 15px;
        margin: 20px 0;
    }
    
    .days-card {
        text-align: center;
        padding: 20px;
        border-radius: 8px;
    }
    
    .days-card.requested {
        background: #e3f2fd;
    }
    
    .days-card.approved {
        background: #e8f5e9;
    }
    
    .days-card.rejected {
        background: #ffebee;
    }
    
    .days-number {
        font-size: 2rem;
        font-weight: 700;
        margin-bottom: 5px;
    }
    
    .days-label {
        font-size: 0.85rem;
        color: #86868b;
    }
    
    .btn {
        padding: 10px 20px;
        border-radius: 8px;
        font-size: 0.9rem;
        font-weight: 500;
        cursor: pointer;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        border: none;
        transition: all 0.2s;
    }
    
    .btn-primary {
        background: #0071e3;
        color: white;
    }
    
    .btn-primary:hover {
        background: #0077ed;
    }
    
    .btn-secondary {
        background: #6c757d;
        color: white;
    }
    
    .btn-success {
        background: #388e3c;
        color: white;
    }
    
    .btn-danger {
        background: #d32f2f;
        color: white;
    }
    
    .timeline {
        position: relative;
        padding-right: 30px;
    }
    
    .timeline-item {
        position: relative;
        padding-bottom: 20px;
    }
    
    .timeline-item::before {
        content: '';
        position: absolute;
        right: -30px;
        top: 0;
        width: 2px;
        height: 100%;
        background: #f5f5f7;
    }
    
    .timeline-item::after {
        content: '';
        position: absolute;
        right: -36px;
        top: 5px;
        width: 14px;
        height: 14px;
        border-radius: 50%;
        background: #0071e3;
        border: 3px solid white;
    }
    
    .timeline-item.completed::after {
        background: #388e3c;
    }
    
    .timeline-item.pending::after {
        background: #d1d1d6;
    }
    
    .timeline-title {
        font-weight: 600;
        margin-bottom: 5px;
    }
    
    .timeline-date {
        font-size: 0.8rem;
        color: #86868b;
    }
    
    .table {
        width: 100%;
        border-collapse: collapse;
    }
    
    .table th {
        background: #f5f5f7;
        padding: 10px;
        text-align: right;
        font-size: 0.85rem;
        font-weight: 600;
    }
    
    .table td {
        padding: 10px;
        border-bottom: 1px solid #f5f5f7;
    }
    
    .action-buttons {
        display: flex;
        gap: 10px;
        margin-top: 20px;
    }
</style>

<div class="eot-show">
    <div class="page-header">
        <div class="header-left">
            <h1>{{ $eotClaim->eot_number }}</h1>
            <p class="subtitle">{{ $eotClaim->project->name }}</p>
        </div>
        <div>
            <span class="status-badge status-{{ str_replace('_', '-', $eotClaim->status) }}">
                {{ $eotClaim->status_badge }}
            </span>
        </div>
    </div>

    <div class="content-grid">
        <!-- Main Content -->
        <div>
            <!-- معلومات المطالبة -->
            <div class="card">
                <h3 class="card-title">
                    <i data-lucide="info" style="width: 20px; height: 20px;"></i>
                    معلومات المطالبة
                </h3>
                
                <div class="info-grid">
                    <div class="info-item">
                        <div class="info-label">تاريخ المطالبة</div>
                        <div class="info-value">{{ $eotClaim->claim_date->format('Y-m-d') }}</div>
                    </div>
                    
                    <div class="info-item">
                        <div class="info-label">السبب</div>
                        <div class="info-value">{{ $eotClaim->cause_category_label }}</div>
                    </div>
                    
                    <div class="info-item">
                        <div class="info-label">المادة القانونية (FIDIC)</div>
                        <div class="info-value">{{ $eotClaim->fidic_clause_reference ?? '-' }}</div>
                    </div>
                    
                    <div class="info-item">
                        <div class="info-label">المسار الحرج</div>
                        <div class="info-value">
                            @if($eotClaim->affects_critical_path)
                                <span style="color: #d32f2f;">✓ نعم</span>
                            @else
                                <span style="color: #86868b;">لا</span>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="info-item" style="margin-top: 20px;">
                    <div class="info-label">وصف الحدث</div>
                    <div class="info-value">{{ $eotClaim->event_description }}</div>
                </div>

                <div class="info-item">
                    <div class="info-label">التأثير</div>
                    <div class="info-value">{{ $eotClaim->impact_description }}</div>
                </div>

                <div class="info-item">
                    <div class="info-label">المبررات القانونية</div>
                    <div class="info-value">{{ $eotClaim->justification }}</div>
                </div>
            </div>

            <!-- الأيام -->
            <div class="card">
                <h3 class="card-title">
                    <i data-lucide="calendar" style="width: 20px; height: 20px;"></i>
                    ملخص الأيام
                </h3>
                
                <div class="days-summary">
                    <div class="days-card requested">
                        <div class="days-number" style="color: #1976d2;">{{ $eotClaim->requested_days }}</div>
                        <div class="days-label">المطلوب</div>
                    </div>
                    
                    <div class="days-card approved">
                        <div class="days-number" style="color: #388e3c;">
                            {{ $eotClaim->approved_days ?? '-' }}
                        </div>
                        <div class="days-label">المعتمد</div>
                    </div>
                    
                    <div class="days-card rejected">
                        <div class="days-number" style="color: #d32f2f;">
                            {{ $eotClaim->rejected_days ?? '-' }}
                        </div>
                        <div class="days-label">المرفوض</div>
                    </div>
                </div>

                <div class="info-grid" style="margin-top: 20px;">
                    <div class="info-item">
                        <div class="info-label">تاريخ الإنجاز الأصلي</div>
                        <div class="info-value">{{ $eotClaim->original_completion_date->format('Y-m-d') }}</div>
                    </div>
                    
                    <div class="info-item">
                        <div class="info-label">تاريخ الإنجاز الحالي</div>
                        <div class="info-value">{{ $eotClaim->current_completion_date->format('Y-m-d') }}</div>
                    </div>
                    
                    @if($eotClaim->approved_days)
                    <div class="info-item">
                        <div class="info-label">تاريخ الإنجاز الجديد المعتمد</div>
                        <div class="info-value" style="color: #388e3c; font-weight: 700;">
                            {{ $eotClaim->approved_new_completion_date?->format('Y-m-d') ?? '-' }}
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- التكاليف -->
            @if($eotClaim->has_prolongation_costs)
            <div class="card">
                <h3 class="card-title">
                    <i data-lucide="dollar-sign" style="width: 20px; height: 20px;"></i>
                    تكاليف الإطالة
                </h3>
                
                @if($eotClaim->prolongationCostItems->count() > 0)
                <table class="table">
                    <thead>
                        <tr>
                            <th>الفئة</th>
                            <th>الوصف</th>
                            <th>المدة</th>
                            <th>التكلفة اليومية</th>
                            <th>الإجمالي</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($eotClaim->prolongationCostItems as $item)
                        <tr>
                            <td>{{ $item->cost_category_label }}</td>
                            <td>{{ $item->description }}</td>
                            <td>{{ $item->duration_days }} يوم</td>
                            <td>{{ number_format($item->daily_rate, 2) }} د.أ</td>
                            <td><strong>{{ number_format($item->total_cost, 2) }} د.أ</strong></td>
                        </tr>
                        @endforeach
                        <tr style="background: #f5f5f7; font-weight: 700;">
                            <td colspan="4" style="text-align: left;">الإجمالي:</td>
                            <td>{{ number_format($eotClaim->total_prolongation_cost, 2) }} د.أ</td>
                        </tr>
                    </tbody>
                </table>
                @else
                <p style="color: #86868b; text-align: center; padding: 20px;">لا توجد بنود تكاليف مضافة</p>
                @endif
            </div>
            @endif

            <!-- الأنشطة المتأثرة -->
            @if($eotClaim->affectedActivities->count() > 0)
            <div class="card">
                <h3 class="card-title">
                    <i data-lucide="list" style="width: 20px; height: 20px;"></i>
                    الأنشطة المتأثرة
                </h3>
                
                <table class="table">
                    <thead>
                        <tr>
                            <th>النشاط</th>
                            <th>التاريخ الأصلي</th>
                            <th>التاريخ المنقح</th>
                            <th>التأخير</th>
                            <th>حرج؟</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($eotClaim->affectedActivities as $affected)
                        <tr>
                            <td>{{ $affected->activity->activity_name }}</td>
                            <td>{{ $affected->original_end_date->format('Y-m-d') }}</td>
                            <td>{{ $affected->revised_end_date->format('Y-m-d') }}</td>
                            <td>{{ $affected->delay_days }} يوم</td>
                            <td>
                                @if($affected->on_critical_path)
                                    <span style="color: #d32f2f;">✓</span>
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div>
            <!-- سلسلة الموافقات -->
            <div class="card">
                <h3 class="card-title">
                    <i data-lucide="check-circle" style="width: 20px; height: 20px;"></i>
                    سلسلة الموافقات
                </h3>
                
                <div class="timeline">
                    <div class="timeline-item completed">
                        <div class="timeline-title">✓ مهندس المشروع</div>
                        <div class="timeline-date">
                            {{ $eotClaim->preparedBy->name }}<br>
                            {{ $eotClaim->created_at->format('Y-m-d H:i') }}
                        </div>
                    </div>
                    
                    <div class="timeline-item {{ $eotClaim->consultant_reviewed_by ? 'completed' : 'pending' }}">
                        <div class="timeline-title">
                            {{ $eotClaim->consultant_reviewed_by ? '✓' : '⏳' }} الاستشاري
                        </div>
                        <div class="timeline-date">
                            @if($eotClaim->consultant_reviewed_by)
                                {{ $eotClaim->consultantReviewedBy->name }}<br>
                                {{ $eotClaim->consultant_reviewed_at->format('Y-m-d H:i') }}
                            @else
                                قيد المراجعة
                            @endif
                        </div>
                        @if($eotClaim->consultant_comments)
                        <div style="margin-top: 10px; padding: 10px; background: #f5f5f7; border-radius: 6px; font-size: 0.85rem;">
                            {{ $eotClaim->consultant_comments }}
                        </div>
                        @endif
                    </div>
                    
                    <div class="timeline-item {{ $eotClaim->client_approved_by ? 'completed' : 'pending' }}">
                        <div class="timeline-title">
                            {{ $eotClaim->client_approved_by ? '✓' : '⏳' }} العميل
                        </div>
                        <div class="timeline-date">
                            @if($eotClaim->client_approved_by)
                                {{ $eotClaim->clientApprovedBy->name }}<br>
                                {{ $eotClaim->client_approved_at->format('Y-m-d H:i') }}
                            @else
                                معلق
                            @endif
                        </div>
                        @if($eotClaim->client_comments)
                        <div style="margin-top: 10px; padding: 10px; background: #f5f5f7; border-radius: 6px; font-size: 0.85rem;">
                            {{ $eotClaim->client_comments }}
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- إجراءات -->
            <div class="card">
                <h3 class="card-title">
                    <i data-lucide="settings" style="width: 20px; height: 20px;"></i>
                    إجراءات
                </h3>
                
                <div class="action-buttons">
                    <a href="{{ route('eot.index') }}" class="btn btn-secondary">
                        <i data-lucide="arrow-right" style="width: 16px; height: 16px;"></i>
                        رجوع
                    </a>
                    
                    @if($eotClaim->status === 'draft')
                        <a href="{{ route('eot.edit', $eotClaim) }}" class="btn btn-primary">
                            <i data-lucide="edit" style="width: 16px; height: 16px;"></i>
                            تعديل
                        </a>
                        
                        <form method="POST" action="{{ route('eot.submit', $eotClaim) }}" style="display: inline;">
                            @csrf
                            <button type="submit" class="btn btn-success">
                                <i data-lucide="send" style="width: 16px; height: 16px;"></i>
                                تقديم
                            </button>
                        </form>
                    @endif
                    
                    @if(in_array($eotClaim->status, ['submitted', 'under_review_consultant', 'under_review_client']))
                        <a href="{{ route('eot.approval-form', $eotClaim) }}" class="btn btn-primary">
                            <i data-lucide="check" style="width: 16px; height: 16px;"></i>
                            مراجعة
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    lucide.createIcons();
</script>
@endsection
