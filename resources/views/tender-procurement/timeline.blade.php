@extends('layouts.app')

@section('content')
<style>
    .page-header {
        background: white;
        padding: 25px 30px;
        border-radius: 12px;
        margin-bottom: 25px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    }

    .page-title {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--text);
        margin: 0 0 5px 0;
    }

    .timeline-container {
        background: white;
        padding: 30px;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        margin-bottom: 25px;
    }

    .timeline {
        position: relative;
        padding: 20px 0;
    }

    .timeline-item {
        position: relative;
        padding: 20px 0 20px 40px;
        border-right: 2px solid var(--border);
    }

    .timeline-item:last-child {
        border-right: none;
    }

    .timeline-marker {
        position: absolute;
        right: -9px;
        top: 25px;
        width: 16px;
        height: 16px;
        border-radius: 50%;
        background: var(--accent);
        border: 3px solid white;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .timeline-marker.critical {
        background: #ff3b30;
    }

    .timeline-content {
        background: #f9f9f9;
        padding: 20px;
        border-radius: 8px;
        margin-bottom: 15px;
    }

    .timeline-header {
        display: flex;
        justify-content: space-between;
        align-items: start;
        margin-bottom: 15px;
    }

    .timeline-title {
        font-size: 1rem;
        font-weight: 600;
        color: var(--text);
    }

    .timeline-code {
        font-size: 0.85rem;
        color: #86868b;
        background: white;
        padding: 4px 10px;
        border-radius: 6px;
    }

    .timeline-details {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 15px;
    }

    .timeline-detail {
        font-size: 0.85rem;
    }

    .timeline-detail-label {
        color: #86868b;
        margin-bottom: 3px;
    }

    .timeline-detail-value {
        font-weight: 600;
        color: var(--text);
    }

    .gantt-chart {
        background: white;
        padding: 30px;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        overflow-x: auto;
    }

    .gantt-header {
        display: grid;
        grid-template-columns: 200px 1fr;
        gap: 20px;
        margin-bottom: 20px;
    }

    .gantt-row {
        display: grid;
        grid-template-columns: 200px 1fr;
        gap: 20px;
        margin-bottom: 15px;
        align-items: center;
    }

    .gantt-label {
        font-size: 0.9rem;
        font-weight: 600;
        color: var(--text);
    }

    .gantt-bar-container {
        background: #f5f5f7;
        height: 40px;
        border-radius: 8px;
        position: relative;
        overflow: hidden;
    }

    .gantt-bar {
        height: 100%;
        background: linear-gradient(90deg, var(--accent), #00c4cc);
        border-radius: 8px;
        display: flex;
        align-items: center;
        padding: 0 10px;
        color: white;
        font-size: 0.8rem;
        font-weight: 600;
    }

    .gantt-bar.critical {
        background: linear-gradient(90deg, #ff3b30, #ff6b5a);
    }

    .btn {
        padding: 10px 20px;
        border: none;
        border-radius: 8px;
        font-size: 0.9rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
        text-decoration: none;
        display: inline-block;
        font-family: 'Cairo', sans-serif;
    }

    .btn-secondary {
        background: #f5f5f7;
        color: var(--text);
    }

    .badge {
        display: inline-block;
        padding: 4px 10px;
        border-radius: 12px;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .badge-critical {
        background: #ffebee;
        color: #d32f2f;
    }

    .badge-normal {
        background: #e3f2fd;
        color: #1976d2;
    }
</style>

<div class="page-header">
    <h1 class="page-title">الجدول الزمني للشراء - {{ $tender->name }}</h1>
    <p style="color: #86868b; font-size: 0.9rem; margin: 5px 0 0 0;">{{ $tender->tender_number }}</p>
</div>

<div style="margin-bottom: 20px;">
    <a href="{{ route('tender-procurement.index', $tender->id) }}" class="btn btn-secondary">رجوع</a>
</div>

<div class="timeline-container">
    <h3 style="margin: 0 0 30px 0; font-size: 1.2rem; font-weight: 600;">خط زمني للحزم</h3>
    
    @if($packages->isEmpty())
        <div style="text-align: center; padding: 60px 20px; color: #86868b;">
            <i data-lucide="calendar" style="width: 60px; height: 60px; margin-bottom: 15px;"></i>
            <p>لا توجد حزم لعرضها</p>
        </div>
    @else
        <div class="timeline">
            @foreach($packages as $package)
            <div class="timeline-item">
                <div class="timeline-marker {{ $package->lead_time_days && $package->lead_time_days > 60 ? 'critical' : '' }}"></div>
                
                <div class="timeline-content">
                    <div class="timeline-header">
                        <div>
                            <div class="timeline-title">{{ $package->package_name }}</div>
                            <div style="font-size: 0.85rem; color: #86868b; margin-top: 3px;">
                                {{ $package->getProcurementTypeLabel() }}
                            </div>
                        </div>
                        <span class="timeline-code">{{ $package->package_code }}</span>
                    </div>

                    <div class="timeline-details">
                        <div class="timeline-detail">
                            <div class="timeline-detail-label">تاريخ الحاجة</div>
                            <div class="timeline-detail-value">
                                {{ $package->required_by_date?->format('Y-m-d') ?? 'غير محدد' }}
                            </div>
                        </div>

                        <div class="timeline-detail">
                            <div class="timeline-detail-label">مدة التوريد</div>
                            <div class="timeline-detail-value">
                                {{ $package->lead_time_days ?? 'غير محدد' }} 
                                @if($package->lead_time_days) يوم @endif
                            </div>
                        </div>

                        <div class="timeline-detail">
                            <div class="timeline-detail-label">تاريخ بدء الشراء</div>
                            <div class="timeline-detail-value">
                                {{ $package->procurement_start?->format('Y-m-d') ?? 'غير محدد' }}
                            </div>
                        </div>

                        <div class="timeline-detail">
                            <div class="timeline-detail-label">المسؤول</div>
                            <div class="timeline-detail-value">
                                {{ $package->responsible?->name ?? 'غير محدد' }}
                            </div>
                        </div>
                    </div>

                    @if($package->lead_time_days && $package->lead_time_days > 60)
                        <div style="margin-top: 15px;">
                            <span class="badge badge-critical">⚠ بند طويل الأجل</span>
                        </div>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    @endif
</div>

@if($packages->whereNotNull('required_by_date')->isNotEmpty())
<div class="gantt-chart">
    <h3 style="margin: 0 0 30px 0; font-size: 1.2rem; font-weight: 600;">مخطط جانت</h3>
    
    <div class="gantt-header">
        <div style="font-size: 0.85rem; font-weight: 600; color: #86868b;">الحزمة</div>
        <div style="font-size: 0.85rem; font-weight: 600; color: #86868b;">الجدول الزمني</div>
    </div>

    @php
        $minDate = $packages->whereNotNull('required_by_date')->min('required_by_date');
        $maxDate = $packages->whereNotNull('required_by_date')->max('required_by_date');
        $totalDays = $minDate && $maxDate ? $minDate->diffInDays($maxDate) : 100;
        $totalDays = max($totalDays, 1);
    @endphp

    @foreach($packages->whereNotNull('required_by_date')->sortBy('required_by_date') as $package)
        @php
            $daysFromStart = $minDate ? $minDate->diffInDays($package->required_by_date) : 0;
            $barStart = ($daysFromStart / $totalDays) * 100;
            $barWidth = ($package->lead_time_days ?? 10) / $totalDays * 100;
            $barWidth = min($barWidth, 100 - $barStart);
        @endphp
        
        <div class="gantt-row">
            <div class="gantt-label">
                {{ $package->package_name }}
                <div style="font-size: 0.75rem; color: #86868b; margin-top: 3px;">
                    {{ $package->package_code }}
                </div>
            </div>
            <div class="gantt-bar-container">
                <div class="gantt-bar {{ $package->lead_time_days && $package->lead_time_days > 60 ? 'critical' : '' }}" 
                     style="margin-right: {{ $barStart }}%; width: {{ $barWidth }}%;">
                    {{ $package->required_by_date->format('M d') }}
                </div>
            </div>
        </div>
    @endforeach
</div>
@endif

<script>
    lucide.createIcons();
</script>
@endsection
