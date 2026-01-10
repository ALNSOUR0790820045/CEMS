@extends('layouts.app')

@section('content')
<style>
    .matrix-view {
        padding: 20px;
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

    .matrix-container {
        background: white;
        padding: 40px;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    }

    .matrix-grid {
        display: grid;
        grid-template-columns: 100px repeat(5, 1fr);
        grid-template-rows: 50px repeat(5, 120px);
        gap: 3px;
        max-width: 900px;
        margin: 0 auto;
    }

    .matrix-header {
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        color: #1d1d1f;
        text-align: center;
        font-size: 0.9rem;
    }

    .matrix-label {
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        color: #1d1d1f;
        text-align: center;
        font-size: 0.9rem;
        writing-mode: vertical-rl;
        text-orientation: mixed;
    }

    .matrix-cell {
        background: white;
        border-radius: 12px;
        padding: 15px;
        position: relative;
        cursor: pointer;
        transition: all 0.3s;
        box-shadow: 0 2px 6px rgba(0,0,0,0.1);
    }

    .matrix-cell:hover {
        transform: scale(1.05);
        z-index: 10;
        box-shadow: 0 8px 20px rgba(0,0,0,0.2);
    }

    .matrix-cell.low { background: linear-gradient(135deg, #34c759, #30d158); }
    .matrix-cell.medium { background: linear-gradient(135deg, #ff9500, #ffb340); }
    .matrix-cell.high { background: linear-gradient(135deg, #ff3b30, #ff6961); }
    .matrix-cell.critical { background: linear-gradient(135deg, #000, #333); }

    .cell-score {
        position: absolute;
        top: 10px;
        right: 10px;
        font-size: 1.5rem;
        font-weight: 700;
        color: white;
        opacity: 0.3;
    }

    .cell-risks {
        display: flex;
        flex-direction: column;
        gap: 5px;
        margin-top: 20px;
    }

    .risk-item {
        background: rgba(255,255,255,0.9);
        padding: 8px 10px;
        border-radius: 6px;
        font-size: 0.75rem;
        color: #1d1d1f;
        font-weight: 600;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .risk-item:hover {
        white-space: normal;
    }

    .matrix-legend {
        display: flex;
        justify-content: center;
        gap: 30px;
        margin-top: 40px;
        flex-wrap: wrap;
    }

    .legend-item {
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 1rem;
    }

    .legend-color {
        width: 30px;
        height: 30px;
        border-radius: 6px;
    }
</style>

<div class="matrix-view">
    <div class="page-header">
        <h1 class="page-title">مصفوفة المخاطر التفاعلية</h1>
        <div class="action-buttons">
            <a href="{{ route('tender-risks.dashboard', $tender->id) }}" class="btn btn-secondary">← العودة للوحة</a>
        </div>
    </div>

    <div class="tender-info">
        <span class="tender-code">{{ $tender->code }}</span> - {{ $tender->title }}
    </div>

    <div class="matrix-container">
        <div class="matrix-grid">
            <!-- Top left corner -->
            <div class="matrix-header"></div>
            
            <!-- Column headers (Impact) -->
            <div class="matrix-header">ضئيل جداً</div>
            <div class="matrix-header">طفيف</div>
            <div class="matrix-header">متوسط</div>
            <div class="matrix-header">كبير</div>
            <div class="matrix-header">كارثي</div>

            @for($prob = 5; $prob >= 1; $prob--)
                <!-- Row label -->
                <div class="matrix-label">
                    @if($prob == 5) شبه مؤكد
                    @elseif($prob == 4) مرجح
                    @elseif($prob == 3) محتمل
                    @elseif($prob == 2) نادر
                    @else نادر جداً
                    @endif
                </div>

                <!-- Cells -->
                @for($impact = 1; $impact <= 5; $impact++)
                    @php
                        $score = $prob * $impact;
                        $level = $score >= 21 ? 'critical' : ($score >= 13 ? 'high' : ($score >= 7 ? 'medium' : 'low'));
                        $risks = $matrixData[$prob][$impact] ?? [];
                    @endphp
                    <div class="matrix-cell {{ $level }}" title="P={{ $prob }} × I={{ $impact }} = {{ $score }}">
                        <div class="cell-score">{{ $score }}</div>
                        @if(count($risks) > 0)
                            <div class="cell-risks">
                                @foreach($risks as $risk)
                                    <div class="risk-item" title="{{ $risk->risk_title }}">
                                        {{ $risk->risk_code }}
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endfor
            @endfor
        </div>

        <div class="matrix-legend">
            <div class="legend-item">
                <div class="legend-color" style="background: linear-gradient(135deg, #34c759, #30d158);"></div>
                <span>🟢 منخفض (1-6)</span>
            </div>
            <div class="legend-item">
                <div class="legend-color" style="background: linear-gradient(135deg, #ff9500, #ffb340);"></div>
                <span>🟡 متوسط (7-12)</span>
            </div>
            <div class="legend-item">
                <div class="legend-color" style="background: linear-gradient(135deg, #ff3b30, #ff6961);"></div>
                <span>🔴 عالي (13-20)</span>
            </div>
            <div class="legend-item">
                <div class="legend-color" style="background: linear-gradient(135deg, #000, #333);"></div>
                <span>⚫ حرج (21-25)</span>
            </div>
        </div>
    </div>
</div>
@endsection
