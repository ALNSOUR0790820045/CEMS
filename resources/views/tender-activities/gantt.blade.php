@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2><i class="fas fa-chart-gantt me-2"></i>مخطط جانت - {{ $tender->name }}</h2>
            <small class="text-muted">{{ $activities->count() }} نشاط • {{ $dependencies->count() }} علاقة</small>
        </div>
        <div class="btn-group">
            <button onclick="gantt.exportToPNG()" class="btn btn-outline-secondary">
                <i class="fas fa-image me-2"></i>تصدير PNG
            </button>
            <button onclick="gantt.exportToPDF()" class="btn btn-outline-secondary">
                <i class="fas fa-file-pdf me-2"></i>تصدير PDF
            </button>
            <a href="{{ route('tender-activities. cpm-analysis', $tender->id) }}" class="btn btn-outline-info">
                <i class="fas fa-project-diagram me-2"></i>تحليل CPM
            </a>
            <a href="{{ route('tender-activities.index', $tender->id) }}" class="btn btn-secondary">
                <i class="fas fa-arrow-right me-2"></i>رجوع
            </a>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-md-3">
            <div class="card bg-primary bg-opacity-10 border-0">
                <div class="card-body text-center">
                    <h4 class="text-primary">{{ $activities->count() }}</h4>
                    <small class="text-muted">إجمالي الأنشطة</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-danger bg-opacity-10 border-0">
                <div class="card-body text-center">
                    <h4 class="text-danger">{{ $activities->where('is_critical', true)->count() }}</h4>
                    <small class="text-muted">الأنشطة الحرجة</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success bg-opacity-10 border-0">
                <div class="card-body text-center">
                    <h4 class="text-success">{{ $activities->sum('duration_days') }}</h4>
                    <small class="text-muted">إجمالي المدة (أيام)</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info bg-opacity-10 border-0">
                <div class="card-body text-center">
                    <h4 class="text-info">{{ number_format($activities->sum('estimated_cost'), 0) }}</h4>
                    <small class="text-muted">التكلفة المقدرة</small>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header bg-white">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">مخطط جانت التفاعلي</h5>
                <div class="btn-group btn-group-sm">
                    <button onclick="gantt.ext.zoom. setLevel('day')" class="btn btn-outline-secondary">يوم</button>
                    <button onclick="gantt.ext.zoom.setLevel('week')" class="btn btn-outline-secondary">أسبوع</button>
                    <button onclick="gantt.ext.zoom. setLevel('month')" class="btn btn-outline-secondary">شهر</button>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <div id="gantt_here" style="width: 100%; height:650px;"></div>
        </div>
    </div>
</div>

<link href="https://cdn.dhtmlx.com/gantt/edge/dhtmlx-gantt.css" rel="stylesheet">
<script src="https://cdn.dhtmlx.com/gantt/edge/dhtmlx-gantt.js"></script>

<script>
gantt.config.date_format = "%Y-%m-%d";
gantt.config.rtl = true;
gantt. config.readonly = true;
gantt.config.autosize = "y";
gantt.config.scale_height = 54;

gantt.config.columns = [
    {name: "text", label: "النشاط", tree: true, width: 250},
    {name: "duration", label: "المدة", align: "center", width: 60},
    {name: "start_date", label: "البداية", align: "center", width: 90},
    {name: "is_critical", label: "حرج؟", align: "center", width: 60, template: function(task) {
        return task.is_critical ? '<span class="badge bg-danger">حرج</span>' : '';
    }}
];

gantt.ext.zoom. init({
    levels: [
        {
            name: "day",
            scale_height: 54,
            min_column_width: 80,
            scales: [{unit: "day", step: 1, format: "%d %M"}]
        },
        {
            name: "week",
            scale_height: 54,
            min_column_width: 50,
            scales: [
                {unit: "week", step: 1, format: "أسبوع #%W"},
                {unit: "day", step: 1, format:  "%d"}
            ]
        },
        {
            name: "month",
            scale_height: 54,
            min_column_width: 120,
            scales: [{unit: "month", step: 1, format: "%F %Y"}]
        }
    ]
});
gantt.ext.zoom.setLevel("week");

var tasks = {
    data: [
        @foreach($activities as $activity)
        {
            id: {{ $activity->id }},
            text:  "{{ $activity->activity_code }} - {{ $activity->name }}",
            start_date: "{{ $activity->planned_start_date ??  now()->format('Y-m-d') }}",
            duration: {{ $activity->duration_days }},
            progress: 0,
            is_critical:  {{ $activity->is_critical ?  'true' : 'false' }},
            parent: 0
        }{{ ! $loop->last ? ',' :  '' }}
        @endforeach
    ],
    links: [
        @foreach($dependencies as $dep)
        {
            id: {{ $dep->id }},
            source: {{ $dep->predecessor_id }},
            target:  {{ $dep->successor_id }},
            type: "{{ $dep->type == 'FS' ? '0' : ($dep->type == 'SS' ? '1' : ($dep->type == 'FF' ? '2' : '3')) }}"
        }{{ !$loop->last ? ',' : '' }}
        @endforeach
    ]
};

gantt.templates.task_class = function(start, end, task){
    if(task.is_critical){
        return "gantt_critical_task";
    }
    return "";
};

gantt.templates. tooltip_text = function(start, end, task){
    return "<b>النشاط:</b> " + task.text + "<br/>" +
           "<b>المدة:</b> " + task.duration + " يوم<br/>" +
           "<b>البداية:</b> " + gantt.templates.tooltip_date_format(start) + "<br/>" +
           "<b>النهاية:</b> " + gantt.templates.tooltip_date_format(end);
};

gantt.init("gantt_here");
gantt.parse(tasks);
</script>

<style>
.gantt_critical_task {
    background-color: #dc3545 !important;
    border-color: #bd2130 !important;
}
.gantt_critical_task . gantt_task_progress {
    background-color: #c82333 !important;
}
.gantt_task_line {
    border-radius: 6px;
}
.gantt_task_link . gantt_link_arrow {
    border-color: #666;
}
</style>
@endsection
