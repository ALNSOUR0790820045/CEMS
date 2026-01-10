@extends('layouts.app')

@section('content')
<style>
    .pe-container {
        max-width: 1400px;
        margin: 0 auto;
    }
    
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
    
    .btn-primary {
        background: #0071e3;
        color: white;
        padding: 12px 24px;
        border-radius: 8px;
        text-decoration: none;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.2s;
        border: none;
        cursor: pointer;
    }
    
    .btn-primary:hover {
        background: #0077ed;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 113, 227, 0.3);
    }
    
    .contracts-grid {
        display: grid;
        gap: 20px;
    }
    
    .contract-card {
        background: white;
        border-radius: 12px;
        padding: 25px;
        box-shadow: 0 2px 12px rgba(0,0,0,0.06);
        transition: all 0.3s;
    }
    
    .contract-card:hover {
        box-shadow: 0 8px 24px rgba(0,0,0,0.12);
        transform: translateY(-2px);
    }
    
    .contract-header {
        display: flex;
        justify-content: space-between;
        align-items: start;
        margin-bottom: 20px;
    }
    
    .contract-title {
        font-size: 1.25rem;
        font-weight: 600;
        color: #1d1d1f;
        margin-bottom: 5px;
    }
    
    .contract-code {
        font-size: 0.9rem;
        color: #86868b;
    }
    
    .status-badge {
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 600;
    }
    
    .status-active {
        background: #d1f4dd;
        color: #047857;
    }
    
    .status-inactive {
        background: #fee;
        color: #dc2626;
    }
    
    .contract-details {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 15px;
        margin-bottom: 20px;
    }
    
    .detail-item {
        display: flex;
        flex-direction: column;
        gap: 5px;
    }
    
    .detail-label {
        font-size: 0.8rem;
        color: #86868b;
    }
    
    .detail-value {
        font-size: 1rem;
        font-weight: 600;
        color: #1d1d1f;
    }
    
    .contract-actions {
        display: flex;
        gap: 10px;
        padding-top: 20px;
        border-top: 1px solid #f5f5f7;
    }
    
    .btn-action {
        padding: 8px 16px;
        border-radius: 6px;
        font-size: 0.85rem;
        font-weight: 500;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: all 0.2s;
        border: none;
        cursor: pointer;
    }
    
    .btn-secondary {
        background: #f5f5f7;
        color: #1d1d1f;
    }
    
    .btn-secondary:hover {
        background: #e8e8ed;
    }
    
    .btn-danger {
        background: #ff3b30;
        color: white;
    }
    
    .btn-danger:hover {
        background: #ff453a;
    }
    
    .empty-state {
        text-align: center;
        padding: 60px 20px;
        background: white;
        border-radius: 12px;
    }
    
    .empty-state i {
        width: 64px;
        height: 64px;
        color: #86868b;
        margin-bottom: 20px;
    }
    
    .empty-state h3 {
        font-size: 1.5rem;
        color: #1d1d1f;
        margin-bottom: 10px;
    }
    
    .empty-state p {
        color: #86868b;
        margin-bottom: 30px;
    }
</style>

<div class="pe-container">
    <div class="page-header">
        <h1 class="page-title">عقود فروقات الأسعار</h1>
        <a href="{{ route('price-escalation.contract-setup') }}" class="btn-primary">
            <i data-lucide="plus" style="width: 18px; height: 18px;"></i>
            إنشاء عقد جديد
        </a>
    </div>
    
    @if(session('success'))
        <div style="background: #d1f4dd; color: #047857; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
            {{ session('success') }}
        </div>
    @endif
    
    @if($contracts->isEmpty())
        <div class="empty-state">
            <i data-lucide="file-text"></i>
            <h3>لا توجد عقود حتى الآن</h3>
            <p>ابدأ بإنشاء عقد فروقات أسعار جديد للمشاريع</p>
            <a href="{{ route('price-escalation.contract-setup') }}" class="btn-primary">
                <i data-lucide="plus" style="width: 18px; height: 18px;"></i>
                إنشاء العقد الأول
            </a>
        </div>
    @else
        <div class="contracts-grid">
            @foreach($contracts as $contract)
                <div class="contract-card">
                    <div class="contract-header">
                        <div>
                            <div class="contract-title">{{ $contract->project->name }}</div>
                            <div class="contract-code">{{ $contract->project->code }}</div>
                        </div>
                        <span class="status-badge {{ $contract->is_active ? 'status-active' : 'status-inactive' }}">
                            {{ $contract->is_active ? 'نشط' : 'غير نشط' }}
                        </span>
                    </div>
                    
                    <div class="contract-details">
                        <div class="detail-item">
                            <span class="detail-label">تاريخ العقد</span>
                            <span class="detail-value">{{ $contract->contract_date->format('Y-m-d') }}</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">قيمة العقد</span>
                            <span class="detail-value">{{ number_format($contract->contract_amount, 0) }} د.أ</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">نوع المعادلة</span>
                            <span class="detail-value">
                                @if($contract->formula_type === 'dsi') DSI
                                @elseif($contract->formula_type === 'fixed_percentage') نسبة ثابتة
                                @elseif($contract->formula_type === 'custom_indices') مؤشرات مخصصة
                                @else لا يوجد
                                @endif
                            </span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">النسب</span>
                            <span class="detail-value">
                                {{ $contract->materials_weight }}% مواد + {{ $contract->labor_weight }}% عمالة
                            </span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">عتبة التطبيق</span>
                            <span class="detail-value">{{ $contract->threshold_percentage }}%</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">التكرار</span>
                            <span class="detail-value">
                                @if($contract->calculation_frequency === 'monthly') شهري
                                @elseif($contract->calculation_frequency === 'quarterly') ربع سنوي
                                @elseif($contract->calculation_frequency === 'per_ipc') مع كل مستخلص
                                @else سنوي
                                @endif
                            </span>
                        </div>
                    </div>
                    
                    <div class="contract-actions">
                        <a href="{{ route('price-escalation.contract-setup', $contract->project_id) }}" class="btn-action btn-secondary">
                            <i data-lucide="edit" style="width: 16px; height: 16px;"></i>
                            تعديل
                        </a>
                        <a href="{{ route('price-escalation.calculate') }}?contract={{ $contract->id }}" class="btn-action btn-secondary">
                            <i data-lucide="calculator" style="width: 16px; height: 16px;"></i>
                            حساب فروقات
                        </a>
                        <form method="POST" action="{{ route('price-escalation.contract.destroy', $contract) }}" 
                              style="display: inline;" 
                              onsubmit="return confirm('هل أنت متأكد من حذف هذا العقد؟')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-action btn-danger">
                                <i data-lucide="trash-2" style="width: 16px; height: 16px;"></i>
                                حذف
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>

<script>
    lucide.createIcons();
</script>
@endsection
