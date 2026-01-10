@extends('layouts.app')

@section('content')
<style>
    .eot-index {
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
    
    .btn-primary:hover {
        background: #0077ed;
        transform: translateY(-2px);
    }
    
    .claims-container {
        background: white;
        border-radius: 12px;
        padding: 25px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    }
    
    .claims-table {
        width: 100%;
        border-collapse: collapse;
    }
    
    .claims-table th {
        background: #f5f5f7;
        padding: 15px 12px;
        text-align: right;
        font-size: 0.85rem;
        font-weight: 600;
        color: #1d1d1f;
        border-bottom: 2px solid #e5e5e7;
    }
    
    .claims-table td {
        padding: 15px 12px;
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
    .status-under-review-consultant,
    .status-under-review-client { background: #fff3e0; color: #f57c00; }
    .status-partially-approved { background: #fff9c4; color: #f57f17; }
    .status-disputed { background: #fce4ec; color: #c2185b; }
    
    .action-links {
        display: flex;
        gap: 12px;
    }
    
    .action-links a {
        color: #0071e3;
        text-decoration: none;
        font-size: 0.85rem;
        transition: color 0.2s;
    }
    
    .action-links a:hover {
        color: #0077ed;
        text-decoration: underline;
    }
    
    .pagination {
        margin-top: 25px;
        display: flex;
        justify-content: center;
        gap: 5px;
    }
    
    .pagination a,
    .pagination span {
        padding: 8px 12px;
        border-radius: 6px;
        font-size: 0.85rem;
        text-decoration: none;
        color: #1d1d1f;
        border: 1px solid #e5e5e7;
    }
    
    .pagination a:hover {
        background: #f5f5f7;
    }
    
    .pagination .active {
        background: #0071e3;
        color: white;
        border-color: #0071e3;
    }
    
    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: #86868b;
    }
    
    .empty-state-icon {
        width: 80px;
        height: 80px;
        margin: 0 auto 20px;
        color: #d1d1d6;
    }
    
    .empty-state h3 {
        font-size: 1.2rem;
        margin-bottom: 10px;
        color: #1d1d1f;
    }
    
    .empty-state p {
        margin-bottom: 20px;
    }
</style>

<div class="eot-index">
    <div class="page-header">
        <h1>مطالبات EOT والإطالة</h1>
        <div style="display: flex; gap: 10px;">
            <a href="{{ route('eot.dashboard') }}" class="btn-primary" style="background: #6c757d;">
                <i data-lucide="layout-dashboard" style="width: 16px; height: 16px;"></i>
                لوحة التحكم
            </a>
            <a href="{{ route('eot.create') }}" class="btn-primary">
                <i data-lucide="plus" style="width: 16px; height: 16px;"></i>
                مطالبة جديدة
            </a>
        </div>
    </div>

    <div class="claims-container">
        @if($eotClaims->count() > 0)
        <table class="claims-table">
            <thead>
                <tr>
                    <th>رقم EOT</th>
                    <th>المشروع</th>
                    <th>تاريخ المطالبة</th>
                    <th>السبب</th>
                    <th>الأيام المطلوبة</th>
                    <th>الأيام المعتمدة</th>
                    <th>التكاليف</th>
                    <th>الحالة</th>
                    <th>إجراءات</th>
                </tr>
            </thead>
            <tbody>
                @foreach($eotClaims as $claim)
                <tr>
                    <td><strong>{{ $claim->eot_number }}</strong></td>
                    <td>{{ $claim->project->name }}</td>
                    <td>{{ $claim->claim_date->format('Y-m-d') }}</td>
                    <td>{{ $claim->cause_category_label }}</td>
                    <td>{{ $claim->requested_days }} يوم</td>
                    <td>
                        @if($claim->approved_days)
                            <span style="color: #388e3c; font-weight: 600;">
                                {{ $claim->approved_days }} يوم
                            </span>
                        @else
                            <span style="color: #86868b;">-</span>
                        @endif
                    </td>
                    <td>
                        @if($claim->has_prolongation_costs)
                            {{ number_format($claim->total_prolongation_cost, 0) }} د.أ
                        @else
                            <span style="color: #86868b;">لا يوجد</span>
                        @endif
                    </td>
                    <td>
                        <span class="status-badge status-{{ str_replace('_', '-', $claim->status) }}">
                            {{ $claim->status_badge }}
                        </span>
                    </td>
                    <td>
                        <div class="action-links">
                            <a href="{{ route('eot.show', $claim) }}">عرض</a>
                            @if($claim->status === 'draft')
                                <a href="{{ route('eot.edit', $claim) }}">تعديل</a>
                                <form method="POST" action="{{ route('eot.submit', $claim) }}" style="display: inline;">
                                    @csrf
                                    <button type="submit" style="background: none; border: none; color: #0071e3; cursor: pointer; font-size: 0.85rem; padding: 0;">
                                        تقديم
                                    </button>
                                </form>
                            @endif
                            @if(in_array($claim->status, ['submitted', 'under_review_consultant', 'under_review_client']))
                                <a href="{{ route('eot.approval-form', $claim) }}">مراجعة</a>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="pagination">
            {{ $eotClaims->links() }}
        </div>
        @else
        <div class="empty-state">
            <div class="empty-state-icon">
                <i data-lucide="file-text" style="width: 100%; height: 100%;"></i>
            </div>
            <h3>لا توجد مطالبات EOT</h3>
            <p>ابدأ بإنشاء أول مطالبة لتمديد الوقت</p>
            <a href="{{ route('eot.create') }}" class="btn-primary">
                <i data-lucide="plus" style="width: 16px; height: 16px;"></i>
                إنشاء مطالبة جديدة
            </a>
        </div>
        @endif
    </div>
</div>

<script>
    lucide.createIcons();
</script>
@endsection
