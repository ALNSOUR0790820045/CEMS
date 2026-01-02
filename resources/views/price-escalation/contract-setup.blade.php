@extends('layouts.app')

@section('content')
<style>
    .pe-container {
        max-width: 1000px;
        margin: 0 auto;
    }
    
    .page-header {
        margin-bottom: 30px;
    }
    
    .page-title {
        font-size: 1.8rem;
        font-weight: 700;
        color: #1d1d1f;
        margin-bottom: 10px;
    }
    
    .page-subtitle {
        font-size: 1rem;
        color: #86868b;
    }
    
    .form-card {
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
    
    .form-group {
        margin-bottom: 20px;
    }
    
    .form-label {
        display: block;
        font-size: 0.9rem;
        font-weight: 600;
        color: #1d1d1f;
        margin-bottom: 8px;
    }
    
    .form-control {
        width: 100%;
        padding: 12px 16px;
        border: 1px solid #d1d1d6;
        border-radius: 8px;
        font-size: 1rem;
        font-family: 'Cairo', sans-serif;
        transition: all 0.2s;
    }
    
    .form-control:focus {
        outline: none;
        border-color: #0071e3;
        box-shadow: 0 0 0 3px rgba(0, 113, 227, 0.1);
    }
    
    .form-row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
    }
    
    .weights-input {
        display: grid;
        grid-template-columns: 1fr auto 1fr auto 1fr auto 1fr;
        align-items: center;
        gap: 10px;
    }
    
    .weight-input {
        position: relative;
    }
    
    .weight-input input {
        padding-left: 40px;
    }
    
    .weight-suffix {
        position: absolute;
        left: 16px;
        top: 50%;
        transform: translateY(-50%);
        color: #86868b;
        font-weight: 600;
    }
    
    .weight-label {
        font-size: 0.85rem;
        color: #86868b;
        text-align: center;
    }
    
    .total-indicator {
        padding: 15px;
        border-radius: 8px;
        font-size: 1.1rem;
        font-weight: 600;
        text-align: center;
        margin-top: 10px;
    }
    
    .total-valid {
        background: #d1f4dd;
        color: #047857;
    }
    
    .total-invalid {
        background: #fee;
        color: #dc2626;
    }
    
    .base-indices {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
        padding: 20px;
        background: #f5f5f7;
        border-radius: 8px;
    }
    
    .index-display {
        text-align: center;
    }
    
    .index-label {
        font-size: 0.85rem;
        color: #86868b;
        margin-bottom: 8px;
    }
    
    .index-value {
        font-size: 1.5rem;
        font-weight: 700;
        color: #0071e3;
    }
    
    .help-text {
        font-size: 0.85rem;
        color: #86868b;
        margin-top: 5px;
    }
    
    .btn-primary {
        background: #0071e3;
        color: white;
        padding: 14px 30px;
        border-radius: 8px;
        font-weight: 600;
        font-size: 1rem;
        border: none;
        cursor: pointer;
        transition: all 0.2s;
        font-family: 'Cairo', sans-serif;
    }
    
    .btn-primary:hover {
        background: #0077ed;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 113, 227, 0.3);
    }
    
    .btn-secondary {
        background: #f5f5f7;
        color: #1d1d1f;
        padding: 14px 30px;
        border-radius: 8px;
        font-weight: 600;
        font-size: 1rem;
        border: none;
        cursor: pointer;
        transition: all 0.2s;
        text-decoration: none;
        display: inline-block;
        font-family: 'Cairo', sans-serif;
    }
    
    .btn-secondary:hover {
        background: #e8e8ed;
    }
    
    .form-actions {
        display: flex;
        gap: 15px;
        justify-content: flex-end;
        margin-top: 30px;
    }
    
    .error-message {
        background: #fee;
        color: #dc2626;
        padding: 15px;
        border-radius: 8px;
        margin-bottom: 20px;
    }
</style>

