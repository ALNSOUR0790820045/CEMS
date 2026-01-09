@extends('layouts.app')

@section('content')
<style>
    .card {
        background: white;
        border-radius: 12px;
        padding: 24px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        margin-bottom: 20px;
    }

    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 30px;
    }

    .page-title {
        font-size: 2rem;
        font-weight: 700;
        color: #1d1d1f;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-label {
        font-size: 0.9rem;
        font-weight: 600;
        color: #1d1d1f;
        margin-bottom: 8px;
        display: block;
    }

    .form-control {
        width: 100%;
        padding: 12px;
        border: 1px solid #d2d2d7;
        border-radius: 6px;
        font-size: 0.9rem;
        font-family: 'Cairo', sans-serif;
    }

    .form-control:focus {
        outline: none;
        border-color: #0071e3;
        box-shadow: 0 0 0 3px rgba(0, 113, 227, 0.1);
    }

    .btn {
        padding: 12px 24px;
        border-radius: 8px;
        border: none;
        font-size: 0.9rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
        font-family: 'Cairo', sans-serif;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        text-decoration: none;
    }

    .btn-success {
        background: #34c759;
        color: white;
    }

    .btn-danger {
        background: #ff3b30;
        color: white;
    }

    .btn-secondary {
        background: #f5f5f7;
        color: #1d1d1f;
    }

    .decision-options {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
        margin-bottom: 30px;
    }

    .decision-card {
        padding: 30px;
        border: 3px solid #e5e5e7;
        border-radius: 12px;
        cursor: pointer;
        transition: all 0.3s;
        text-align: center;
    }

    .decision-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.1);
    }

    .decision-card.selected {
        border-color: #0071e3;
        background: rgba(0, 113, 227, 0.05);
    }

    .decision-card.go.selected {
        border-color: #34c759;
        background: rgba(52, 199, 89, 0.05);
    }

    .decision-card.no-go.selected {
        border-color: #ff3b30;
        background: rgba(255, 59, 48, 0.05);
    }

    .decision-icon {
        font-size: 3rem;
        margin-bottom: 15px;
    }

    .decision-title {
        font-size: 1.5rem;
        font-weight: 700;
        margin-bottom: 10px;
    }

    .decision-description {
        font-size: 0.9rem;
        color: #86868b;
    }

    .info-box {
        background: #f5f5f7;
        padding: 20px;
        border-radius: 8px;
        margin-bottom: 20px;
    }

    .info-row {
        display: flex;
        justify-content: space-between;
        padding: 10px 0;
        border-bottom: 1px solid #e5e5e7;
    }

    .info-row:last-child {
        border-bottom: none;
    }

    .info-label {
        font-weight: 600;
        color: #86868b;
    }

    .info-value {
        font-weight: 600;
        color: #1d1d1f;
    }
</style>

<div class="page-header">
    <h1 class="page-title">قرار المشاركة في العطاء</h1>
    <a href="{{ route('tenders.show', $tender) }}" class="btn btn-secondary">
        <i data-lucide="arrow-right" style="width: 18px; height: 18px;"></i>
        رجوع
    </a>
</div>

<div class="card">
    <h2 style="font-size: 1.5rem; font-weight: 700; margin-bottom: 20px;">{{ $tender->tender_name }}</h2>
    
    <div class="info-box">
        <div class="info-row">
            <span class="info-label">رقم العطاء</span>
            <span class="info-value">{{ $tender->tender_number }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">الجهة المالكة</span>
            <span class="info-value">{{ $tender->owner_name }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">القيمة التقديرية</span>
            <span class="info-value">{{ number_format($tender->estimated_value ?? 0, 2) }} {{ $tender->currency->code ?? '' }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">آخر موعد للتقديم</span>
            <span class="info-value">{{ $tender->submission_deadline->format('Y-m-d') }} ({{ $tender->getDaysUntilSubmission() }} أيام)</span>
        </div>
    </div>

    <form method="POST" action="{{ route('tenders.decision.store', $tender) }}">
        @csrf

        <input type="hidden" name="participate" id="participate_input" value="">

        <div class="decision-options">
            <div class="decision-card go" id="go_card">
                <div class="decision-icon">✅</div>
                <div class="decision-title">المشاركة</div>
                <div class="decision-description">نوصي بالمشاركة في هذا العطاء</div>
            </div>

            <div class="decision-card no-go" id="no_go_card">
                <div class="decision-icon">❌</div>
                <div class="decision-title">عدم المشاركة</div>
                <div class="decision-description">نوصي بعدم المشاركة في هذا العطاء</div>
            </div>
        </div>

        <div class="form-group">
            <label class="form-label">الأسباب والمبررات</label>
            <textarea name="participation_decision_notes" class="form-control" rows="8" placeholder="اذكر الأسباب والمبررات لهذا القرار...">{{ old('participation_decision_notes') }}</textarea>
        </div>

        <div style="border-top: 1px solid #e5e5e7; padding-top: 20px; margin-top: 20px;">
            <h3 style="font-size: 1.2rem; font-weight: 700; margin-bottom: 15px;">تحليل SWOT</h3>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                <div>
                    <label class="form-label">نقاط القوة (Strengths)</label>
                    <textarea class="form-control" rows="4" placeholder="ما هي نقاط قوتنا في هذا العطاء؟"></textarea>
                </div>
                
                <div>
                    <label class="form-label">نقاط الضعف (Weaknesses)</label>
                    <textarea class="form-control" rows="4" placeholder="ما هي نقاط ضعفنا أو التحديات؟"></textarea>
                </div>
                
                <div>
                    <label class="form-label">الفرص (Opportunities)</label>
                    <textarea class="form-control" rows="4" placeholder="ما هي الفرص المتاحة؟"></textarea>
                </div>
                
                <div>
                    <label class="form-label">التهديدات (Threats)</label>
                    <textarea class="form-control" rows="4" placeholder="ما هي المخاطر والتهديدات؟"></textarea>
                </div>
            </div>
        </div>

        <div style="display: flex; gap: 15px; justify-content: flex-end; margin-top: 30px;">
            <a href="{{ route('tenders.show', $tender) }}" class="btn btn-secondary">
                إلغاء
            </a>
            <button type="submit" class="btn btn-success" id="submit_btn" disabled>
                <i data-lucide="save" style="width: 18px; height: 18px;"></i>
                حفظ القرار
            </button>
        </div>
    </form>
</div>

<script>
    lucide.createIcons();

    const goCard = document.getElementById('go_card');
    const noGoCard = document.getElementById('no_go_card');
    const participateInput = document.getElementById('participate_input');
    const submitBtn = document.getElementById('submit_btn');

    goCard.addEventListener('click', function() {
        goCard.classList.add('selected');
        noGoCard.classList.remove('selected');
        participateInput.value = '1';
        submitBtn.disabled = false;
    });

    noGoCard.addEventListener('click', function() {
        noGoCard.classList.add('selected');
        goCard.classList.remove('selected');
        participateInput.value = '0';
        submitBtn.disabled = false;
    });
</script>
@endsection
