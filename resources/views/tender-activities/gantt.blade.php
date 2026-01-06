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
    
    .gantt_critical_task {
        background-color: #ff6b6b !important;
        border-color: #ff0000 !important;
    }
    
    .gantt_critical_task .gantt_task_progress {
        background-color: #cc0000 !important;
    }
</style>

<link href="https://cdn.dhtmlx.com/gantt/edge/dhtmlx-gantt.css" rel="stylesheet">

<div class="breadcrumb">
    <a href="{{ route('dashboard') }}">الرئيسية</a> / 
    <a href="{{ route('tenders.index') }}">العطاءات</a> / 
    <a href="{{ route('tenders.show', $tender) }}">{{ $tender->name }}</a> / 
    <a href="{{ route('tender-activities.index', $tender) }}">الأنشطة</a> / 
    <span>مخطط جانت</span>
</div>

<div class="page-header">
    <div>
        <h1 class="page-title">مخطط جانت - {{ $tender->name }}</h1>
        <p style="color: #6c757d; margin-top: 8px;">رمز العطاء: {{ $tender->tender_code }}</p>
    </div>
    <div style="display: flex; gap: 12px;">
        <button onclick="exportGantt('png')" class="btn btn-secondary">
            <i data-lucide="image"></i>
            تصدير PNG
        </button>
        <button onclick="exportGantt('pdf')" class="btn btn-secondary">
            <i data-lucide="file-text"></i>
            تصدير PDF
        </button>
        <a href="{{ route('tender-activities.index', $tender) }}" class="btn btn-outline-secondary">
            <i data-lucide="arrow-right"></i>
            رجوع
        </a>
    </div>
</div>

<div class="card">
    <div id="gantt_here" style="width:100%; height:600px;"></div>
</div>

<script src="https://cdn.dhtmlx.com/gantt/edge/dhtmlx-gantt.js"></script>
<script>
    lucide.createIcons();
    
    gantt.config.date_format = "%Y-%m-%d";
    gantt.config.rtl = true;
    gantt.config.readonly = true;
    
    // Configure columns
    gantt.config.columns = [
        {name: "text", label: "النشاط", tree: true, width: "*"},
        {name: "duration", label: "المدة", align: "center", width: 70},
        {name: "start_date", label: "البداية", align: "center", width: 100},
        {name: "is_critical", label: "حرج؟", align: "center", width: 60, template: function(task) {
            return task.is_critical ? '<span style="background:#dc3545;color:white;padding:4px 8px;border-radius:4px;font-size:0.75rem;">حرج</span>' : '';
        }}
    ];
    
    // Prepare data
    var tasks = {
        data: [
            @foreach($activities as $activity)
            {
                id: {{ $activity->id }},
                text: "{{ $activity->activity_code }} - {{ $activity->name }}",
                start_date: "{{ $activity->planned_start_date ? $activity->planned_start_date->format('Y-m-d') : now()->format('Y-m-d') }}",
                duration: {{ $activity->duration_days }},
                progress: 0,
                is_critical: {{ $activity->is_critical ? 'true' : 'false' }},
                parent: {{ $activity->tender_wbs_id ?? 0 }}
            },
            @endforeach
        ],
        links: [
            @foreach($dependencies as $dep)
            {
                id: {{ $dep->id }},
                source: {{ $dep->predecessor_id }},
                target: {{ $dep->successor_id }},
                type: "{{ $dep->type == 'FS' ? '0' : ($dep->type == 'SS' ? '1' : ($dep->type == 'FF' ? '2' : '3')) }}"
            },
            @endforeach
        ]
    };
    
    // Template for critical path
    gantt.templates.task_class = function(start, end, task){
        if(task.is_critical){
            return "gantt_critical_task";
        }
        return "";
    };
    
    gantt.init("gantt_here");
    gantt.parse(tasks);
    
    // Export functions
    function exportGantt(type) {
        if(type === 'png') {
            gantt.exportToPNG({
                name: "gantt_{{ $tender->tender_code }}.png"
            });
        } else if(type === 'pdf') {
            gantt.exportToPDF({
                name: "gantt_{{ $tender->tender_code }}.pdf"
            });
        }
    }
</script>
@endsection
