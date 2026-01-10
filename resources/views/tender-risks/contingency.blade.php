@extends('layouts.app')

@section('content')
<style>
    .contingency-view {
        padding: 20px;
        max-width: 1000px;
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

    .action-buttons {
        display: flex;
        gap: 10px;
    }

    .btn {
        padding: 10px 20px;
        border-radius: 8px;
        border: none;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
        text-decoration: none;
        display: inline-block;
        font-size: 0.9rem;
    }

    .btn-primary {
        background: #0071e3;
        color: white;
    }

    .btn-secondary {
        background: #f5f5f7;
        color: #1d1d1f;
    }

    .tender-info {
        background: white;
        padding: 15px 20px;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        margin-bottom: 25px;
    }

    .tender-code {
        font-weight: 600;
        color: #0071e3;
        font-size: 1.1rem;
    }

    .summary-card {
        background: white;
        padding: 40px;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        margin-bottom: 30px;
    }

    .summary-title {
        font-size: 1.3rem;
        font-weight: 700;
        color: #1d1d1f;
        margin-bottom: 30px;
        text-align: center;
    }

    .summary-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 20px;
        border-bottom: 1px solid #f0f0f0;
        font-size: 1.1rem;
    }

    .summary-row:last-child {
        border-bottom: 3px solid #0071e3;
        background: #f5f5f7;
        font-weight: 700;
    }

    .summary-label {
        color: #6e6e73;
    }

    .summary-value {
        font-weight: 700;
        color: #1d1d1f;
        font-size: 1.3rem;
    }

    .summary-value.highlight {
        color: #0071e3;
        font-size: 1.8rem;
    }

    .form-card {
        background: white;
        padding: 30px;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    }

    .form-section-title {
        font-size: 1.2rem;
        font-weight: 700;
        color: #1d1d1f;
        margin-bottom: 20px;
    }

    .form-grid {
        display: grid;
        gap: 20px;
    }

    .form-group {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .form-label {
        font-size: 0.9rem;
        font-weight: 600;
        color: #1d1d1f;
    }

    .form-input,
    .form-textarea {
        padding: 10px 15px;
        border: 1px solid #d2d2d7;
        border-radius: 8px;
        font-size: 0.95rem;
        font-family: 'Cairo', sans-serif;
        transition: border-color 0.2s;
    }

    .form-input:focus,
    .form-textarea:focus {
        outline: none;
        border-color: #0071e3;
    }

    .form-textarea {
        min-height: 100px;
        resize: vertical;
    }

    .btn-group {
        display: flex;
        justify-content: flex-end;
        margin-top: 20px;
    }

    .info-box {
        background: #f0f7ff;
        border-left: 4px solid #0071e3;
        padding: 20px;
        border-radius: 8px;
        margin-top: 20px;
    }

    .info-box-title {
        font-weight: 700;
        color: #0071e3;
        margin-bottom: 10px;
    }

    .info-box-text {
        font-size: 0.9rem;
        color: #1d1d1f;
        line-height: 1.6;
    }
</style>

<div class="contingency-view">
    <div class="page-header">
        <h1 class="page-title">احتياطي المخاطر (Contingency Reserve)</h1>
        <div class="action-buttons">
            <a href="{{ route('tender-risks.dashboard', $tender->id) }}" class="btn btn-secondary">← العودة</a>
        </div>
    </div>

    <div class="tender-info">
        <span class="tender-code">{{ $tender->code }}</span> - {{ $tender->title }}
    </div>

    <!-- Summary -->
    <div class="summary-card">
        <h2 class="summary-title">ملخص احتياطي المخاطر</h2>
        
        <div class="summary-row">
            <span class="summary-label">إجمالي التعرض للمخاطر:</span>
            <span class="summary-value">{{ number_format($totalExposure, 2) }} د.أ</span>
        </div>

        <div class="summary-row">
            <span class="summary-label">نسبة الاحتياطي:</span>
            <span class="summary-value">{{ number_format($reserve->contingency_percentage ?? 10, 2) }}%</span>
        </div>

        <div class="summary-row">
            <span class="summary-label">احتياطي المخاطر:</span>
            <span class="summary-value highlight">
                {{ number_format(($totalExposure * ($reserve->contingency_percentage ?? 10)) / 100, 2) }} د.أ
            </span>
        </div>
    </div>

    <!-- Update Form -->
    <div class="form-card">
        <h2 class="form-section-title">تحديث الاحتياطي</h2>

        <form method="POST" action="{{ route('tender-risks.update-contingency', $tender->id) }}">
            @csrf
            
            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label">نسبة الاحتياطي (%)</label>
                    <input 
                        type="number" 
                        step="0.01" 
                        name="contingency_percentage" 
                        class="form-input" 
                        value="{{ $reserve->contingency_percentage ?? 10.00 }}" 
                        required
                        min="0"
                        max="100"
                    >
                </div>

                <div class="form-group">
                    <label class="form-label">التبرير</label>
                    <textarea name="justification" class="form-textarea" placeholder="اذكر التبرير لنسبة الاحتياطي المحددة">{{ $reserve->justification ?? '' }}</textarea>
                </div>
            </div>

            <div class="btn-group">
                <button type="submit" class="btn btn-primary">تحديث الاحتياطي</button>
            </div>
        </form>

        <div class="info-box">
            <div class="info-box-title">📊 كيفية حساب الاحتياطي</div>
            <div class="info-box-text">
                <p><strong>التعرض للمخاطر:</strong> يتم حسابه بجمع (الاحتمالية × التأثير المالي المتوقع) لكل مخاطرة.</p>
                <p style="margin-top: 10px;"><strong>احتياطي المخاطر:</strong> = التعرض للمخاطر × نسبة الاحتياطي</p>
                <p style="margin-top: 10px;">النسبة الموصى بها: 5-15% حسب درجة عدم اليقين في العطاء.</p>
            </div>
        </div>
    </div>
</div>
@endsection
