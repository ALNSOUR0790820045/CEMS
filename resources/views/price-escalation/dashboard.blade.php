@extends('layouts.app')

@section('content')
<style>
    .pe-dashboard {
        max-width: 1600px;
        margin: 0 auto;
    }
    
    .pe-header {
        background: linear-gradient(135deg, #0071e3, #00c4cc);
        color: white;
        padding: 40px;
        border-radius: 16px;
        margin-bottom: 30px;
        box-shadow: 0 10px 40px rgba(0, 113, 227, 0.2);
    }
    
    .pe-header h1 {
        font-size: 2rem;
        font-weight: 700;
        margin: 0 0 10px 0;
    }
    
    .pe-header p {
        font-size: 1rem;
        opacity: 0.9;
        margin: 0;
    }
    
    .kpi-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }
    
    .kpi-card {
        background: white;
        padding: 30px;
        border-radius: 12px;
        box-shadow: 0 2px 12px rgba(0,0,0,0.06);
        transition: all 0.3s;
    }
    
    .kpi-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 24px rgba(0,0,0,0.12);
    }
    
    .kpi-label {
        font-size: 0.85rem;
        color: #86868b;
        margin-bottom: 10px;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    
    .kpi-value {
        font-size: 2.2rem;
        font-weight: 700;
        color: #1d1d1f;
        margin-bottom: 8px;
    }
    
    .kpi-change {
        font-size: 0.85rem;
        display: flex;
        align-items: center;
        gap: 5px;
    }
    
    .kpi-change.positive {
        color: #34c759;
    }
    
    .kpi-change.negative {
        color: #ff3b30;
    }
    
    .chart-container {
        background: white;
        padding: 30px;
        border-radius: 12px;
        box-shadow: 0 2px 12px rgba(0,0,0,0.06);
        margin-bottom: 30px;
    }
    
    .chart-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 25px;
    }
    
    .chart-title {
        font-size: 1.25rem;
        font-weight: 600;
        color: #1d1d1f;
    }
    
    .chart-subtitle {
        font-size: 0.85rem;
        color: #86868b;
    }
    
    canvas {
        max-height: 350px;
    }
</style>

