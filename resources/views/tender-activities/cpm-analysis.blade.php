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
        margin-bottom: 24px;
    }
    
    .page-title {
        font-size: 1.75rem;
        font-weight: 700;
        color: var(--text);
    }
    
    .btn {
        padding: 10px 20px;
        border-radius: 8px;
        border: none;
        cursor: pointer;
        font-weight: 600;
        font-size: 0.9rem;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.2s;
    }
    
    .btn-secondary {
        background: #6c757d;
        color: white;
    }
    
    .btn-outline-secondary {
        background: transparent;
        color: #6c757d;
        border: 1px solid #6c757d;
    }
    
    .breadcrumb {
        display: flex;
        gap: 8px;
        margin-bottom: 20px;
        font-size: 0.9rem;
        color: #6c757d;
    }
    
    .breadcrumb a {
        color: var(--accent);
        text-decoration: none;
    }
    
    .stats-row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 16px;
        margin-bottom: 24px;
    }
    
    .stat-card {
        padding: 20px;
        border-radius: 12px;
        color: white;
    }
    
    .stat-card:nth-child(1) {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }
    
    .stat-card:nth-child(2) {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    }
    
    .stat-card:nth-child(3) {
        background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
    }
    
    .stat-card:nth-child(4) {
        background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
    }
    
    .stat-value {
        font-size: 2rem;
        font-weight: 700;
    }
    
    .stat-label {
        font-size: 0.9rem;
        opacity: 0.9;
        margin-top: 4px;
    }
    
    .card-header {
        background: #f8f9fa;
        border-bottom: 2px solid #dee2e6;
        padding: 16px 24px;
        margin: -24px -24px 24px -24px;
        border-radius: 12px 12px 0 0;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    
    .card-header.bg-danger {
        background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
        color: white;
    }
    
    .table {
        width: 100%;
        border-collapse: collapse;
    }
    
    .table thead {
        background: #f8f9fa;
    }
    
    .table th {
        padding: 12px;
        text-align: right;
        font-weight: 600;
        color: #495057;
        border-bottom: 2px solid #dee2e6;
        font-size: 0.9rem;
    }
    
    .table td {
        padding: 12px;
        border-bottom: 1px solid #dee2e6;
        text-align: right;
        font-size: 0.9rem;
    }
    
    .table tbody tr:hover {
        background: #f8f9fa;
    }
    
    .table-danger tr {
        background: #f8d7da !important;
    }
    
    .table-danger tr:hover {
        background: #f1b0b7 !important;
    }
    
    .badge {
        display: inline-block;
        padding: 4px 12px;
        border-radius: 12px;
        font-size: 0.75rem;
        font-weight: 600;
    }
    
    .badge.bg-danger {
        background: #dc3545;
        color: white;
    }
    
    .badge.bg-success {
        background: #28a745;
        color: white;
    }
    
    .badge.bg-warning {
        background: #ffc107;
        color: #000;
    }
    
    @media print {
        .page-header button,
        .page-header a {
            display: none;
        }
    }
</style>

<link href="https://unpkg.com/vis-network/styles/vis-network.min.css" rel="stylesheet">

<div class="breadcrumb">
    <a href="{{ route('dashboard') }}">الرئيسية</a> / 
    <a href="{{ route('tenders.index') }}">العطاءات</a> / 
    <a href="{{ route('tenders.show', $tender) }}">{{ $tender->name }}</a> / 
    <a href="{{ route('tender-activities.index', $tender) }}">الأنشطة</a> / 
    <span>تحليل CPM</span>
</div>

<div class="page-header">
    <div>
        <h1 class="page-title">تحليل المسار الحرج (CPM) - {{ $tender->name }}</h1>
        <p style="color: #6c757d; margin-top: 8px;">رمز العطاء: {{ $tender->tender_code }}</p>
    </div>
    <div style="display: flex; gap: 12px;">
        <button onclick="window.print()" class="btn btn-secondary">
            <i data-lucide="printer"></i>
            طباعة
        </button>
        <a href="{{ route('tender-activities.index', $tender) }}" class="btn btn-outline-secondary">
            <i data-lucide="arrow-right"></i>
            رجوع
        </a>
    </div>
</div>

<!-- Summary Cards -->
<div class="stats-row">
    <div class="stat-card">
        <div class="stat-value">{{ $activities->count() }}</div>
        <div class="stat-label">إجمالي الأنشطة</div>
    </div>
    <div class="stat-card">
        <div class="stat-value">{{ $criticalActivities->count() }}</div>
        <div class="stat-label">الأنشطة الحرجة</div>
    </div>
    <div class="stat-card">
        <div class="stat-value">{{ $cpmResult['project_duration'] ?? 0 }} يوم</div>
        <div class="stat-label">مدة المشروع</div>
    </div>
    <div class="stat-card">
        <div class="stat-value">{{ $activities->filter(fn($a) => $a->successors->isNotEmpty() || $a->predecessors->isNotEmpty())->count() }}</div>
        <div class="stat-label">الأنشطة المرتبطة</div>
    </div>
</div>

<!-- Critical Path -->
<div class="card">
    <div class="card-header bg-danger">
        <i data-lucide="route"></i>
        <span>المسار الحرج (Critical Path)</span>
    </div>
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>الكود</th>
                    <th>النشاط</th>
                    <th>المدة</th>
                    <th>ES</th>
                    <th>EF</th>
                    <th>LS</th>
                    <th>LF</th>
                    <th>Float</th>
                </tr>
            </thead>
            <tbody class="table-danger">
                @forelse($criticalActivities as $activity)
                <tr>
                    <td><strong>{{ $activity->activity_code }}</strong></td>
                    <td>{{ $activity->name }}</td>
                    <td>{{ $activity->duration_days }} يوم</td>
                    <td>{{ $activity->early_start }}</td>
                    <td>{{ $activity->early_finish }}</td>
                    <td>{{ $activity->late_start }}</td>
                    <td>{{ $activity->late_finish }}</td>
                    <td><span class="badge bg-danger">{{ $activity->total_float }}</span></td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" style="text-align: center; padding: 40px; color: #6c757d;">
                        لم يتم العثور على أنشطة حرجة. قد تحتاج إلى <a href="{{ route('tender-activities.recalculate-cpm', $tender) }}" onclick="event.preventDefault(); document.getElementById('recalculate-form').submit();">إعادة حساب CPM</a>.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Non-Critical Activities -->
@php
    $nonCriticalActivities = $activities->where('is_critical', false);
@endphp

@if($nonCriticalActivities->isNotEmpty())
<div class="card">
    <div class="card-header">
        <i data-lucide="list"></i>
        <span>الأنشطة غير الحرجة</span>
    </div>
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>الكود</th>
                    <th>النشاط</th>
                    <th>المدة</th>
                    <th>ES</th>
                    <th>EF</th>
                    <th>LS</th>
                    <th>LF</th>
                    <th>Total Float</th>
                    <th>Free Float</th>
                </tr>
            </thead>
            <tbody>
                @foreach($nonCriticalActivities as $activity)
                <tr>
                    <td>{{ $activity->activity_code }}</td>
                    <td>{{ $activity->name }}</td>
                    <td>{{ $activity->duration_days }}</td>
                    <td>{{ $activity->early_start }}</td>
                    <td>{{ $activity->early_finish }}</td>
                    <td>{{ $activity->late_start }}</td>
                    <td>{{ $activity->late_finish }}</td>
                    <td>
                        <span class="badge bg-{{ $activity->total_float > 10 ? 'success' : ($activity->total_float > 5 ? 'warning' : 'danger') }}">
                            {{ $activity->total_float }} يوم
                        </span>
                    </td>
                    <td>{{ $activity->free_float }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

<!-- Network Diagram -->
<div class="card">
    <div class="card-header">
        <i data-lucide="git-branch"></i>
        <span>مخطط الشبكة</span>
    </div>
    <div id="network-diagram" style="height: 500px; border: 1px solid #dee2e6; border-radius: 8px;"></div>
</div>

<!-- Hidden form for recalculating CPM -->
<form id="recalculate-form" action="{{ route('tender-activities.recalculate-cpm', $tender) }}" method="POST" style="display: none;">
    @csrf
</form>

<script src="https://unpkg.com/vis-network/standalone/umd/vis-network.min.js"></script>
<script>
    lucide.createIcons();
    
    // Network Diagram
    var nodes = new vis.DataSet([
        @foreach($activities as $activity)
        {
            id: {{ $activity->id }},
            label: '{{ $activity->activity_code }}\n{{ $activity->duration_days }}د',
            color: {{ $activity->is_critical ? '{background: "#ff6b6b", border: "#cc0000"}' : '{background: "#4CAF50", border: "#2E7D32"}' }},
            font: {color: 'white', size: 12},
            title: '{{ $activity->name }}<br>ES: {{ $activity->early_start }}, EF: {{ $activity->early_finish }}<br>LS: {{ $activity->late_start }}, LF: {{ $activity->late_finish }}<br>Float: {{ $activity->total_float }}'
        },
        @endforeach
    ]);
    
    var edges = new vis.DataSet([
        @foreach($networkData['edges'] ?? [] as $edge)
        {
            from: {{ $edge['from'] }},
            to: {{ $edge['to'] }},
            arrows: 'to',
            color: '#666',
            label: '{{ $edge['type'] }}'
        },
        @endforeach
    ]);
    
    var container = document.getElementById('network-diagram');
    var data = {nodes: nodes, edges: edges};
    var options = {
        layout: {
            hierarchical: {
                direction: 'LR',
                sortMethod: 'directed',
                levelSeparation: 150,
                nodeSpacing: 100
            }
        },
        physics: false,
        interaction: {
            hover: true,
            tooltipDelay: 100
        }
    };
    var network = new vis.Network(container, data, options);
</script>
@endsection