<div class="pe-container">
    <div class="page-header">
        <h1 class="page-title">{{ $contract ? 'تعديل' : 'إنشاء' }} عقد فروقات الأسعار</h1>
        <p class="page-subtitle">إعداد معادلة حساب فروقات الأسعار للمشروع</p>
    </div>
    
    @if($errors->any())
        <div class="error-message">
            <strong>خطأ في البيانات:</strong>
            <ul style="margin: 10px 0 0 20px;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    
    <form method="POST" action="{{ $contract ? route('price-escalation.contract.update', $contract) : route('price-escalation.contract.store') }}">
        @csrf
        @if($contract)
            @method('PUT')
        @endif
        
        <!-- 1. معلومات العقد -->
        <div class="form-card">
            <h2 class="section-title">1. معلومات العقد</h2>
            
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">المشروع *</label>
                    <select name="project_id" class="form-control" required {{ $contract ? 'disabled' : '' }}>
                        <option value="">اختر المشروع</option>
                        @foreach($projects as $project)
                            <option value="{{ $project->id }}" {{ old('project_id', $contract?->project_id) == $project->id ? 'selected' : '' }}>
                                {{ $project->name }} ({{ $project->code }})
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="form-group">
                    <label class="form-label">تاريخ العقد (المرجعي) *</label>
                    <input type="date" name="contract_date" class="form-control" 
                           value="{{ old('contract_date', $contract?->contract_date?->format('Y-m-d')) }}" 
                           required id="contractDate">
                </div>
                
                <div class="form-group">
                    <label class="form-label">قيمة العقد (د.أ) *</label>
                    <input type="number" step="0.01" name="contract_amount" class="form-control" 
                           value="{{ old('contract_amount', $contract?->contract_amount) }}" 
                           required>
                </div>
            </div>
        </div>
        
        <!-- 2. معادلة الفروقات -->
        <div class="form-card">
            <h2 class="section-title">2. معادلة الفروقات</h2>
            
            <div class="form-group">
                <label class="form-label">نوع المعادلة *</label>
                <select name="formula_type" class="form-control" required>
                    <option value="dsi" {{ old('formula_type', $contract?->formula_type ?? 'dsi') == 'dsi' ? 'selected' : '' }}>
                        DSI - دائرة الإحصاءات العامة (الأردن)
                    </option>
                    <option value="fixed_percentage" {{ old('formula_type', $contract?->formula_type) == 'fixed_percentage' ? 'selected' : '' }}>
                        نسبة ثابتة
                    </option>
                    <option value="custom_indices" {{ old('formula_type', $contract?->formula_type) == 'custom_indices' ? 'selected' : '' }}>
                        مؤشرات مخصصة
                    </option>
                    <option value="none" {{ old('formula_type', $contract?->formula_type) == 'none' ? 'selected' : '' }}>
                        لا يوجد
                    </option>
                </select>
            </div>
            
            <div class="form-group">
                <label class="form-label">النسب *</label>
                <div class="weights-input">
                    <div class="weight-input">
                        <input type="number" step="0.01" name="materials_weight" class="form-control" 
                               value="{{ old('materials_weight', $contract?->materials_weight ?? 70) }}" 
                               required id="materialsWeight">
                        <span class="weight-suffix">%</span>
                    </div>
                    <span class="weight-label">المواد (A)</span>
                    
                    <div class="weight-input">
                        <input type="number" step="0.01" name="labor_weight" class="form-control" 
                               value="{{ old('labor_weight', $contract?->labor_weight ?? 30) }}" 
                               required id="laborWeight">
                        <span class="weight-suffix">%</span>
                    </div>
                    <span class="weight-label">العمالة (B)</span>
                    
                    <div class="weight-input">
                        <input type="number" step="0.01" name="fixed_portion" class="form-control" 
                               value="{{ old('fixed_portion', $contract?->fixed_portion ?? 0) }}" 
                               required id="fixedPortion">
                        <span class="weight-suffix">%</span>
                    </div>
                    <span class="weight-label">الثابت (C)</span>
                </div>
                <div class="total-indicator" id="totalIndicator">
                    الإجمالي: <span id="totalValue">100</span>% <span id="totalStatus">✅</span>
                </div>
                <p class="help-text">يجب أن يكون مجموع النسب = 100%</p>
            </div>
        </div>
        
        <!-- 3. المؤشرات المرجعية -->
        <div class="form-card">
            <h2 class="section-title">3. المؤشرات المرجعية</h2>
            
            <div class="base-indices" id="baseIndices">
                <div class="index-display">
                    <div class="index-label">مؤشر المواد (L₀)</div>
                    <div class="index-value" id="baseMaterialsIndex">--</div>
                </div>
                <div class="index-display">
                    <div class="index-label">مؤشر العمالة (P₀)</div>
                    <div class="index-value" id="baseLaborIndex">--</div>
                </div>
            </div>
            <p class="help-text">يتم جلب المؤشرات تلقائياً من DSI بناءً على تاريخ العقد</p>
        </div>
        
        <!-- 4. الحدود -->
        <div class="form-card">
            <h2 class="section-title">4. الحدود</h2>
            
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">العتبة (Threshold) % *</label>
                    <input type="number" step="0.01" name="threshold_percentage" class="form-control" 
                           value="{{ old('threshold_percentage', $contract?->threshold_percentage ?? 5) }}" 
                           required>
                    <p class="help-text">لا تطبق فروقات إذا كانت أقل من هذه النسبة</p>
                </div>
                
                <div class="form-group">
                    <label class="form-label">السقف (Maximum) % (اختياري)</label>
                    <input type="number" step="0.01" name="max_escalation_percentage" class="form-control" 
                           value="{{ old('max_escalation_percentage', $contract?->max_escalation_percentage) }}">
                    <p class="help-text">الحد الأقصى لنسبة الفروقات</p>
                </div>
            </div>
        </div>
        
        <!-- 5. التكرار -->
        <div class="form-card">
            <h2 class="section-title">5. التكرار</h2>
            
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">تكرار الحساب *</label>
                    <select name="calculation_frequency" class="form-control" required>
                        <option value="per_ipc" {{ old('calculation_frequency', $contract?->calculation_frequency ?? 'per_ipc') == 'per_ipc' ? 'selected' : '' }}>
                            مع كل مستخلص
                        </option>
                        <option value="monthly" {{ old('calculation_frequency', $contract?->calculation_frequency) == 'monthly' ? 'selected' : '' }}>
                            شهري
                        </option>
                        <option value="quarterly" {{ old('calculation_frequency', $contract?->calculation_frequency) == 'quarterly' ? 'selected' : '' }}>
                            ربع سنوي
                        </option>
                        <option value="annual" {{ old('calculation_frequency', $contract?->calculation_frequency) == 'annual' ? 'selected' : '' }}>
                            سنوي
                        </option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label class="form-label">ساري من</label>
                    <input type="date" name="effective_from" class="form-control" 
                           value="{{ old('effective_from', $contract?->effective_from?->format('Y-m-d')) }}">
                </div>
                
                <div class="form-group">
                    <label class="form-label">ساري إلى</label>
                    <input type="date" name="effective_to" class="form-control" 
                           value="{{ old('effective_to', $contract?->effective_to?->format('Y-m-d')) }}">
                </div>
            </div>
            
            @if($contract)
                <div class="form-group">
                    <label style="display: flex; align-items: center; gap: 10px; cursor: pointer;">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $contract->is_active) ? 'checked' : '' }}>
                        <span class="form-label" style="margin: 0;">العقد نشط</span>
                    </label>
                </div>
            @endif
        </div>
        
        <div class="form-actions">
            <a href="{{ route('price-escalation.index') }}" class="btn-secondary">إلغاء</a>
            <button type="submit" class="btn-primary">
                {{ $contract ? 'تحديث العقد' : 'إنشاء العقد' }}
            </button>
        </div>
    </form>
