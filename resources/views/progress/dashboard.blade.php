@extends('layouts.app')

@section('content')
<div style="padding: 40px; max-width: 1400px; margin: 0 auto;">
    <div style="margin-bottom: 30px;">
        <h1 style="margin-bottom: 10px;">لوحة متابعة التقدم - إدارة القيمة المكتسبة (EVM)</h1>
        
        <!-- Project Selector -->
        <form method="GET" style="margin-top: 20px;">
            <select name="project_id" onchange="this.form.submit()" style="padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif; min-width: 300px;">
                <option value="">اختر المشروع</option>
                @foreach($projects as $project)
                    <option value="{{ $project->id }}" {{ request('project_id') == $project->id ? 'selected' : '' }}>
                        {{ $project->name }}
                    </option>
                @endforeach
            </select>
        </form>
    </div>

    @if($selectedProject && $dashboardData && $dashboardData['snapshot'])
        @php
            $snapshot = $dashboardData['snapshot'];
            $healthStatus = $dashboardData['health_status'];
            $quickStats = $dashboardData['quick_stats'];
            $alerts = $dashboardData['alerts'];
        @endphp

        <!-- KPIs Section -->
        <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; margin-bottom: 30px;">
            <!-- Overall Progress -->
            <div style="background: white; padding: 25px; border-radius: 10px; text-align: center; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                <div style="font-size: 14px; color: #666; margin-bottom: 10px;">نسبة الإنجاز الإجمالية</div>
                <div style="font-size: 48px; font-weight: bold; color: #0071e3; margin-bottom: 5px;">
                    {{ number_format($snapshot->overall_progress_percent, 1) }}%
                </div>
                <div style="font-size: 12px; color: #999;">من الخطة: {{ number_format($snapshot->planned_progress_percent, 1) }}%</div>
            </div>

            <!-- SPI -->
            <div style="background: white; padding: 25px; border-radius: 10px; text-align: center; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                <div style="font-size: 14px; color: #666; margin-bottom: 10px;">مؤشر أداء الجدول (SPI)</div>
                <div style="font-size: 48px; font-weight: bold; color: {{ $healthStatus['schedule'] == 'green' ? '#28a745' : ($healthStatus['schedule'] == 'yellow' ? '#ffc107' : '#dc3545') }}; margin-bottom: 5px;">
                    {{ number_format($snapshot->schedule_performance_index_spi, 2) }}
                </div>
                <div style="font-size: 12px; color: #999;">{{ $healthStatus['spi_status'] }}</div>
            </div>

            <!-- CPI -->
            <div style="background: white; padding: 25px; border-radius: 10px; text-align: center; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                <div style="font-size: 14px; color: #666; margin-bottom: 10px;">مؤشر أداء التكلفة (CPI)</div>
                <div style="font-size: 48px; font-weight: bold; color: {{ $healthStatus['cost'] == 'green' ? '#28a745' : ($healthStatus['cost'] == 'yellow' ? '#ffc107' : '#dc3545') }}; margin-bottom: 5px;">
                    {{ number_format($snapshot->cost_performance_index_cpi, 2) }}
                </div>
                <div style="font-size: 12px; color: #999;">{{ $healthStatus['cpi_status'] }}</div>
            </div>

            <!-- Budget Health -->
            <div style="background: white; padding: 25px; border-radius: 10px; text-align: center; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                <div style="font-size: 14px; color: #666; margin-bottom: 10px;">الميزانية المنفقة</div>
                <div style="font-size: 48px; font-weight: bold; color: {{ $quickStats['budget_spent_percent'] > 100 ? '#dc3545' : '#0071e3' }}; margin-bottom: 5px;">
                    {{ number_format($quickStats['budget_spent_percent'], 1) }}%
                </div>
                <div style="font-size: 12px; color: #999;">{{ number_format($snapshot->actual_cost_ac, 0) }} من {{ number_format($snapshot->budget_at_completion_bac, 0) }}</div>
            </div>
        </div>

        <!-- Charts Row -->
        <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 20px; margin-bottom: 30px;">
            <!-- S-Curve Chart -->
            <div style="background: white; padding: 25px; border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                <h3 style="margin-bottom: 20px;">منحنى S (PV, EV, AC)</h3>
                <canvas id="sCurveChart" height="100"></canvas>
            </div>

            <!-- Performance Indexes Trend -->
            <div style="background: white; padding: 25px; border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                <h3 style="margin-bottom: 20px;">مؤشرات الأداء</h3>
                <canvas id="performanceChart" height="150"></canvas>
            </div>
        </div>

        <!-- Variance Charts -->
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 30px;">
            <div style="background: white; padding: 25px; border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                <h3 style="margin-bottom: 20px;">انحراف الجدول (SV)</h3>
                <canvas id="scheduleVarianceChart" height="80"></canvas>
            </div>

            <div style="background: white; padding: 25px; border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                <h3 style="margin-bottom: 20px;">انحراف التكلفة (CV)</h3>
                <canvas id="costVarianceChart" height="80"></canvas>
            </div>
        </div>

        <!-- Alerts and Quick Stats -->
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 30px;">
            <!-- Alerts -->
            <div style="background: white; padding: 25px; border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                <h3 style="margin-bottom: 20px;">التنبيهات</h3>
                
                @if($alerts['critical_delayed'] > 0)
                    <div style="padding: 15px; background: #fee; border-right: 4px solid #dc3545; margin-bottom: 10px; border-radius: 5px;">
                        <strong>تحذير حرج:</strong> {{ $alerts['critical_delayed'] }} نشاط حرج متأخر
                    </div>
                @endif

                @if($alerts['delayed_activities'] > 0)
                    <div style="padding: 15px; background: #fff3cd; border-right: 4px solid #ffc107; margin-bottom: 10px; border-radius: 5px;">
                        <strong>تأخير:</strong> {{ $alerts['delayed_activities'] }} نشاط متأخر عن الجدول
                    </div>
                @endif

                @if($alerts['over_budget_activities'] > 0)
                    <div style="padding: 15px; background: #fff3cd; border-right: 4px solid #ffc107; margin-bottom: 10px; border-radius: 5px;">
                        <strong>تجاوز ميزانية:</strong> {{ $alerts['over_budget_activities'] }} نشاط تجاوز الميزانية
                    </div>
                @endif

                @if($snapshot->cost_variance_cv < 0)
                    <div style="padding: 15px; background: #fee; border-right: 4px solid #dc3545; margin-bottom: 10px; border-radius: 5px;">
                        <strong>انحراف تكلفة:</strong> {{ number_format(abs($snapshot->cost_variance_cv), 0) }} تجاوز في التكلفة
                    </div>
                @endif

                @if($alerts['delayed_activities'] == 0 && $alerts['over_budget_activities'] == 0)
                    <div style="padding: 15px; background: #d4edda; border-right: 4px solid #28a745; border-radius: 5px;">
                        <strong>ممتاز!</strong> لا توجد تنبيهات حالياً
                    </div>
                @endif
            </div>

            <!-- Quick Stats -->
            <div style="background: white; padding: 25px; border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                <h3 style="margin-bottom: 20px;">إحصائيات سريعة</h3>
                
                <div style="margin-bottom: 15px; padding-bottom: 15px; border-bottom: 1px solid #eee;">
                    <div style="font-size: 12px; color: #666; margin-bottom: 5px;">تاريخ الإنجاز المتوقع</div>
                    <div style="font-size: 18px; font-weight: 600;">{{ $snapshot->forecasted_completion_date->format('Y-m-d') }}</div>
                    <div style="font-size: 11px; color: #999;">الخطة: {{ $snapshot->planned_completion_date->format('Y-m-d') }}</div>
                </div>

                <div style="margin-bottom: 15px; padding-bottom: 15px; border-bottom: 1px solid #eee;">
                    <div style="font-size: 12px; color: #666; margin-bottom: 5px;">التكلفة المتوقعة عند الإنجاز (EAC)</div>
                    <div style="font-size: 18px; font-weight: 600;">{{ number_format($snapshot->estimate_at_completion_eac, 0) }}</div>
                    <div style="font-size: 11px; color: #999;">الميزانية: {{ number_format($snapshot->budget_at_completion_bac, 0) }}</div>
                </div>

                <div style="margin-bottom: 15px; padding-bottom: 15px; border-bottom: 1px solid #eee;">
                    <div style="font-size: 12px; color: #666; margin-bottom: 5px;">التكلفة المتبقية للإنجاز (ETC)</div>
                    <div style="font-size: 18px; font-weight: 600;">{{ number_format($snapshot->estimate_to_complete_etc, 0) }}</div>
                </div>

                <div>
                    <div style="font-size: 12px; color: #666; margin-bottom: 5px;">انحراف عند الإنجاز (VAC)</div>
                    <div style="font-size: 18px; font-weight: 600; color: {{ $snapshot->variance_at_completion_vac >= 0 ? '#28a745' : '#dc3545' }};">
                        {{ number_format($snapshot->variance_at_completion_vac, 0) }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div style="display: flex; gap: 15px; justify-content: center;">
            <a href="{{ route('progress.update.create', $selectedProject) }}" style="background: #0071e3; color: white; padding: 12px 30px; border-radius: 8px; text-decoration: none; font-weight: 600;">تحديث التقدم</a>
            <a href="{{ route('progress.variance-analysis.index', $selectedProject) }}" style="background: #6c757d; color: white; padding: 12px 30px; border-radius: 8px; text-decoration: none; font-weight: 600;">تحليل الانحرافات</a>
            <a href="{{ route('progress.forecasting.index', $selectedProject) }}" style="background: #17a2b8; color: white; padding: 12px 30px; border-radius: 8px; text-decoration: none; font-weight: 600;">التنبؤ</a>
        </div>

    @elseif($selectedProject)
        <div style="background: white; padding: 40px; border-radius: 10px; text-align: center; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
            <p style="font-size: 18px; color: #666; margin-bottom: 20px;">لا توجد بيانات تقدم لهذا المشروع بعد</p>
            <a href="{{ route('progress.update.create', $selectedProject) }}" style="background: #0071e3; color: white; padding: 12px 30px; border-radius: 8px; text-decoration: none; font-weight: 600;">إضافة تقرير تقدم</a>
        </div>
    @else
        <div style="background: white; padding: 40px; border-radius: 10px; text-align: center; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
            <p style="font-size: 18px; color: #666;">اختر مشروعاً لعرض لوحة المتابعة</p>
        </div>
    @endif
</div>

@if($selectedProject && $dashboardData && $dashboardData['snapshot'])
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js" 
        integrity="sha384-FhvOKqP1KVBd2/+KvKJrNZFXrR+QQyBfJwP7wTp+0H3bKhIJQzCr7HHRqJwGN5tU" 
        crossorigin="anonymous"></script>
<script>
const trendData = @json($dashboardData['trend_data']);

// S-Curve Chart
new Chart(document.getElementById('sCurveChart'), {
    type: 'line',
    data: {
        labels: trendData.dates,
        datasets: [
            {
                label: 'PV (القيمة المخططة)',
                data: trendData.pv,
                borderColor: '#6c757d',
                backgroundColor: 'rgba(108, 117, 125, 0.1)',
                tension: 0.4
            },
            {
                label: 'EV (القيمة المكتسبة)',
                data: trendData.ev,
                borderColor: '#28a745',
                backgroundColor: 'rgba(40, 167, 69, 0.1)',
                tension: 0.4
            },
            {
                label: 'AC (التكلفة الفعلية)',
                data: trendData.ac,
                borderColor: '#dc3545',
                backgroundColor: 'rgba(220, 53, 69, 0.1)',
                tension: 0.4
            }
        ]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: {
                display: true,
                position: 'bottom'
            }
        }
    }
});

