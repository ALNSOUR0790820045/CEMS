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
    
    .btn-primary {
        background: var(--accent);
        color: white;
    }
    
    .btn-secondary {
        background: #6c757d;
        color: white;
    }
    
    .btn-success {
        background: #28a745;
        color: white;
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
    
    .controls {
        display: flex;
        gap: 12px;
        margin-bottom: 20px;
        flex-wrap: wrap;
        align-items: center;
    }
    
    .btn-small {
        padding: 8px 16px;
        font-size: 0.85rem;
    }
    
    #gantt_here {
        height: calc(100vh - 300px);
        min-height: 600px;
        width: 100%;
        background: white;
        border: 1px solid #ddd;
        border-radius: 8px;
        overflow: hidden;
    }
    
    .stats-summary {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 16px;
        margin-bottom: 20px;
    }
    
    .stat-box {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 16px;
        border-radius: 8px;
        text-align: center;
    }
    
    .stat-box:nth-child(2) {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    }
    
    .stat-box:nth-child(3) {
        background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
    }
    
    .stat-box:nth-child(4) {
        background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
    }
    
    .stat-box:nth-child(5) {
        background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
    }
    
    .stat-value {
        font-size: 1.5rem;
        font-weight: 700;
        margin-bottom: 4px;
    }
    
    .stat-label {
        font-size: 0.75rem;
        opacity: 0.9;
    }
    
    .gantt_critical .gantt_task_line {
        background-color: #dc3545 !important;
        border-color: #dc3545 !important;
    }
    
    .gantt_critical .gantt_task_progress {
        background-color: #c82333 !important;
    }
    
    .gantt_milestone .gantt_task_line {
        background-color: #ffc107 !important;
        border-color: #ffc107 !important;
    }
</style>

<div class="breadcrumb">
    <a href="{{ route('dashboard') }}">الرئيسية</a> / 
    <a href="{{ route('tenders.index') }}">العطاءات</a> / 
    <a href="{{ route('tenders.show', $tender) }}">{{ $tender->name }}</a> / 
    <a href="{{ route('tender-activities.index', $tender) }}">الأنشطة</a> / 
    <span>مخطط جانت</span>
</div>

<div class="page-header">
    <div>
        <h1 class="page-title">مخطط جانت: {{ $tender->name }}</h1>
        <p style="color: #6c757d; margin-top: 8px;">رمز العطاء: {{ $tender->tender_code }}</p>
    </div>
    <div style="display: flex; gap: 12px;">
        <a href="{{ route('tender-activities.index', $tender) }}" class="btn btn-secondary">
            <i data-lucide="list"></i>
            قائمة الأنشطة
        </a>
        <a href="{{ route('tender-activities.cpm-analysis', $tender) }}" class="btn btn-primary">
            <i data-lucide="git-branch"></i>
            تحليل CPM
        </a>
    </div>
</div>

<div class="stats-summary">
    <div class="stat-box">
        <div class="stat-value">{{ $activities->count() }}</div>
        <div class="stat-label">إجمالي الأنشطة</div>
    </div>
    <div class="stat-box">
        <div class="stat-value">{{ $activities->where('is_critical', true)->count() }}</div>
        <div class="stat-label">أنشطة حرجة</div>
    </div>
    <div class="stat-box">
        <div class="stat-value">{{ $dependencies->count() }}</div>
        <div class="stat-label">علاقات الأنشطة</div>
    </div>
    <div class="stat-box">
        <div class="stat-value">{{ $activities->sum('duration_days') }}</div>
        <div class="stat-label">إجمالي المدة (أيام)</div>
    </div>
    <div class="stat-box">
        <div class="stat-value">{{ $activities->where('type', 'milestone')->count() }}</div>
        <div class="stat-label">المعالم</div>
    </div>
</div>

<div class="card">
    <div class="controls">
        <button class="btn btn-primary btn-small" onclick="gantt.ext.zoom.zoomIn()">
            <i data-lucide="zoom-in" style="width: 14px; height: 14px;"></i>
            تكبير
        </button>
        <button class="btn btn-primary btn-small" onclick="gantt.ext.zoom.zoomOut()">
            <i data-lucide="zoom-out" style="width: 14px; height: 14px;"></i>
            تصغير
        </button>
        <button class="btn btn-success btn-small" onclick="exportToPNG()">
            <i data-lucide="download" style="width: 14px; height: 14px;"></i>
            تصدير PNG
        </button>
        <button class="btn btn-success btn-small" onclick="exportToPDF()">
            <i data-lucide="file-text" style="width: 14px; height: 14px;"></i>
            تصدير PDF
        </button>
        <div style="flex-grow: 1;"></div>
        <label style="display: flex; align-items: center; gap: 8px;">
            <input type="checkbox" id="showCriticalOnly" onchange="toggleCriticalPath()">
            <span>إظهار المسار الحرج فقط</span>
        </label>
    </div>
    
    <div id="gantt_here"></div>
