@extends('layouts.app')

@section('content')
<style>
    .response-plan-view {
        padding: 20px;
        max-width: 1200px;
        margin: 0 auto;
    }

    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 30px;
        flex-wrap: wrap;
        gap: 15px;
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

    .plan-intro {
        background: white;
        padding: 25px;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        margin-bottom: 25px;
        text-align: center;
    }

    .plan-intro h2 {
        font-size: 1.3rem;
        color: #1d1d1f;
        margin-bottom: 10px;
    }

    .plan-intro p {
        color: #6e6e73;
        font-size: 0.95rem;
    }

    .risk-plan-card {
        background: white;
        padding: 30px;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        margin-bottom: 20px;
        border-right: 5px solid #0071e3;
    }

    .risk-plan-card.critical {
        border-right-color: #000;
    }

    .risk-plan-card.high {
        border-right-color: #ff3b30;
    }

    .risk-plan-card.medium {
        border-right-color: #ff9500;
    }

    .risk-plan-card.low {
        border-right-color: #34c759;
    }

    .risk-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 20px;
        flex-wrap: wrap;
        gap: 15px;
    }

    .risk-title-section {
        flex: 1;
    }

    .risk-code {
        font-family: 'SF Mono', 'Courier New', monospace;
        font-weight: 700;
        color: #0071e3;
        font-size: 0.9rem;
        margin-bottom: 5px;
    }

    .risk-title {
        font-size: 1.2rem;
        font-weight: 700;
        color: #1d1d1f;
        margin-bottom: 10px;
    }

    .risk-meta {
        display: flex;
        gap: 15px;
        flex-wrap: wrap;
    }

    .badge {
        display: inline-block;
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 600;
    }

    .badge-critical {
        background: #000;
        color: white;
    }

    .badge-high {
        background: #ff3b30;
        color: white;
    }

    .badge-medium {
        background: #ff9500;
        color: white;
    }

    .badge-low {
        background: #34c759;
        color: white;
    }

    .risk-score-badge {
        background: #f5f5f7;
        color: #1d1d1f;
        padding: 5px 15px;
        border-radius: 20px;
        font-size: 0.85rem;
        font-weight: 700;
    }

    .risk-section {
        margin-bottom: 20px;
    }

    .section-title {
        font-size: 0.9rem;
        font-weight: 700;
        color: #6e6e73;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 10px;
    }

    .section-content {
        background: #f5f5f7;
        padding: 15px;
        border-radius: 8px;
        color: #1d1d1f;
        font-size: 0.95rem;
        line-height: 1.6;
    }

    .strategy-box {
        background: #f0f7ff;
        border-left: 4px solid #0071e3;
        padding: 15px;
        border-radius: 8px;
    }

    .strategy-name {
        font-weight: 700;
        color: #0071e3;
        font-size: 1.1rem;
        margin-bottom: 5px;
    }

    .info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 15px;
        margin-top: 15px;
    }

    .info-item {
        background: #f5f5f7;
        padding: 12px;
        border-radius: 8px;
    }

    .info-label {
        font-size: 0.75rem;
        color: #6e6e73;
        margin-bottom: 5px;
        font-weight: 600;
    }

    .info-value {
        font-size: 1rem;
        color: #1d1d1f;
        font-weight: 700;
    }

    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: #6e6e73;
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    }

    .empty-state-icon {
        font-size: 4rem;
        margin-bottom: 20px;
    }

    @media print {
        .page-header,
        .action-buttons {
            display: none;
        }
    }
</style>

