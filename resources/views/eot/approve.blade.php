@extends('layouts.app')

@section('content')
<style>
    .eot-approve {
        max-width: 1000px;
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
        font-size: 1.8rem;
        font-weight: 700;
        margin: 0 0 10px 0;
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
        margin-bottom: 20px;
    }
    
    .info-item {
        padding: 15px;
        background: #f5f5f7;
        border-radius: 8px;
    }
    
    .info-label {
        font-size: 0.8rem;
        color: #86868b;
        margin-bottom: 5px;
    }
    
    .info-value {
        font-size: 1.1rem;
        color: #1d1d1f;
        font-weight: 600;
    }
    
    .form-group {
        margin-bottom: 25px;
    }
    
    .form-group label {
        display: block;
        margin-bottom: 8px;
        font-weight: 600;
        color: #1d1d1f;
        font-size: 0.9rem;
    }
    
    .form-control {
        width: 100%;
        padding: 12px;
        border: 1px solid #d1d1d6;
        border-radius: 8px;
        font-size: 0.9rem;
        font-family: 'Cairo', sans-serif;
    }
    
    .form-control:focus {
        outline: none;
        border-color: #0071e3;
        box-shadow: 0 0 0 3px rgba(0, 113, 227, 0.1);
    }
    
    textarea.form-control {
        min-height: 100px;
        resize: vertical;
    }
    
    .decision-options {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 15px;
        margin-bottom: 25px;
    }
    
    .decision-option {
        position: relative;
    }
    
    .decision-option input[type="radio"] {
        position: absolute;
        opacity: 0;
    }
    
    .decision-label {
        display: block;
        padding: 20px;
        border: 2px solid #d1d1d6;
        border-radius: 12px;
        text-align: center;
        cursor: pointer;
        transition: all 0.2s;
    }
    
    .decision-option input[type="radio"]:checked + .decision-label {
        border-color: #0071e3;
        background: rgba(0, 113, 227, 0.05);
    }
    
    .decision-label.approve {
        border-color: #388e3c;
    }
    
    .decision-option input[type="radio"]:checked + .decision-label.approve {
        background: rgba(56, 142, 60, 0.1);
        border-color: #388e3c;
    }
    
    .decision-label.reject {
        border-color: #d32f2f;
    }
    
    .decision-option input[type="radio"]:checked + .decision-label.reject {
        background: rgba(211, 47, 47, 0.1);
        border-color: #d32f2f;
    }
    
    .decision-icon {
        font-size: 2rem;
        margin-bottom: 10px;
    }
    
    .decision-text {
        font-weight: 600;
    }
    
    .btn {
        padding: 12px 30px;
        border-radius: 8px;
        font-size: 0.9rem;
        font-weight: 600;
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
    
    .form-actions {
        display: flex;
        justify-content: flex-end;
        gap: 15px;
        margin-top: 30px;
    }
    
    .alert {
        padding: 15px 20px;
        border-radius: 8px;
        margin-bottom: 20px;
    }
    
    .alert-info {
        background: #e3f2fd;
        color: #1976d2;
        border: 1px solid #90caf9;
    }
    
    .summary-box {
        padding: 20px;
        background: #f5f5f7;
        border-radius: 8px;
        margin: 20px 0;
    }
    
    .summary-row {
        display: flex;
        justify-content: space-between;
        padding: 10px 0;
        border-bottom: 1px solid #e5e5e7;
    }
    
    .summary-row:last-child {
        border-bottom: none;
        font-weight: 700;
        font-size: 1.1rem;
    }
</style>

<div class="eot-approve">
    <div class="page-header">
        <h1>مراجعة مطالبة EOT</h1>
        <p style="color: #86868b; margin: 0;">{{ $eotClaim->eot_number }} - {{ $eotClaim->project->name }}</p>
    </div>

    <!-- ملخص المطالبة -->
    <div class="card">
        <h3 class="card-title">
            <i data-lucide="file-text" style="width: 20px; height: 20px;"></i>
            ملخص المطالبة
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
                <div class="info-label">مدة الحدث</div>
                <div class="info-value">{{ $eotClaim->event_duration_days }} يوم</div>
            </div>
            
            <div class="info-item">
                <div class="info-label">الأيام المطلوبة</div>
                <div class="info-value" style="color: #1976d2;">{{ $eotClaim->requested_days }} يوم</div>
            </div>
        </div>

        <div class="summary-box">
            <div class="summary-row">
                <span>وصف الحدث:</span>
                <span></span>
            </div>
            <p style="margin: 10px 0; color: #1d1d1f;">{{ $eotClaim->event_description }}</p>
            
            <div class="summary-row">
                <span>التأثير:</span>
                <span></span>
            </div>
            <p style="margin: 10px 0; color: #1d1d1f;">{{ $eotClaim->impact_description }}</p>
            
            <div class="summary-row">
                <span>المبررات القانونية:</span>
                <span></span>
            </div>
            <p style="margin: 10px 0; color: #1d1d1f;">{{ $eotClaim->justification }}</p>
        </div>

        @if($eotClaim->has_prolongation_costs)
        <div class="alert alert-info">
            <strong>تكاليف الإطالة:</strong> {{ number_format($eotClaim->total_prolongation_cost, 2) }} د.أ
        </div>
        @endif
    </div>

    <!-- نموذج القرار -->
    <div class="card">
        <h3 class="card-title">
            <i data-lucide="check-circle" style="width: 20px; height: 20px;"></i>
            اتخاذ القرار
        </h3>

        <form method="POST" action="{{ route('eot.approve', $eotClaim) }}">
            @csrf
            
            <div class="form-group">
                <label>القرار</label>
                <div class="decision-options">
                    <div class="decision-option">
                        <input type="radio" name="decision" id="approve" value="approve" required>
                        <label for="approve" class="decision-label approve">
                            <div class="decision-icon">✓</div>
                            <div class="decision-text">موافقة كاملة</div>
                        </label>
                    </div>
                    
                    <div class="decision-option">
                        <input type="radio" name="decision" id="partial" value="partial" required>
                        <label for="partial" class="decision-label">
                            <div class="decision-icon">≈</div>
                            <div class="decision-text">موافقة جزئية</div>
                        </label>
                    </div>
                    
                    <div class="decision-option">
                        <input type="radio" name="decision" id="reject" value="reject" required>
                        <label for="reject" class="decision-label reject">
                            <div class="decision-icon">✗</div>
                            <div class="decision-text">رفض</div>
                        </label>
                    </div>
                </div>
            </div>

            <div class="form-group" id="approved-days-group" style="display: none;">
                <label>الأيام المعتمدة</label>
                <input type="number" name="approved_days" class="form-control" min="0" max="{{ $eotClaim->requested_days }}" placeholder="أدخل عدد الأيام المعتمدة">
                <small style="color: #86868b; display: block; margin-top: 5px;">
                    الحد الأقصى: {{ $eotClaim->requested_days }} يوم
                </small>
            </div>

            <div class="form-group">
                <label>التعليقات والملاحظات <span style="color: #d32f2f;">*</span></label>
                <textarea name="comments" class="form-control" required placeholder="أدخل تعليقاتك وملاحظاتك على المطالبة"></textarea>
            </div>

            <div class="form-actions">
                <a href="{{ route('eot.show', $eotClaim) }}" class="btn btn-secondary">إلغاء</a>
                <button type="submit" class="btn btn-primary">
                    <i data-lucide="check" style="width: 16px; height: 16px;"></i>
                    تأكيد القرار
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    lucide.createIcons();
    
    // Show/hide approved days field based on decision
    const decisionInputs = document.querySelectorAll('input[name="decision"]');
    const approvedDaysGroup = document.getElementById('approved-days-group');
    const approvedDaysInput = document.querySelector('input[name="approved_days"]');
    
    decisionInputs.forEach(input => {
        input.addEventListener('change', function() {
            if (this.value === 'partial') {
                approvedDaysGroup.style.display = 'block';
                approvedDaysInput.required = true;
            } else {
                approvedDaysGroup.style.display = 'none';
                approvedDaysInput.required = false;
                if (this.value === 'approve') {
                    approvedDaysInput.value = {{ $eotClaim->requested_days }};
                }
            }
        });
    });
</script>
@endsection
