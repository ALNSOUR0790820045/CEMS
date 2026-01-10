@extends('layouts.app')

@section('content')
<style>
    .risk-dashboard {
        padding: 20px;
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

    .kpi-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }

    .kpi-card {
        background: white;
        padding: 25px;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        text-align: center;
        transition: transform 0.2s;
    }

    .kpi-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.12);
    }

    .kpi-value {
        font-size: 2.5rem;
        font-weight: 700;
        margin: 10px 0;
    }

    .kpi-label {
        font-size: 0.9rem;
        color: #6e6e73;
        font-weight: 500;
    }

    .kpi-total .kpi-value { color: #0071e3; }
    .kpi-critical .kpi-value { color: #000; }
    .kpi-high .kpi-value { color: #ff3b30; }
    .kpi-medium .kpi-value { color: #ff9500; }
    .kpi-low .kpi-value { color: #34c759; }

    .matrix-section {
        background: white;
        padding: 30px;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    }

    .section-title {
        font-size: 1.3rem;
        font-weight: 700;
        color: #1d1d1f;
        margin-bottom: 20px;
        text-align: center;
    }

    .risk-matrix {
        display: grid;
        grid-template-columns: 80px repeat(5, 1fr);
        grid-template-rows: 40px repeat(5, 80px);
        gap: 2px;
        max-width: 700px;
        margin: 0 auto;
        font-size: 0.85rem;
    }

    .matrix-header {
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        color: #6e6e73;
        text-align: center;
        padding: 5px;
    }

    .matrix-label {
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        color: #6e6e73;
        text-align: center;
        padding: 5px;
    }

    .matrix-cell {
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        font-weight: 700;
        font-size: 1.2rem;
        cursor: pointer;
        transition: all 0.2s;
    }

    .matrix-cell:hover {
        transform: scale(1.05);
        box-shadow: 0 4px 12px rgba(0,0,0,0.2);
    }

    .matrix-cell.low { background: #34c759; color: white; }
    .matrix-cell.medium { background: #ff9500; color: white; }
    .matrix-cell.high { background: #ff3b30; color: white; }
    .matrix-cell.critical { background: #000; color: white; }

    .matrix-legend {
        display: flex;
        justify-content: center;
        gap: 20px;
        margin-top: 20px;
        flex-wrap: wrap;
    }

    .legend-item {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 0.85rem;
    }

    .legend-color {
        width: 20px;
        height: 20px;
        border-radius: 4px;
    }

    .action-buttons {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
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

    .btn-primary:hover {
        background: #0077ed;
        transform: translateY(-1px);
    }

    .btn-secondary {
        background: #f5f5f7;
        color: #1d1d1f;
    }

    .btn-secondary:hover {
        background: #e8e8ed;
    }

    @media (max-width: 768px) {
        .kpi-grid {
            grid-template-columns: 1fr;
        }
        
        .risk-matrix {
            font-size: 0.7rem;
            grid-template-rows: 35px repeat(5, 60px);
        }
    }
</style>

<div class="risk-dashboard">
    <div class="page-header">
        <h1 class="page-title">لوحة المخاطر</h1>
        <div class="action-buttons">
            <a href="{{ route('tender-risks.index', $tender->id) }}" class="btn btn-secondary">📋 قائمة المخاطر</a>
            <a href="{{ route('tender-risks.create', $tender->id) }}" class="btn btn-primary">➕ مخاطرة جديدة</a>
        </div>
    </div>

    <div class="tender-info">
        <span class="tender-code">{{ $tender->code }}</span> - {{ $tender->title }}
    </div>

    <!-- KPIs -->
    <div class="kpi-grid">
        <div class="kpi-card kpi-total">
            <div class="kpi-label">إجمالي المخاطر</div>
            <div class="kpi-value">{{ $kpis['total'] }}</div>
        </div>
        <div class="kpi-card kpi-critical">
            <div class="kpi-label">⚫ حرجة</div>
            <div class="kpi-value">{{ $kpis['critical'] }}</div>
        </div>
        <div class="kpi-card kpi-high">
            <div class="kpi-label">🔴 عالية</div>
            <div class="kpi-value">{{ $kpis['high'] }}</div>
        </div>
        <div class="kpi-card kpi-medium">
            <div class="kpi-label">🟡 متوسطة</div>
            <div class="kpi-value">{{ $kpis['medium'] }}</div>
        </div>
        <div class="kpi-card kpi-low">
            <div class="kpi-label">🟢 منخفضة</div>
            <div class="kpi-value">{{ $kpis['low'] }}</div>
        </div>
    </div>

    <!-- Risk Matrix -->
    <div class="matrix-section">
        <h2 class="section-title">مصفوفة المخاطر (5×5)</h2>
        
        <div class="risk-matrix">
            <!-- Headers -->
            <div class="matrix-header"></div>
            <div class="matrix-header">ضئيل جداً</div>
            <div class="matrix-header">طفيف</div>
            <div class="matrix-header">متوسط</div>
            <div class="matrix-header">كبير</div>
            <div class="matrix-header">كارثي</div>

            <!-- Row 5 -->
            <div class="matrix-label">شبه مؤكد</div>
            @for($impact = 1; $impact <= 5; $impact++)
                @php
                    $score = 5 * $impact;
                    $count = $matrixData[5][$impact] ?? 0;
                    $level = $score >= 21 ? 'critical' : ($score >= 13 ? 'high' : ($score >= 7 ? 'medium' : 'low'));
                @endphp
                <div class="matrix-cell {{ $level }}" title="الاحتمالية: 5, التأثير: {{ $impact }}, النتيجة: {{ $score }}">
                    {{ $count > 0 ? $count : '' }}
                </div>
            @endfor

            <!-- Row 4 -->
            <div class="matrix-label">مرجح</div>
            @for($impact = 1; $impact <= 5; $impact++)
                @php
                    $score = 4 * $impact;
                    $count = $matrixData[4][$impact] ?? 0;
                    $level = $score >= 21 ? 'critical' : ($score >= 13 ? 'high' : ($score >= 7 ? 'medium' : 'low'));
                @endphp
                <div class="matrix-cell {{ $level }}" title="الاحتمالية: 4, التأثير: {{ $impact }}, النتيجة: {{ $score }}">
                    {{ $count > 0 ? $count : '' }}
                </div>
            @endfor

            <!-- Row 3 -->
            <div class="matrix-label">محتمل</div>
            @for($impact = 1; $impact <= 5; $impact++)
                @php
                    $score = 3 * $impact;
                    $count = $matrixData[3][$impact] ?? 0;
                    $level = $score >= 21 ? 'critical' : ($score >= 13 ? 'high' : ($score >= 7 ? 'medium' : 'low'));
                @endphp
                <div class="matrix-cell {{ $level }}" title="الاحتمالية: 3, التأثير: {{ $impact }}, النتيجة: {{ $score }}">
                    {{ $count > 0 ? $count : '' }}
                </div>
            @endfor

            <!-- Row 2 -->
            <div class="matrix-label">نادر</div>
            @for($impact = 1; $impact <= 5; $impact++)
                @php
                    $score = 2 * $impact;
                    $count = $matrixData[2][$impact] ?? 0;
                    $level = $score >= 21 ? 'critical' : ($score >= 13 ? 'high' : ($score >= 7 ? 'medium' : 'low'));
                @endphp
                <div class="matrix-cell {{ $level }}" title="الاحتمالية: 2, التأثير: {{ $impact }}, النتيجة: {{ $score }}">
                    {{ $count > 0 ? $count : '' }}
                </div>
            @endfor

            <!-- Row 1 -->
            <div class="matrix-label">نادر جداً</div>
            @for($impact = 1; $impact <= 5; $impact++)
                @php
                    $score = 1 * $impact;
                    $count = $matrixData[1][$impact] ?? 0;
                    $level = $score >= 21 ? 'critical' : ($score >= 13 ? 'high' : ($score >= 7 ? 'medium' : 'low'));
                @endphp
                <div class="matrix-cell {{ $level }}" title="الاحتمالية: 1, التأثير: {{ $impact }}, النتيجة: {{ $score }}">
                    {{ $count > 0 ? $count : '' }}
                </div>
            @endfor
        </div>

        <div class="matrix-legend">
            <div class="legend-item">
                <div class="legend-color" style="background: #34c759;"></div>
                <span>🟢 منخفض (1-6)</span>
            </div>
            <div class="legend-item">
                <div class="legend-color" style="background: #ff9500;"></div>
                <span>🟡 متوسط (7-12)</span>
            </div>
            <div class="legend-item">
                <div class="legend-color" style="background: #ff3b30;"></div>
                <span>🔴 عالي (13-20)</span>
            </div>
            <div class="legend-item">
                <div class="legend-color" style="background: #000;"></div>
                <span>⚫ حرج (21-25)</span>
            </div>
        </div>
    </div>
</div>
@endsection