</div>

<link href="https://cdn.dhtmlx.com/gantt/8.0.6/dhtmlxgantt.css" rel="stylesheet">
<script src="https://cdn.dhtmlx.com/gantt/8.0.6/dhtmlxgantt.js"></script>
<script src="https://cdn.dhtmlx.com/gantt/8.0.6/ext/dhtmlxgantt_marker.js"></script>
<script src="https://cdn.dhtmlx.com/gantt/8.0.6/ext/dhtmlxgantt_tooltip.js"></script>

<script>
    lucide.createIcons();
    
    // Configure RTL
    gantt.config.rtl = true;
    gantt.config.layout = {
        css: "gantt_container",
        rows: [
            {
                cols: [
                    {view: "grid", scrollX: "scrollHor", scrollY: "scrollVer"},
                    {resizer: true, width: 1},
                    {view: "timeline", scrollX: "scrollHor", scrollY: "scrollVer"},
                    {view: "scrollbar", id: "scrollVer"}
                ]
            },
            {view: "scrollbar", id: "scrollHor", height: 20}
        ]
    };
    
    // Configure columns
    gantt.config.columns = [
        {name: "text", label: "اسم النشاط", tree: true, width: 250, resize: true},
        {name: "activity_code", label: "الكود", align: "center", width: 100, resize: true},
        {name: "duration", label: "المدة", align: "center", width: 60},
        {name: "start_date", label: "البداية", align: "center", width: 80, resize: true},
        {name: "end_date", label: "النهاية", align: "center", width: 80, resize: true},
        {name: "total_float", label: "Float", align: "center", width: 50}
    ];
    
    // Configure date format
    gantt.config.date_format = "%Y-%m-%d";
    gantt.config.scale_unit = "day";
    gantt.config.date_scale = "%d %M";
    gantt.config.subscales = [
        {unit: "month", step: 1, date: "%F %Y"}
    ];
    gantt.config.scale_height = 60;
    
    // Configure task appearance
    gantt.config.row_height = 30;
    gantt.config.bar_height = 20;
    gantt.config.details_on_dblclick = false;
    gantt.config.show_links = true;
    gantt.config.highlight_critical_path = true;
    
    // Zoom levels
    gantt.ext.zoom.init({
        levels: [
            {
                name: "day",
                scale_height: 60,
                min_column_width: 80,
                scales: [
                    {unit: "month", step: 1, format: "%F %Y"},
                    {unit: "day", step: 1, format: "%d"}
                ]
            },
            {
                name: "week",
                scale_height: 60,
                min_column_width: 50,
                scales: [
                    {unit: "month", step: 1, format: "%F %Y"},
                    {unit: "week", step: 1, format: "Week %W"}
                ]
            },
            {
                name: "month",
                scale_height: 60,
                min_column_width: 120,
                scales: [
                    {unit: "year", step: 1, format: "%Y"},
                    {unit: "month", step: 1, format: "%M"}
                ]
            }
        ]
    });
    gantt.ext.zoom.setLevel("day");
    
    // Template for task class
    gantt.templates.task_class = function(start, end, task){
        if(task.is_critical){
            return "gantt_critical";
        }
        if(task.type == gantt.config.types.milestone){
            return "gantt_milestone";
        }
        return "";
    };
    
    // Tooltip template
    gantt.templates.tooltip_text = function(start, end, task) {
        return "<b>" + task.text + "</b><br/>" +
               "الكود: " + task.activity_code + "<br/>" +
               "المدة: " + task.duration + " أيام<br/>" +
               "البداية المبكرة: " + (task.early_start || 0) + "<br/>" +
               "النهاية المبكرة: " + (task.early_finish || 0) + "<br/>" +
               "Total Float: " + (task.total_float || 0) + "<br/>" +
               "الحالة: " + (task.is_critical ? '<span style="color: red; font-weight: bold;">حرج</span>' : 'عادي') + "<br/>" +
               "التكلفة: " + (task.estimated_cost ? task.estimated_cost.toLocaleString() + " ريال" : "غير محدد");
    };
    
    // Load data
    var tasks = @json([
        'data' => $activities->map(function($activity) {
            return [
                'id' => $activity->id,
                'text' => $activity->name,
                'activity_code' => $activity->activity_code,
                'start_date' => $activity->planned_start_date ? $activity->planned_start_date->format('Y-m-d') : now()->format('Y-m-d'),
                'duration' => $activity->duration_days,
                'progress' => 0,
                'type' => $activity->type == 'milestone' ? 'milestone' : 'task',
                'is_critical' => $activity->is_critical,
                'early_start' => $activity->early_start ?? 0,
                'early_finish' => $activity->early_finish ?? 0,
                'total_float' => $activity->total_float ?? 0,
                'estimated_cost' => $activity->estimated_cost ?? 0,
                'wbs_code' => $activity->wbs ? $activity->wbs->wbs_code : ''
            ];
        })->values(),
        'links' => $dependencies->map(function($dependency) {
            $typeMap = ['FS' => '0', 'SS' => '1', 'FF' => '2', 'SF' => '3'];
            return [
                'id' => $dependency->id,
                'source' => $dependency->predecessor_id,
                'target' => $dependency->successor_id,
                'type' => $typeMap[$dependency->type] ?? '0',
                'lag' => $dependency->lag_days ?? 0
            ];
        })->values()
    ]);
    
    // Initialize Gantt
    gantt.init("gantt_here");
    gantt.parse(tasks);
    
    // Add today marker
    var todayMarker = gantt.addMarker({
        start_date: new Date(),
        css: "today",
        text: "اليوم",
        title: "اليوم: " + gantt.date.date_to_str(gantt.config.task_date)(new Date())
    });
    
    // Toggle critical path filter
    var allTasks = [];
    
    function toggleCriticalPath() {
        var showCriticalOnly = document.getElementById('showCriticalOnly').checked;
        
        // Store all tasks on first filter
        if (allTasks.length === 0) {
            gantt.eachTask(function(task) {
                allTasks.push({
                    id: task.id,
                    data: Object.assign({}, task)
                });
            });
        }
        
        if (showCriticalOnly) {
            // Filter to show only critical tasks
            var criticalTasks = allTasks.filter(t => t.data.is_critical);
            var filteredData = {
                data: criticalTasks.map(t => t.data),
                links: tasks.links.filter(link => 
                    criticalTasks.some(t => t.id === link.source) && 
                    criticalTasks.some(t => t.id === link.target)
                )
            };
            gantt.clearAll();
            gantt.parse(filteredData);
        } else {
            // Show all tasks
            gantt.clearAll();
            gantt.parse(tasks);
        }
    }
    
    // Export to PNG
    function exportToPNG() {
        var tenderCode = @json($tender->tender_code);
        var tenderName = @json($tender->name);
        gantt.exportToPNG({
            name: "gantt_" + tenderCode + ".png",
            header: '<div style="text-align:center;font-size:18px;font-weight:bold;padding:10px;">مخطط جانت - ' + tenderName + '</div>',
            footer: '<div style="text-align:center;font-size:12px;padding:10px;">تم الإنشاء في: ' + new Date().toLocaleDateString('ar-SA') + '</div>'
        });
    }
    
    // Export to PDF
    function exportToPDF() {
        var tenderCode = @json($tender->tender_code);
        var tenderName = @json($tender->name);
        gantt.exportToPDF({
            name: "gantt_" + tenderCode + ".pdf",
            header: '<div style="text-align:center;font-size:18px;font-weight:bold;padding:10px;">مخطط جانت - ' + tenderName + '</div>',
            footer: '<div style="text-align:center;font-size:12px;padding:10px;">تم الإنشاء في: ' + new Date().toLocaleDateString('ar-SA') + '</div>',
            orientation: "landscape",
            zoom: 1
        });
    }
    
    // Prevent editing
    gantt.attachEvent("onBeforeDrag", function(){ return false; });
    gantt.attachEvent("onBeforeTaskDrag", function(){ return false; });
    gantt.attachEvent("onBeforeLinkAdd", function(){ return false; });
    gantt.attachEvent("onBeforeTaskChanged", function(){ return false; });
    gantt.attachEvent("onBeforeTaskDelete", function(){ return false; });
    gantt.attachEvent("onBeforeLinkDelete", function(){ return false; });
</script>

@push('scripts')
<style>
    .today {
        background-color: rgba(255, 0, 0, 0.1);
        opacity: 0.5;
    }
    
    .gantt_task_line.gantt_critical {
        background-color: #dc3545 !important;
        border-color: #bd2130 !important;
    }
    
    .gantt_link_arrow.gantt_critical {
        border-color: #dc3545 !important;
    }
</style>
@endpush

@endsection
