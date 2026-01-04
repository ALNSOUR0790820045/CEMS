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
    
    .btn-small {
        padding: 8px 16px;
        font-size: 0.85rem;
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
    
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 16px;
        margin-bottom: 24px;
    }
    
    .stat-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 20px;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
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
        margin-bottom: 8px;
    }
    
    .stat-label {
        font-size: 0.9rem;
        opacity: 0.95;
    }
    
    .critical-path-section {
        background: linear-gradient(135deg, #ff6b6b 0%, #ee5a6f 100%);
        color: white;
        padding: 20px;
        border-radius: 12px;
        margin-bottom: 24px;
    }
    
    .critical-path-title {
        font-size: 1.25rem;
        font-weight: 700;
        margin-bottom: 16px;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .critical-activities {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }
    
    .activity-badge {
        background: rgba(255, 255, 255, 0.2);
        padding: 8px 16px;
        border-radius: 20px;
        font-size: 0.85rem;
        font-weight: 600;
        backdrop-filter: blur(10px);
    }
    
    #network-diagram {
        height: 500px;
        background: white;
        border: 1px solid #ddd;
        border-radius: 8px;
        margin-bottom: 24px;
    }
    
    .filters {
        display: flex;
        gap: 12px;
        margin-bottom: 20px;
        flex-wrap: wrap;
    }
    
    .filter-input {
        padding: 8px 12px;
        border: 1px solid #ddd;
        border-radius: 6px;
        font-size: 0.9rem;
        min-width: 200px;
    }
    
    .table {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.9rem;
    }
    
    .table thead {
        background: #f8f9fa;
        position: sticky;
        top: 0;
        z-index: 10;
    }
    
    .table th {
        padding: 12px 8px;
        text-align: center;
        font-weight: 600;
        color: #495057;
        border-bottom: 2px solid #dee2e6;
        white-space: nowrap;
    }
    
    .table td {
        padding: 10px 8px;
        border-bottom: 1px solid #dee2e6;
        text-align: center;
    }
    
    .table tbody tr:hover {
        background: #f8f9fa;
    }
    
    .table tbody tr.critical-row {
        background: #fff5f5;
    }
    
    .badge {
        display: inline-block;
        padding: 4px 12px;
        border-radius: 12px;
        font-size: 0.75rem;
        font-weight: 600;
    }
    
    .badge-critical {
        background: #dc3545;
        color: white;
    }
    
    .badge-success {
        background: #28a745;
        color: white;
    }
    
    .badge-warning {
        background: #ffc107;
        color: #000;
    }
    
    .table-container {
        overflow-x: auto;
        max-height: 600px;
        overflow-y: auto;
    }
    
    .section-title {
        font-size: 1.25rem;
        font-weight: 700;
        margin-bottom: 16px;
        color: var(--text);
        display: flex;
        align-items: center;
        gap: 10px;
    }
</style>

<div class="breadcrumb">
    <a href="{{ route('dashboard') }}">الرئيسية</a> / 
    <a href="{{ route('tenders.index') }}">العطاءات</a> / 
    <a href="{{ route('tenders.show', $tender) }}">{{ $tender->name }}</a> / 
    <a href="{{ route('tender-activities.index', $tender) }}">الأنشطة</a> / 
    <span>تحليل CPM</span>
</div>

<div class="page-header">
    <div>
        <h1 class="page-title">تحليل المسار الحرج (CPM): {{ $tender->name }}</h1>
        <p style="color: #6c757d; margin-top: 8px;">رمز العطاء: {{ $tender->tender_code }}</p>
    </div>
    <div style="display: flex; gap: 12px;">
        <form method="POST" action="{{ route('tender-activities.recalculate-cpm', $tender) }}" style="display: inline;">
            @csrf
            <button type="submit" class="btn btn-success">
                <i data-lucide="refresh-cw"></i>
                إعادة الحساب
            </button>
        </form>
        <a href="{{ route('tender-activities.gantt', $tender) }}" class="btn btn-primary">
            <i data-lucide="gantt-chart"></i>
            مخطط جانت
        </a>
        <a href="{{ route('tender-activities.index', $tender) }}" class="btn btn-secondary">
            <i data-lucide="list"></i>
            قائمة الأنشطة
        </a>
    </div>
</div>

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-value">{{ $cpmResult['project_duration'] ?? 0 }}</div>
        <div class="stat-label">إجمالي مدة المشروع (أيام)</div>
    </div>
    <div class="stat-card">
        <div class="stat-value">{{ $activities->count() }}</div>
        <div class="stat-label">إجمالي الأنشطة</div>
    </div>
    <div class="stat-card">
        <div class="stat-value">{{ $activities->where('is_critical', true)->count() }}</div>
        <div class="stat-label">الأنشطة الحرجة</div>
    </div>
    <div class="stat-card">
        <div class="stat-value">{{ $activities->where('is_critical', false)->count() }}</div>
        <div class="stat-label">الأنشطة غير الحرجة</div>
    </div>
</div>

<div class="critical-path-section">
    <div class="critical-path-title">
        <i data-lucide="alert-circle" style="width: 24px; height: 24px;"></i>
        المسار الحرج (Critical Path)
    </div>
    @if($criticalActivities->isNotEmpty())
        <div class="critical-activities">
            @foreach($criticalActivities as $activity)
                <div class="activity-badge" title="{{ $activity->name }}">
                    {{ $activity->activity_code }}
                </div>
            @endforeach
        </div>
    @else
        <p style="opacity: 0.9;">لا توجد أنشطة حرجة في الوقت الحالي.</p>
    @endif
</div>

<div class="card">
    <div class="section-title">
        <i data-lucide="git-branch"></i>
        مخطط الشبكة (Network Diagram)
    </div>
    <div id="network-diagram"></div>
</div>

<div class="card">
    <div class="section-title">
        <i data-lucide="table"></i>
        جدول تحليل الأنشطة
    </div>
    
    <form method="GET" action="{{ route('tender-activities.cpm-analysis', $tender) }}" id="filterForm">
        <div class="filters">
            <input type="text" name="search" class="filter-input" placeholder="بحث في الأنشطة..." value="{{ request('search') }}">
            
            <select name="is_critical" class="filter-input" onchange="document.getElementById('filterForm').submit()">
                <option value="">كل الأنشطة</option>
                <option value="1" {{ request('is_critical') == '1' ? 'selected' : '' }}>حرجة فقط</option>
                <option value="0" {{ request('is_critical') == '0' ? 'selected' : '' }}>غير حرجة فقط</option>
            </select>
            
            <button type="submit" class="btn btn-primary btn-small">بحث</button>
        </div>
    </form>
    
    <div class="table-container">
        <table class="table">
            <thead>
                <tr>
                    <th>الكود</th>
                    <th>اسم النشاط</th>
                    <th>المدة<br>(أيام)</th>
                    <th>البداية<br>المبكرة (ES)</th>
                    <th>النهاية<br>المبكرة (EF)</th>
                    <th>البداية<br>المتأخرة (LS)</th>
                    <th>النهاية<br>المتأخرة (LF)</th>
                    <th>Total<br>Float</th>
                    <th>Free<br>Float</th>
                    <th>حرج؟</th>
                </tr>
            </thead>
            <tbody>
                @forelse($activities as $activity)
                <tr class="{{ $activity->is_critical ? 'critical-row' : '' }}">
                    <td><strong>{{ $activity->activity_code }}</strong></td>
                    <td style="text-align: right; max-width: 250px;">{{ $activity->name }}</td>
                    <td>{{ $activity->duration_days }}</td>
                    <td>{{ $activity->early_start ?? 0 }}</td>
                    <td>{{ $activity->early_finish ?? 0 }}</td>
                    <td>{{ $activity->late_start ?? 0 }}</td>
                    <td>{{ $activity->late_finish ?? 0 }}</td>
                    <td>
                        @if(($activity->total_float ?? 0) == 0)
                            <span style="color: #dc3545; font-weight: bold;">0</span>
                        @elseif(($activity->total_float ?? 0) < 5)
                            <span style="color: #ffc107; font-weight: bold;">{{ $activity->total_float }}</span>
                        @else
                            <span style="color: #28a745;">{{ $activity->total_float }}</span>
                        @endif
                    </td>
                    <td>{{ $activity->free_float ?? 0 }}</td>
                    <td>
                        @if($activity->is_critical)
                            <span class="badge badge-critical">نعم</span>
                        @else
                            <span class="badge badge-success">لا</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="10" style="text-align: center; padding: 40px; color: #6c757d;">
                        لا توجد أنشطة للعرض.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Include vis.js for network diagram -->
<link href="https://unpkg.com/vis-network/styles/vis-network.min.css" rel="stylesheet">
<script src="https://unpkg.com/vis-network/standalone/umd/vis-network.min.js"></script>

<script>
    lucide.createIcons();
    
    // Network diagram data
    var networkData = @json($networkData);
    
    // Create nodes for vis.js
    var nodes = new vis.DataSet(
        networkData.nodes.map(function(node) {
            return {
                id: node.id,
                label: node.label + '\n' + node.duration + ' أيام',
                title: node.name + '\nES: ' + node.early_start + ' | EF: ' + node.early_finish + 
                       '\nLS: ' + node.late_start + ' | LF: ' + node.late_finish + 
                       '\nTF: ' + node.total_float,
                color: {
                    background: node.is_critical ? '#dc3545' : '#0071e3',
                    border: node.is_critical ? '#bd2130' : '#005bb5',
                    highlight: {
                        background: node.is_critical ? '#c82333' : '#0056b3',
                        border: node.is_critical ? '#a71d2a' : '#004085'
                    }
                },
                font: {
                    color: 'white',
                    size: 12,
                    bold: node.is_critical
                },
                borderWidth: node.is_critical ? 3 : 2,
                shadow: true
            };
        })
    );
    
    // Create edges for vis.js
    var edges = new vis.DataSet(
        networkData.edges.map(function(edge) {
            var edgeTypeLabel = '';
            switch(edge.type) {
                case 'FS': edgeTypeLabel = 'FS'; break;
                case 'SS': edgeTypeLabel = 'SS'; break;
                case 'FF': edgeTypeLabel = 'FF'; break;
                case 'SF': edgeTypeLabel = 'SF'; break;
            }
            
            return {
                from: edge.from,
                to: edge.to,
                arrows: 'to',
                label: edgeTypeLabel + (edge.lag ? '\n(' + edge.lag + ')' : ''),
                color: {
                    color: '#999',
                    highlight: '#333',
                    hover: '#333'
                },
                font: {
                    size: 10,
                    align: 'middle'
                },
                smooth: {
                    type: 'cubicBezier',
                    forceDirection: 'horizontal',
                    roundness: 0.4
                }
            };
        })
    );
    
    // Container
    var container = document.getElementById('network-diagram');
    
    // Options
    var options = {
        layout: {
            hierarchical: {
                direction: 'LR',
                sortMethod: 'directed',
                levelSeparation: 200,
                nodeSpacing: 150,
                treeSpacing: 200
            }
        },
        physics: {
            enabled: false
        },
        interaction: {
            hover: true,
            zoomView: true,
            dragView: true
        },
        nodes: {
            shape: 'box',
            margin: 10,
            widthConstraint: {
                minimum: 100,
                maximum: 150
            }
        },
        edges: {
            width: 2,
            smooth: {
                enabled: true,
                type: 'cubicBezier'
            }
        }
    };
    
    // Initialize network
    var network = new vis.Network(container, {nodes: nodes, edges: edges}, options);
    
    // Fit network to screen
    network.once('stabilizationIterationsDone', function() {
        network.fit({
            animation: {
                duration: 1000,
                easingFunction: 'easeInOutQuad'
            }
        });
    });
    
    // Click event to show activity details
    network.on('click', function(params) {
        if (params.nodes.length > 0) {
            var nodeId = params.nodes[0];
            var node = networkData.nodes.find(n => n.id === nodeId);
            if (node) {
                alert('النشاط: ' + node.name + '\n' +
                      'الكود: ' + node.label + '\n' +
                      'المدة: ' + node.duration + ' أيام\n' +
                      'Early Start: ' + node.early_start + '\n' +
                      'Early Finish: ' + node.early_finish + '\n' +
                      'Late Start: ' + node.late_start + '\n' +
                      'Late Finish: ' + node.late_finish + '\n' +
                      'Total Float: ' + node.total_float + '\n' +
                      'حرج: ' + (node.is_critical ? 'نعم' : 'لا'));
            }
        }
    });
</script>

@endsection
