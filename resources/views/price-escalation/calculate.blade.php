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
    
    .indices-display {
        background: #f5f5f7;
        border-radius: 12px;
        padding: 25px;
        margin-top: 20px;
    }
    
    .indices-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
    }
    
    .index-group {
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
        display: flex;
        align-items: center;
        gap: 5px;
    }
    
    .change-positive {
        color: #34c759;
    }
    
    .change-negative {
        color: #ff3b30;
    }
    
    .calculation-result {
        background: linear-gradient(135deg, #0071e3, #00c4cc);
        color: white;
        border-radius: 12px;
        padding: 30px;
        margin-top: 20px;
    }
    
    .result-title {
        font-size: 1rem;
        opacity: 0.9;
        margin-bottom: 15px;
    }
    
    .result-formula {
        font-family: monospace;
        font-size: 1.1rem;
        background: rgba(255,255,255,0.1);
        padding: 15px;
        border-radius: 8px;
        margin-bottom: 20px;
        line-height: 1.8;
    }
    
    .result-value {
        font-size: 2.5rem;
        font-weight: 700;
        margin-bottom: 10px;
    }
    
    .result-amounts {
        display: grid;
        grid-template-columns: 1fr 1fr 1fr;
        gap: 20px;
        margin-top: 20px;
    }
    
    .amount-box {
        background: rgba(255,255,255,0.15);
        padding: 15px;
        border-radius: 8px;
        text-align: center;
    }
    
    .amount-label {
        font-size: 0.85rem;
        opacity: 0.9;
        margin-bottom: 8px;
    }
    
    .amount-value {
        font-size: 1.5rem;
        font-weight: 700;
    }
    
    .threshold-warning {
        background: #fff3cd;
        color: #856404;
        padding: 15px;
        border-radius: 8px;
        margin-top: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .threshold-success {
        background: #d1f4dd;
        color: #047857;
        padding: 15px;
        border-radius: 8px;
        margin-top: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
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
    
    .form-actions {
        display: flex;
        gap: 15px;
        justify-content: flex-end;
        margin-top: 30px;
    }
</style>

<div class="pe-container">
    <div class="page-header">
        <h1 class="page-title">حساب فروقات الأسعار</h1>
        <p style="color: #86868b;">احسب فروقات الأسعار تلقائياً باستخدام مؤشرات DSI</p>
    </div>
    
    @if($errors->any())
        <div style="background: #fee; color: #dc2626; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
            <strong>خطأ:</strong>
            <ul style="margin: 10px 0 0 20px;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    
    <form method="POST" action="{{ route('price-escalation.calculations.store') }}" id="calculationForm">
        @csrf
        
        <!-- 1. الفترة -->
        <div class="form-card">
            <h2 class="section-title">1. اختيار العقد والفترة</h2>
            
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">عقد فروقات الأسعار *</label>
                    <select name="price_escalation_contract_id" class="form-control" required id="contractSelect">
                        <option value="">اختر العقد</option>
                        @foreach($contracts as $contract)
                            <option value="{{ $contract->id }}" 
                                    data-project="{{ $contract->project->name }}"
                                    data-threshold="{{ $contract->threshold_percentage }}"
                                    data-materials-weight="{{ $contract->materials_weight }}"
                                    data-labor-weight="{{ $contract->labor_weight }}">
                                {{ $contract->project->name }} - {{ $contract->project->code }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="form-group">
                    <label class="form-label">ربط بمستخلص (اختياري)</label>
                    <select name="main_ipc_id" class="form-control" id="ipcSelect">
                        <option value="">بدون مستخلص</option>
                        @foreach($ipcs as $ipc)
                            <option value="{{ $ipc->id }}" data-amount="{{ $ipc->amount }}">
                                {{ $ipc->ipc_number }} - {{ $ipc->project->name }} ({{ number_format($ipc->amount, 0) }} د.أ)
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">من تاريخ *</label>
                    <input type="date" name="period_from" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label">إلى تاريخ *</label>
                    <input type="date" name="period_to" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label">تاريخ الحساب *</label>
                    <input type="date" name="calculation_date" class="form-control" required id="calculationDate" value="{{ date('Y-m-d') }}">
                </div>
            </div>
            
            <div class="form-group" id="manualAmountGroup" style="display: none;">
                <label class="form-label">مبلغ المستخلص (د.أ) *</label>
                <input type="number" step="0.01" name="ipc_amount" class="form-control" id="ipcAmount">
            </div>
            
            <div style="text-align: center; margin-top: 20px;">
                <button type="button" class="btn-secondary" onclick="previewCalculation()" style="padding: 12px 40px;">
                    <i data-lucide="eye" style="width: 18px; height: 18px; display: inline-block; vertical-align: middle;"></i>
                    معاينة الحساب
                </button>
            </div>
        </div>
        
        <!-- 2. Preview Results -->
        <div id="previewResults" style="display: none;">
            <!-- المؤشرات -->
            <div class="form-card">
                <h2 class="section-title">2. المؤشرات</h2>
                
                <div class="indices-display">
                    <h4 style="margin-bottom: 20px;">المؤشرات المرجعية (Base):</h4>
                    <div class="indices-grid">
                        <div class="index-group">
                            <div class="index-label">مؤشر المواد (L₀)</div>
                            <div class="index-value" id="baseMatIndex">--</div>
                        </div>
                        <div class="index-group">
                            <div class="index-label">مؤشر العمالة (P₀)</div>
                            <div class="index-value" id="baseLaborIndex">--</div>
                        </div>
                    </div>
                    
                    <h4 style="margin: 30px 0 20px;">المؤشرات الحالية (Current):</h4>
                    <div class="indices-grid">
                        <div class="index-group">
                            <div class="index-label">مؤشر المواد (L₁)</div>
                            <div class="index-value" id="currentMatIndex">--</div>
                            <div class="index-change" id="matChange">--</div>
                        </div>
                        <div class="index-group">
                            <div class="index-label">مؤشر العمالة (P₁)</div>
                            <div class="index-value" id="currentLaborIndex">--</div>
                            <div class="index-change" id="laborChange">--</div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- الحساب -->
            <div class="form-card">
                <h2 class="section-title">3. الحساب التلقائي</h2>
                
                <div class="calculation-result">
                    <div class="result-title">المعادلة:</div>
                    <div class="result-formula" id="formula">
                        E = (A × ΔL) + (B × ΔP)
                    </div>
                    
                    <div class="result-title">نسبة فروقات الأسعار:</div>
                    <div class="result-value" id="escalationPercent">--</div>
                    
                    <div class="result-amounts">
                        <div class="amount-box">
                            <div class="amount-label">مبلغ المستخلص</div>
                            <div class="amount-value" id="ipcAmountDisplay">--</div>
                        </div>
                        <div class="amount-box">
                            <div class="amount-label">فروقات الأسعار</div>
                            <div class="amount-value" id="escalationAmount">--</div>
                        </div>
                        <div class="amount-box">
                            <div class="amount-label">الإجمالي</div>
                            <div class="amount-value" id="totalAmount">--</div>
                        </div>
                    </div>
                </div>
                
                <div id="thresholdMessage"></div>
            </div>
        </div>
        
        <div class="form-actions">
            <a href="{{ route('price-escalation.calculations') }}" class="btn-secondary">إلغاء</a>
            <button type="submit" class="btn-primary" id="submitBtn" disabled>
                حفظ الحساب
            </button>
        </div>
    </form>
</div>

<script>
    lucide.createIcons();
    
    // Toggle manual amount input
    document.getElementById('ipcSelect').addEventListener('change', function() {
        const manualGroup = document.getElementById('manualAmountGroup');
        const amountInput = document.getElementById('ipcAmount');
        
        if (this.value === '') {
            manualGroup.style.display = 'block';
            amountInput.required = true;
        } else {
            manualGroup.style.display = 'none';
            amountInput.required = false;
            amountInput.value = this.options[this.selectedIndex].dataset.amount;
        }
    });
    
    async function previewCalculation() {
        const contractId = document.getElementById('contractSelect').value;
        const calculationDate = document.getElementById('calculationDate').value;
        const ipcSelect = document.getElementById('ipcSelect');
        const ipcAmount = ipcSelect.value ? 
            ipcSelect.options[ipcSelect.selectedIndex].dataset.amount :
            document.getElementById('ipcAmount').value;
        
        if (!contractId || !calculationDate || !ipcAmount) {
            alert('يرجى ملء جميع الحقول المطلوبة');
            return;
        }
        
        try {
            const response = await fetch('{{ route('price-escalation.calculations.preview') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    contract_id: contractId,
                    calculation_date: calculationDate,
                    ipc_amount: parseFloat(ipcAmount)
                })
            });
            
            const result = await response.json();
            
            if (result.success) {
                displayPreview(result.data, result.threshold_percentage);
                document.getElementById('submitBtn').disabled = false;
            } else {
                alert('خطأ: ' + result.error);
            }
        } catch (error) {
            console.error('Error:', error);
            alert('حدث خطأ في الحساب');
        }
    }
    
    function displayPreview(data, threshold) {
        // Show preview
        document.getElementById('previewResults').style.display = 'block';
        
        // Base indices
        document.getElementById('baseMatIndex').textContent = data.base_materials_index.toFixed(4);
        document.getElementById('baseLaborIndex').textContent = data.base_labor_index.toFixed(4);
        
        // Current indices
        document.getElementById('currentMatIndex').textContent = data.current_materials_index.toFixed(4);
        document.getElementById('currentLaborIndex').textContent = data.current_labor_index.toFixed(4);
        
        // Changes
        const matChangeElem = document.getElementById('matChange');
        const matChange = data.materials_change_percent;
        matChangeElem.innerHTML = `<i data-lucide="${matChange >= 0 ? 'arrow-up' : 'arrow-down'}" style="width: 16px; height: 16px;"></i> ${matChange > 0 ? '+' : ''}${matChange.toFixed(2)}%`;
        matChangeElem.className = `index-change ${matChange >= 0 ? 'change-positive' : 'change-negative'}`;
        
        const laborChangeElem = document.getElementById('laborChange');
        const laborChange = data.labor_change_percent;
        laborChangeElem.innerHTML = `<i data-lucide="${laborChange >= 0 ? 'arrow-up' : 'arrow-down'}" style="width: 16px; height: 16px;"></i> ${laborChange > 0 ? '+' : ''}${laborChange.toFixed(2)}%`;
        laborChangeElem.className = `index-change ${laborChange >= 0 ? 'change-positive' : 'change-negative'}`;
        
        // Formula
        const contractSelect = document.getElementById('contractSelect');
        const A = contractSelect.options[contractSelect.selectedIndex].dataset.materialsWeight;
        const B = contractSelect.options[contractSelect.selectedIndex].dataset.laborWeight;
        
        document.getElementById('formula').innerHTML = `
            E = (A × ΔL) + (B × ΔP)<br>
            E = (${A}% × ${matChange.toFixed(2)}%) + (${B}% × ${laborChange.toFixed(2)}%)<br>
            E = ${((A/100) * matChange).toFixed(2)}% + ${((B/100) * laborChange).toFixed(2)}%<br>
            E = ${data.escalation_percentage}%
        `;
        
        // Result
        document.getElementById('escalationPercent').textContent = data.escalation_percentage.toFixed(2) + '%';
        document.getElementById('ipcAmountDisplay').textContent = data.ipc_amount.toLocaleString() + ' د.أ';
        document.getElementById('escalationAmount').textContent = data.escalation_amount.toLocaleString() + ' د.أ';
        document.getElementById('totalAmount').textContent = (parseFloat(data.ipc_amount) + parseFloat(data.escalation_amount)).toLocaleString() + ' د.أ';
        
        // Threshold message
        const thresholdMsg = document.getElementById('thresholdMessage');
        if (data.threshold_met) {
            thresholdMsg.className = 'threshold-success';
            thresholdMsg.innerHTML = `<i data-lucide="check-circle" style="width: 20px; height: 20px;"></i> تجاوز العتبة (${threshold}%) - سيتم تطبيق الفروقات ✅`;
        } else {
            thresholdMsg.className = 'threshold-warning';
            thresholdMsg.innerHTML = `<i data-lucide="alert-circle" style="width: 20px; height: 20px;"></i> أقل من العتبة (${threshold}%) - لن يتم تطبيق الفروقات ⚠️`;
        }
        
        lucide.createIcons();
        
        // Scroll to results
        document.getElementById('previewResults').scrollIntoView({ behavior: 'smooth' });
    }
</script>
@endsection