// Performance Indexes Chart
new Chart(document.getElementById('performanceChart'), {
    type: 'line',
    data: {
        labels: trendData.dates,
        datasets: [
            {
                label: 'SPI',
                data: trendData.spi,
                borderColor: '#0071e3',
                backgroundColor: 'rgba(0, 113, 227, 0.1)',
                tension: 0.4
            },
            {
                label: 'CPI',
                data: trendData.cpi,
                borderColor: '#17a2b8',
                backgroundColor: 'rgba(23, 162, 184, 0.1)',
                tension: 0.4
            }
        ]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: {
                display: true,
                position: 'bottom'
            }
        },
        scales: {
            y: {
                beginAtZero: false,
                min: 0.5,
                max: 1.5
            }
        }
    }
});

// Schedule Variance Chart
new Chart(document.getElementById('scheduleVarianceChart'), {
    type: 'bar',
    data: {
        labels: trendData.dates,
        datasets: [{
            label: 'SV (انحراف الجدول)',
            data: trendData.sv,
            backgroundColor: trendData.sv.map(v => v >= 0 ? 'rgba(40, 167, 69, 0.7)' : 'rgba(220, 53, 69, 0.7)')
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: {
                display: false
            }
        }
    }
});

// Cost Variance Chart
new Chart(document.getElementById('costVarianceChart'), {
    type: 'bar',
    data: {
        labels: trendData.dates,
        datasets: [{
            label: 'CV (انحراف التكلفة)',
            data: trendData.cv,
            backgroundColor: trendData.cv.map(v => v >= 0 ? 'rgba(40, 167, 69, 0.7)' : 'rgba(220, 53, 69, 0.7)')
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: {
                display: false
            }
        }
    }
});
</script>
@endif
@endsection