<div class="response-plan-view">
    <div class="page-header">
        <h1 class="page-title">خطة الاستجابة للمخاطر</h1>
        <div class="action-buttons">
            <button onclick="window.print()" class="btn btn-secondary">📄 طباعة</button>
            <a href="{{ route('tender-risks.dashboard', $tender->id) }}" class="btn btn-secondary">← العودة</a>
        </div>
    </div>

    <div class="tender-info">
        <span class="tender-code">{{ $tender->code }}</span> - {{ $tender->title }}
    </div>

    <div class="plan-intro">
        <h2>خطة الاستجابة الشاملة للمخاطر</h2>
        <p>هذه الوثيقة تحتوي على جميع المخاطر المحددة واستراتيجيات الاستجابة المخططة</p>
    </div>

    @if($tender->risks->count() > 0)
        @foreach($tender->risks as $risk)
            <div class="risk-plan-card {{ $risk->risk_level }}">
                <div class="risk-header">
                    <div class="risk-title-section">
                        <div class="risk-code">{{ $risk->risk_code }}</div>
                        <h3 class="risk-title">{{ $risk->risk_title }}</h3>
                        <div class="risk-meta">
                            @if($risk->risk_level == 'critical')
                                <span class="badge badge-critical">⚫ حرج</span>
                            @elseif($risk->risk_level == 'high')
                                <span class="badge badge-high">🔴 عالي</span>
                            @elseif($risk->risk_level == 'medium')
                                <span class="badge badge-medium">🟡 متوسط</span>
                            @else
                                <span class="badge badge-low">🟢 منخفض</span>
                            @endif
                            <span class="risk-score-badge">النتيجة: {{ $risk->risk_score }}</span>
                        </div>
                    </div>
                </div>

                <!-- الوصف -->
                <div class="risk-section">
                    <div class="section-title">📋 الوصف</div>
                    <div class="section-content">
                        {{ $risk->risk_description }}
                    </div>
                </div>

                <!-- الاستراتيجية -->
                @if($risk->response_strategy)
                    <div class="risk-section">
                        <div class="section-title">🎯 استراتيجية الاستجابة</div>
                        <div class="strategy-box">
                            <div class="strategy-name">{{ $risk->response_strategy_name }}</div>
                        </div>
                    </div>
                @endif

                <!-- خطة الاستجابة -->
                @if($risk->response_plan)
                    <div class="risk-section">
                        <div class="section-title">📝 الإجراءات المخططة</div>
                        <div class="section-content">
                            {{ $risk->response_plan }}
                        </div>
                    </div>
                @endif

                <!-- معلومات إضافية -->
                <div class="info-grid">
                    <div class="info-item">
                        <div class="info-label">الفئة</div>
                        <div class="info-value">{{ $risk->category_name }}</div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">الاحتمالية</div>
                        <div class="info-value">{{ $risk->probability_score }}/5</div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">التأثير</div>
                        <div class="info-value">{{ $risk->impact_score }}/5</div>
                    </div>

                    @if($risk->response_cost > 0)
                        <div class="info-item">
                            <div class="info-label">تكلفة الاستجابة</div>
                            <div class="info-value">{{ number_format($risk->response_cost, 2) }} د.أ</div>
                        </div>
                    @endif

                    @if($risk->owner)
                        <div class="info-item">
                            <div class="info-label">المسؤول</div>
                            <div class="info-value">{{ $risk->owner->name }}</div>
                        </div>
                    @endif

                    @if($risk->cost_impact_expected)
                        <div class="info-item">
                            <div class="info-label">التأثير المالي المتوقع</div>
                            <div class="info-value">{{ number_format($risk->cost_impact_expected, 2) }} د.أ</div>
                        </div>
                    @endif

                    @if($risk->schedule_impact_days)
                        <div class="info-item">
                            <div class="info-label">التأثير الزمني</div>
                            <div class="info-value">{{ $risk->schedule_impact_days }} يوم</div>
                        </div>
                    @endif
                </div>
            </div>
        @endforeach
    @else
        <div class="empty-state">
            <div class="empty-state-icon">📋</div>
            <div>لا توجد مخاطر مسجلة</div>
            <p>لم يتم تسجيل أي مخاطر لهذا العطاء بعد</p>
        </div>
    @endif
</div>
@endsection