</div>

<script>
    lucide.createIcons();
    
    // Calculate total weights
    function updateTotal() {
        const materials = parseFloat(document.getElementById('materialsWeight').value) || 0;
        const labor = parseFloat(document.getElementById('laborWeight').value) || 0;
        const fixed = parseFloat(document.getElementById('fixedPortion').value) || 0;
        const total = materials + labor + fixed;
        
        document.getElementById('totalValue').textContent = total.toFixed(2);
        
        const indicator = document.getElementById('totalIndicator');
        if (Math.abs(total - 100) < 0.01) {
            indicator.className = 'total-indicator total-valid';
            document.getElementById('totalStatus').textContent = '✅';
        } else {
            indicator.className = 'total-indicator total-invalid';
            document.getElementById('totalStatus').textContent = '❌';
        }
    }
    
    document.getElementById('materialsWeight').addEventListener('input', updateTotal);
    document.getElementById('laborWeight').addEventListener('input', updateTotal);
    document.getElementById('fixedPortion').addEventListener('input', updateTotal);
    
    // Fetch base indices when contract date changes
    document.getElementById('contractDate').addEventListener('change', async function() {
        const date = this.value;
        if (!date) return;
        
        try {
            const response = await fetch(`{{ route('price-escalation.dsi-indices.trend') }}?date=${date}`);
            const data = await response.json();
            
            // Find index for the selected date
            const dateObj = new Date(date);
            const year = dateObj.getFullYear();
            const month = dateObj.getMonth() + 1;
            
            const index = data.find(d => d.year === year && d.month === month);
            
            if (index) {
                document.getElementById('baseMaterialsIndex').textContent = parseFloat(index.materials_index).toFixed(4);
                document.getElementById('baseLaborIndex').textContent = parseFloat(index.labor_index).toFixed(4);
            } else {
                document.getElementById('baseMaterialsIndex').textContent = 'غير متوفر';
                document.getElementById('baseLaborIndex').textContent = 'غير متوفر';
            }
        } catch (error) {
            console.error('Error fetching DSI indices:', error);
        }
    });
    
    // Initialize
    updateTotal();
    
    @if($contract && $contract->base_materials_index)
        document.getElementById('baseMaterialsIndex').textContent = '{{ $contract->base_materials_index }}';
        document.getElementById('baseLaborIndex').textContent = '{{ $contract->base_labor_index }}';
    @endif
</script>
@endsection
