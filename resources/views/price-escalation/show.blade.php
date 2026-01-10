@extends('layouts.app')

@section('content')
<style>
    .pe-container {
        max-width: 1200px;
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
    
    .status-badge {
        padding: 8px 16px;
        border-radius: 20px;
        font-size: 0.9rem;
        font-weight: 600;
    }
    
    .status-calculated {
        background: #e0e7ff;
        color: #4338ca;
    }
    
    .status-pending_approval {
        background: #fef3c7;
        color: #92400e;
    }
    
    .status-approved {
        background: #d1f4dd;
        color: #047857;
    }
    
    .status-paid {
        background: #cffafe;
        color: #0e7490;
    }
    
    .status-rejected {
        background: #fee;
        color: #dc2626;
    }
    
    .info-card {
        background: white;
        border-radius: 12px;
        padding: 30px;
        box-shadow: 0 2px 12px rgba(0,0,0,0.06);
        margin-bottom: 20px;
    }
    
    .section-title {
        font-size: 1.25rem;
        font-weight: 600;
        color: #1d1d1f;
        margin-bottom: 20px;
        padding-bottom: 10px;
        border-bottom: 2px solid #f5f5f7;
    }
    
    .info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
    }
    
    .info-item {
        display: flex;
        flex-direction: column;
        gap: 5px;
    }
    
    .info-label {
        font-size: 0.85rem;
        color: #86868b;
    }
    
    .info-value {
        font-size: 1.1rem;
        font-weight: 600;
        color: #1d1d1f;
    }
    
    .indices-section {
        background: #f5f5f7;
        border-radius: 12px;
        padding: 25px;
        margin-bottom: 20px;
    }
    
    .indices-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
    }
    
    .index-box {
        background: white;
        padding: 20px;
        border-radius: 8px;
    }
    
    .index-label {
        font-size: 0.85rem;
        color: #86868b;
        margin-bottom: 10px;
    }
    
    .index-value {
        font-size: 1.5rem;
        font-weight: 700;
        color: #1d1d1f;
        margin-bottom: 5px;
    }
    
    .index-change {
        font-size: 0.9rem;
        font-weight: 600;
    }
    
    .change-positive {
        color: #34c759;
    }
    
    .change-negative {
        color: #ff3b30;
    }
    
    .formula-card {
        background: linear-gradient(135deg, #0071e3, #00c4cc);
        color: white;
        border-radius: 12px;
        padding: 30px;
    }
    
    .formula-title {
        font-size: 1rem;
        opacity: 0.9;
        margin-bottom: 15px;
    }
    
    .formula-content {
        font-family: monospace;
        font-size: 1.1rem;
        background: rgba(255,255,255,0.1);
        padding: 15px;
        border-radius: 8px;
        margin-bottom: 20px;
        line-height: 1.8;
    }
    
    .result-section {
        margin-top: 20px;
        padding-top: 20px;
        border-top: 1px solid rgba(255,255,255,0.2);
    }
    
    .result-label {
        font-size: 1rem;
        opacity: 0.9;
        margin-bottom: 10px;
    }
    
    .result-value {
        font-size: 2.5rem;
        font-weight: 700;
    }
    
    .amounts-grid {
        display: grid;
        grid-template-columns: 1fr 1fr 1fr;
        gap: 20px;
        margin-top: 25px;
    }
    
    .amount-box {
        background: rgba(255,255,255,0.15);
        padding: 20px;
        border-radius: 8px;
        text-align: center;
    }
    
    .amount-label {
        font-size: 0.85rem;
        opacity: 0.9;
        margin-bottom: 10px;
    }
    
    .amount-value {
        font-size: 1.8rem;
        font-weight: 700;
    }
    
    .threshold-box {
        padding: 20px;
        border-radius: 12px;
        margin-top: 20px;
        display: flex;
        align-items: center;
        gap: 15px;
    }
    
    .threshold-met {
        background: #d1f4dd;
        color: #047857;
    }
    
    .threshold-not-met {
        background: #fff3cd;
        color: #856404;
    }
    
    .actions-card {
        background: white;
        border-radius: 12px;
        padding: 30px;
        box-shadow: 0 2px 12px rgba(0,0,0,0.06);
    }
    
    .btn-action {
        padding: 12px 24px;
        border-radius: 8px;
        font-weight: 600;
        font-size: 1rem;
        border: none;
        cursor: pointer;
        transition: all 0.2s;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        text-decoration: none;
        font-family: 'Cairo', sans-serif;
    }
    
    .btn-primary {
        background: #0071e3;
        color: white;
    }
    
    .btn-primary:hover {
        background: #0077ed;
    }
    
    .btn-success {
        background: #34c759;
        color: white;
    }
    
    .btn-success:hover {
        background: #30d158;
    }
    
    .btn-danger {
        background: #ff3b30;
        color: white;
    }
    
    .btn-danger:hover {
        background: #ff453a;
    }
    
    .btn-secondary {
        background: #f5f5f7;
        color: #1d1d1f;
    }
    
    .btn-secondary:hover {
        background: #e8e8ed;
    }
    
    .notes-section {
        margin-top: 20px;
        padding: 15px;
        background: #f5f5f7;
        border-radius: 8px;
    }
</style>

<div class="pe-container">
    <div class="page-header">
        <div>
            <h1 class="page-title">{{ $calculation->calculation_number }}</h1>
            <p style="color: #86868b; margin-top: 5px;">تفاصيل حساب فروقات الأسعار</p>
        </div>
        <span class="status-badge status-{{ $calculation->status }}">
            @if($calculation->status === 'calculated') محسوب
            @elseif($calculation->status === 'pending_approval') في الانتظار
            @elseif($calculation->status === 'approved') معتمد
            @elseif($calculation->status === 'paid') مدفوع
            @else مرفوض
            @endif
        </span>
    </div>
    
    @if(session('success'))
        <div style="background: #d1f4dd; color: #047857; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
            {{ session('success') }}
        </div>
    @endif
    
    <!-- معلومات أساسية -->
    <div class="info-card">
        <h2 class="section-title">معلومات الحساب</h2>
        <div class="info-grid">
            <div class="info-item">
                <span class="info-label">المشروع</span>
                <span class="info-value">{{ $calculation->contract->project->name }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">رمز المشروع</span>
                <span class="info-value">{{ $calculation->contract->project->code }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">تاريخ الحساب</span>
                <span class="info-value">{{ $calculation->calculation_date->format('Y-m-d') }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">الفترة</span>
                <span class="info-value">
                    {{ $calculation->period_from->format('Y-m-d') }} - {{ $calculation->period_to->format('Y-m-d') }}
                </span>
            </div>
            @if($calculation->ipc)
                <div class="info-item">
                    <span class="info-label">رقم المستخلص</span>
                    <span class="info-value">{{ $calculation->ipc->ipc_number }}</span>
                </div>
            @endif
        </div>
    </div>
    
    <!-- المؤشرات -->
    <div class="info-card">
        <h2 class="section-title">المؤشرات</h2>
        
        <div class="indices-section">
            <h4 style="margin-bottom: 20px; font-weight: 600;">المؤشرات المرجعية (Base):</h4>
            <div class="indices-grid">
                <div class="index-box">
                    <div class="index-label">مؤشر المواد (L₀)</div>
                    <div class="index-value">{{ number_format($calculation->base_materials_index, 4) }}</div>
                </div>
                <div class="index-box">
                    <div class="index-label">مؤشر العمالة (P₀)</div>
                    <div class="index-value">{{ number_format($calculation->base_labor_index, 4) }}</div>
                </div>
            </div>
            
            <h4 style="margin: 30px 0 20px; font-weight: 600;">المؤشرات الحالية (Current):</h4>
            <div class="indices-grid">
                <div class="index-box">
                    <div class="index-label">مؤشر المواد (L₁)</div>
                    <div class="index-value">{{ number_format($calculation->current_materials_index, 4) }}</div>
                    <div class="index-change {{ $calculation->materials_change_percent >= 0 ? 'change-positive' : 'change-negative' }}">
                        {{ $calculation->materials_change_percent > 0 ? '+' : '' }}{{ number_format($calculation->materials_change_percent, 2) }}%
                    </div>
                </div>
                <div class="index-box">
                    <div class="index-label">مؤشر العمالة (P₁)</div>
                    <div class="index-value">{{ number_format($calculation->current_labor_index, 4) }}</div>
                    <div class="index-change {{ $calculation->labor_change_percent >= 0 ? 'change-positive' : 'change-negative' }}">
                        {{ $calculation->labor_change_percent > 0 ? '+' : '' }}{{ number_format($calculation->labor_change_percent, 2) }}%
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- الحساب والنتيجة -->
    <div class="info-card">
        <h2 class="section-title">الحساب والنتيجة</h2>
        
        <div class="formula-card">
            <div class="formula-title">المعادلة:</div>
            <div class="formula-content">
                E = (A × ΔL) + (B × ΔP)<br>
                E = ({{ $calculation->contract->materials_weight }}% × {{ number_format($calculation->materials_change_percent, 2) }}%) + 
                    ({{ $calculation->contract->labor_weight }}% × {{ number_format($calculation->labor_change_percent, 2) }}%)<br>
                E = {{ number_format(($calculation->contract->materials_weight / 100) * $calculation->materials_change_percent, 2) }}% + 
                    {{ number_format(($calculation->contract->labor_weight / 100) * $calculation->labor_change_percent, 2) }}%<br>
                E = {{ number_format($calculation->escalation_percentage, 2) }}%
            </div>
            
            <div class="result-section">
                <div class="result-label">نسبة فروقات الأسعار:</div>
                <div class="result-value">{{ number_format($calculation->escalation_percentage, 2) }}%</div>
                
                <div class="amounts-grid">
                    <div class="amount-box">
                        <div class="amount-label">مبلغ المستخلص</div>
                        <div class="amount-value">{{ number_format($calculation->ipc_amount, 0) }}</div>
                        <div style="font-size: 0.9rem; margin-top: 5px;">د.أ</div>
                    </div>
                    <div class="amount-box">
                        <div class="amount-label">فروقات الأسعار</div>
                        <div class="amount-value">{{ number_format($calculation->escalation_amount, 0) }}</div>
                        <div style="font-size: 0.9rem; margin-top: 5px;">د.أ</div>
                    </div>
                    <div class="amount-box">
                        <div class="amount-label">الإجمالي</div>
                        <div class="amount-value">{{ number_format($calculation->ipc_amount + $calculation->escalation_amount, 0) }}</div>
                        <div style="font-size: 0.9rem; margin-top: 5px;">د.أ</div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="threshold-box {{ $calculation->threshold_met ? 'threshold-met' : 'threshold-not-met' }}">
            <i data-lucide="{{ $calculation->threshold_met ? 'check-circle' : 'alert-circle' }}" style="width: 32px; height: 32px;"></i>
            <div>
                <strong>{{ $calculation->threshold_met ? 'تجاوز العتبة' : 'لم يتجاوز العتبة' }}</strong><br>
                <span style="font-size: 0.9rem;">
                    العتبة المطلوبة: {{ $calculation->contract->threshold_percentage }}%
                    @if($calculation->threshold_met)
                        - سيتم تطبيق الفروقات ✅
                    @else
                        - لن يتم تطبيق الفروقات ⚠️
                    @endif
                </span>
            </div>
        </div>
        
        @if($calculation->notes)
            <div class="notes-section">
                <strong style="display: block; margin-bottom: 10px;">ملاحظات:</strong>
                {{ $calculation->notes }}
            </div>
        @endif
        
        @if($calculation->approved_by)
            <div style="margin-top: 20px; padding: 15px; background: #f5f5f7; border-radius: 8px;">
                <strong>{{ $calculation->status === 'approved' ? 'تم الاعتماد' : 'تم الرفض' }}</strong> بواسطة 
                {{ $calculation->approvedBy->name }} في {{ $calculation->approved_at->format('Y-m-d H:i') }}
            </div>
        @endif
    </div>
    
    <!-- الإجراءات -->
    @if(in_array($calculation->status, ['calculated', 'pending_approval']))
        <div class="actions-card">
            <h2 class="section-title">الإجراءات</h2>
            
            <div style="display: flex; gap: 15px; flex-wrap: wrap;">
                @if($calculation->status === 'calculated')
                    <form method="POST" action="{{ route('price-escalation.calculations.approve', $calculation) }}" style="display: inline;">
                        @csrf
                        <button type="submit" class="btn-action btn-success">
                            <i data-lucide="check" style="width: 18px; height: 18px;"></i>
                            اعتماد
                        </button>
                    </form>
                    
                    <button onclick="showRejectModal()" class="btn-action btn-danger">
                        <i data-lucide="x" style="width: 18px; height: 18px;"></i>
                        رفض
                    </button>
                @endif
                
                <form method="POST" action="{{ route('price-escalation.calculations.destroy', $calculation) }}" 
                      style="display: inline;" 
                      onsubmit="return confirm('هل أنت متأكد من حذف هذا الحساب؟')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn-action btn-danger">
                        <i data-lucide="trash-2" style="width: 18px; height: 18px;"></i>
                        حذف
                    </button>
                </form>
                
                <a href="{{ route('price-escalation.calculations') }}" class="btn-action btn-secondary">
                    <i data-lucide="arrow-right" style="width: 18px; height: 18px;"></i>
                    العودة للقائمة
                </a>
            </div>
        </div>
    @else
        <div style="text-align: center; margin-top: 20px;">
            <a href="{{ route('price-escalation.calculations') }}" class="btn-action btn-secondary">
                <i data-lucide="arrow-right" style="width: 18px; height: 18px;"></i>
                العودة للقائمة
            </a>
        </div>
    @endif
</div>

<!-- Reject Modal -->
<div id="rejectModal" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); z-index: 9999; align-items: center; justify-content: center;">
    <div style="background: white; border-radius: 12px; padding: 30px; max-width: 500px; width: 90%;">
        <h3 style="font-size: 1.5rem; font-weight: 700; margin-bottom: 20px;">رفض الحساب</h3>
        <form method="POST" action="{{ route('price-escalation.calculations.reject', $calculation) }}">
            @csrf
            <div style="margin-bottom: 20px;">
                <label style="display: block; font-weight: 600; margin-bottom: 8px;">سبب الرفض *</label>
                <textarea name="notes" rows="4" style="width: 100%; padding: 12px; border: 1px solid #d1d1d6; border-radius: 8px; font-family: 'Cairo', sans-serif;" required></textarea>
            </div>
            <div style="display: flex; gap: 10px; justify-content: flex-end;">
                <button type="button" onclick="closeRejectModal()" class="btn-action btn-secondary">إلغاء</button>
                <button type="submit" class="btn-action btn-danger">رفض</button>
            </div>
        </form>
    </div>
</div>

<script>
    lucide.createIcons();
    
    function showRejectModal() {
        document.getElementById('rejectModal').style.display = 'flex';
    }
    
    function closeRejectModal() {
        document.getElementById('rejectModal').style.display = 'none';
    }
    
    // Close modal on outside click
    document.getElementById('rejectModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeRejectModal();
        }
    });
</script>
@endsection
