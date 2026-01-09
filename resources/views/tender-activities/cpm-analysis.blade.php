@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2><i class="fas fa-project-diagram me-2"></i>تحليل المسار الحرج (CPM)</h2>
            <small class="text-muted">{{ $tender->name }}</small>
        </div>
        <div class="btn-group">
            <button onclick="window.print()" class="btn btn-outline-secondary">
                <i class="fas fa-print me-2"></i>طباعة
            </button>
            <a href="{{ route('tender-activities. gantt', $tender->id) }}" class="btn btn-outline-info">
                <i class="fas fa-chart-gantt me-2"></i>مخطط جانت
            </a>
            <a href="{{ route('tender-activities.index', $tender->id) }}" class="btn btn-secondary">
                <i class="fas fa-arrow-right me-2"></i>رجوع
            </a>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center bg-primary bg-opacity-10">
                    <i class="fas fa-tasks fa-2x text-primary mb-2"></i>
                    <h3 class="mb-0">{{ $activities->count() }}</h3>
                    <small class="text-muted">إجمالي الأنشطة</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center bg-danger bg-opacity-10">
                    <i class="fas fa-exclamation-triangle fa-2x text-danger mb-2"></i>
                    <h3 class="mb-0">{{ $criticalActivities->count() }}</h3>
                    <small class="text-muted">الأنشطة الحرجة</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center bg-success bg-opacity-10">
                    <i class="fas fa-calendar-alt fa-2x text-success mb-2"></i>
                    <h3 class="mb-0">{{ $projectDuration }}</h3>
                    <small class="text-muted">مدة المشروع (يوم)</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center bg-info bg-opacity-10">
                    <i class="fas fa-link fa-2x text-info mb-2"></i>
                    <h3 class="mb-0">{{ $dependencies->count() }}</h3>
                    <small class="text-muted">إجمالي العلاقات</small>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-header bg-danger text-white">
            <h5 class="mb-0"><i class="fas fa-route me-2"></i>المسار الحرج (Critical Path)</h5>
        </div>
        <div class="card-body">
            @if($criticalActivities->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
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
                        <tbody>
                            @foreach($criticalActivities as $activity)
                            <tr class="table-danger">
                                <td><strong>{{ $activity->activity_code }}</strong></td>
                                <td>{{ $activity->name }}</td>
                                <td><span class="badge bg-secondary">{{ $activity->duration_days }} يوم</span></td>
                                <td>{{ $activity->early_start ??  0 }}</td>
                                <td>{{ $activity->early_finish ?? 0 }}</td>
                                <td>{{ $activity->late_start ?? 0 }}</td>
                                <td>{{ $activity->late_finish ?? 0 }}</td>
                                <td><span class="badge bg-danger">{{ $activity->total_float ??  0 }}</span></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="alert alert-warning">
                    <i class="fas fa-info-circle me-2"></i>لم يتم حساب المسار الحرج بعد.  يرجى تشغيل حساب CPM. 
                </div>
            @endif
        </div>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-header bg-white">
            <h5 class="mb-0"><i class="fas fa-list me-2"></i>الأنشطة غير الحرجة</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-sm">
                    <thead class="table-light">
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
                            <th>الحالة</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($nonCriticalActivities as $activity)
                        <tr>
                            <td>{{ $activity->activity_code }}</td>
                            <td>{{ $activity->name }}</td>
                            <td>{{ $activity->duration_days }}</td>
                            <td>{{ $activity->early_start ??  0 }}</td>
                            <td>{{ $activity->early_finish ?? 0 }}</td>
                            <td>{{ $activity->late_start ??  0 }}</td>
                            <td>{{ $activity->late_finish ?? 0 }}</td>
                            <td>
                                @php
                                    $float = $activity->total_float ??  0;
                                    $badgeClass = $float > 10 ? 'success' : ($float > 5 ? 'warning' : 'danger');
                                @endphp
                                <span class="badge bg-{{ $badgeClass }}">{{ $float }} يوم</span>
                            </td>
                            <td>{{ $activity->free_float ?? 0 }}</td>
                            <td>
                                @if($float > 10)
                                    <span class="badge bg-success">مرن</span>
                                @elseif($float > 5)
                                    <span class="badge bg-warning">متوسط</span>
                                @else
                                    <span class="badge bg-danger">محدود</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header bg-white">
            <h5 class="mb-0"><i class="fas fa-sitemap me-2"></i>مخطط الشبكة (Network Diagram)</h5>
        </div>
        <div class="card-body">
            <div id="network-diagram" style="height: 550px; border: 1px solid #dee2e6; border-radius:8px;"></div>
        </div>
    </div>
</div>

<script src="https://unpkg.com/vis-network/standalone/umd/vis-network.min.js"></script>
<link href="https://unpkg.com/vis-network/styles/vis-network.min.css" rel="stylesheet">

<script>
var nodes = new vis.DataSet([
    @foreach($activities as $activity)
    {
        id: {{ $activity->id }},
        label: '{{ $activity->activity_code }}\n{{ $activity->name }}\n{{ $activity->duration_days }}د',
        color: {
            background: '{{ $activity->is_critical ? "#dc3545" : "#198754" }}',
            border:  '{{ $activity->is_critical ? "#bd2130" : "#146c43" }}',
            highlight: {
                background: '{{ $activity->is_critical ? "#c82333" : "#157347" }}',
                border: '{{ $activity->is_critical ?  "#a71d2a" : "#0f5132" }}'
            }
        },
        font: {color: 'white', size: 14, face: 'Arial'},
        shape: 'box',
        margin: 10
    }{{ !$loop->last ? ',' : '' }}
    @endforeach
]);

var edges = new vis.DataSet([
    @foreach($dependencies as $dep)
    {
        from: {{ $dep->predecessor_id }},
        to: {{ $dep->successor_id }},
        arrows: 'to',
        color: {color: '#6c757d', highlight: '#495057'},
        width: 2,
        smooth: {type: 'cubicBezier', roundness: 0.5}
    }{{ !$loop->last ? ',' : '' }}
    @endforeach
]);

var container = document.getElementById('network-diagram');
var data = {nodes: nodes, edges: edges};
var options = {
    layout: {
        hierarchical: {
            direction: 'LR',
            sortMethod: 'directed',
            levelSeparation: 200,
            nodeSpacing: 150
        }
    },
    physics: {enabled: false},
    interaction: {dragNodes: true, dragView: true, zoomView: true}
};

var network = new vis.Network(container, data, options);
network.fit();
</script>

<style>
@media print {
    .btn, .card-header {
        display: none !important;
    }
}
</style>
@endsection