<div class="pe-dashboard">
    <div class="pe-header">
        <h1>لوحة فروقات الأسعار</h1>
        <p>نظام حساب فروقات الأسعار التلقائي وفقاً لمؤشرات دائرة الإحصاءات العامة (DSI)</p>
    </div>
    
    <!-- KPIs -->
    <div class="kpi-grid">
        <div class="kpi-card">
            <div class="kpi-label">
                <i data-lucide="trending-up" style="width: 18px; height: 18px;"></i>
                إجمالي فروقات الأسعار
            </div>
            <div class="kpi-value">{{ number_format($totalEscalation, 0) }} د.أ</div>
            <div class="kpi-change positive">
                <i data-lucide="arrow-up" style="width: 16px; height: 16px;"></i>
                إجمالي المبلغ المحسوب
            </div>
        </div>
        
        <div class="kpi-card">
            <div class="kpi-label">
                <i data-lucide="check-circle" style="width: 18px; height: 18px;"></i>
                المطالبات المعتمدة
            </div>
            <div class="kpi-value">{{ number_format($approvedClaims, 0) }} د.أ</div>
            <div class="kpi-change">
                المبلغ الموافق عليه
            </div>
        </div>
        
        <div class="kpi-card">
            <div class="kpi-label">
                <i data-lucide="package" style="width: 18px; height: 18px;"></i>
                نسبة التغير في المواد
            </div>
            <div class="kpi-value {{ $materialsChange >= 0 ? 'positive' : 'negative' }}">
                {{ number_format($materialsChange, 2) }}%
            </div>
            <div class="kpi-change {{ $materialsChange >= 0 ? 'positive' : 'negative' }}">
                <i data-lucide="{{ $materialsChange >= 0 ? 'arrow-up' : 'arrow-down' }}" style="width: 16px; height: 16px;"></i>
                @if($latestDsi)
                    {{ $latestDsi->year }}/{{ $latestDsi->month }}
                @endif
            </div>
        </div>
        
        <div class="kpi-card">
            <div class="kpi-label">
                <i data-lucide="users" style="width: 18px; height: 18px;"></i>
                نسبة التغير في العمالة
            </div>
            <div class="kpi-value {{ $laborChange >= 0 ? 'positive' : 'negative' }}">
                {{ number_format($laborChange, 2) }}%
            </div>
            <div class="kpi-change {{ $laborChange >= 0 ? 'positive' : 'negative' }}">
                <i data-lucide="{{ $laborChange >= 0 ? 'arrow-up' : 'arrow-down' }}" style="width: 16px; height: 16px;"></i>
                @if($latestDsi)
                    {{ $latestDsi->year }}/{{ $latestDsi->month }}
                @endif
            </div>
        </div>
    </div>
    
    <!-- DSI Trend Chart -->
    <div class="chart-container">
        <div class="chart-header">
            <div>
                <div class="chart-title">اتجاه مؤشرات DSI</div>
                <div class="chart-subtitle">مواد البناء مقابل الأجور - آخر 12 شهر</div>
            </div>
        </div>
        <canvas id="dsiTrendChart"></canvas>
    </div>
    
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
        <!-- Escalation Over Time -->
        <div class="chart-container">
            <div class="chart-header">
                <div>
                    <div class="chart-title">فروقات الأسعار عبر الوقت</div>
                    <div class="chart-subtitle">المبالغ المحسوبة شهرياً</div>
                </div>
            </div>
            <canvas id="escalationOverTimeChart"></canvas>
        </div>
        
        <!-- Claimed vs Paid -->
        <div class="chart-container">
            <div class="chart-header">
                <div>
                    <div class="chart-title">المطالبات المعتمدة مقابل المدفوعة</div>
                    <div class="chart-subtitle">حالة المطالبات</div>
                </div>
            </div>
            <canvas id="claimedVsPaidChart"></canvas>
        </div>
    </div>
    
    <!-- Quick Actions -->
    <div style="margin-top: 30px; display: flex; gap: 15px; justify-content: center;">
        <a href="{{ route('price-escalation.calculate') }}" 
           style="background: #0071e3; color: white; padding: 15px 30px; border-radius: 8px; text-decoration: none; font-weight: 600; display: flex; align-items: center; gap: 10px; transition: all 0.2s;">
            <i data-lucide="calculator" style="width: 20px; height: 20px;"></i>
            حساب فروقات جديدة
        </a>
        <a href="{{ route('price-escalation.dsi-indices') }}" 
           style="background: white; color: #0071e3; padding: 15px 30px; border-radius: 8px; text-decoration: none; font-weight: 600; display: flex; align-items: center; gap: 10px; border: 2px solid #0071e3; transition: all 0.2s;">
            <i data-lucide="trending-up" style="width: 20px; height: 20px;"></i>
            إدارة مؤشرات DSI
        </a>
        <a href="{{ route('price-escalation.index') }}" 
           style="background: white; color: #0071e3; padding: 15px 30px; border-radius: 8px; text-decoration: none; font-weight: 600; display: flex; align-items: center; gap: 10px; border: 2px solid #0071e3; transition: all 0.2s;">
            <i data-lucide="file-text" style="width: 20px; height: 20px;"></i>
            العقود
        </a>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    lucide.createIcons();
    
    // DSI Trend Chart
    const dsiTrendData = @json($dsiTrend);
    const dsiTrendCtx = document.getElementById('dsiTrendChart').getContext('2d');
    new Chart(dsiTrendCtx, {
        type: 'line',
        data: {
            labels: dsiTrendData.map(d => `${d.year}/${d.month}`),
            datasets: [{
                label: 'مؤشر المواد',
                data: dsiTrendData.map(d => d.materials_index),
                borderColor: '#0071e3',
                backgroundColor: 'rgba(0, 113, 227, 0.1)',
                tension: 0.4,
                fill: true
            }, {
                label: 'مؤشر العمالة',
                data: dsiTrendData.map(d => d.labor_index),
                borderColor: '#00c4cc',
                backgroundColor: 'rgba(0, 196, 204, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    position: 'top',
                    rtl: true
                }
            },
            scales: {
                y: {
                    beginAtZero: false
                }
            }
        }
    });
    
    // Escalation Over Time
    const escalationData = @json($escalationOverTime);
    const escalationCtx = document.getElementById('escalationOverTimeChart').getContext('2d');
    new Chart(escalationCtx, {
        type: 'bar',
        data: {
            labels: escalationData.map(d => d.month),
            datasets: [{
                label: 'فروقات الأسعار (د.أ)',
                data: escalationData.map(d => d.total),
                backgroundColor: '#0071e3',
                borderRadius: 6
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
    
    // Claimed vs Paid
    const claimedVsPaidData = @json($claimedVsPaid);
    const claimedVsPaidCtx = document.getElementById('claimedVsPaidChart').getContext('2d');
    new Chart(claimedVsPaidCtx, {
        type: 'doughnut',
        data: {
            labels: claimedVsPaidData.map(d => d.status === 'approved' ? 'معتمد' : 'مدفوع'),
            datasets: [{
                data: claimedVsPaidData.map(d => d.total),
                backgroundColor: ['#00c4cc', '#34c759'],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    position: 'bottom',
                    rtl: true
                }
            }
        }
    });
</script>
@endsection
