@extends('layouts.app')

@section('content')
<div style="max-width: 1400px; margin: 0 auto;">
    <!-- Header -->
    <div style="margin-bottom: 30px;">
        <h1 style="font-size: 2rem; font-weight: 600; color: #1d1d1f; margin-bottom: 10px;">
            لوحة التحكم التنفيذية
        </h1>
        <p style="color: #86868b; font-size: 0.95rem;">
            نظرة شاملة على مؤشرات الأداء الرئيسية
        </p>
    </div>

    <!-- KPI Cards -->
    <div id="kpi-container" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 30px;">
        <!-- Loading state -->
        <div class="kpi-card" style="background: white; border-radius: 12px; padding: 24px; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
            <div style="height: 100px; display: flex; align-items: center; justify-content: center; color: #86868b;">
                جاري التحميل...
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(500px, 1fr)); gap: 20px; margin-bottom: 30px;">
        <!-- Revenue Trend Chart -->
        <div class="chart-card" style="background: white; border-radius: 12px; padding: 24px; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
            <h3 style="font-size: 1.1rem; font-weight: 600; margin-bottom: 20px; color: #1d1d1f;">
                اتجاه الإيرادات والمصروفات
            </h3>
            <canvas id="revenueTrendChart" style="max-height: 300px;"></canvas>
        </div>

        <!-- Project Status Chart -->
        <div class="chart-card" style="background: white; border-radius: 12px; padding: 24px; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
            <h3 style="font-size: 1.1rem; font-weight: 600; margin-bottom: 20px; color: #1d1d1f;">
                حالة المشاريع
            </h3>
            <canvas id="projectStatusChart" style="max-height: 300px;"></canvas>
        </div>

        <!-- Expense Breakdown Chart -->
        <div class="chart-card" style="background: white; border-radius: 12px; padding: 24px; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
            <h3 style="font-size: 1.1rem; font-weight: 600; margin-bottom: 20px; color: #1d1d1f;">
                تفصيل النفقات
            </h3>
            <canvas id="expenseBreakdownChart" style="max-height: 300px;"></canvas>
        </div>

        <!-- Cash Flow Chart -->
        <div class="chart-card" style="background: white; border-radius: 12px; padding: 24px; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
            <h3 style="font-size: 1.1rem; font-weight: 600; margin-bottom: 20px; color: #1d1d1f;">
                التدفق النقدي
            </h3>
            <canvas id="cashFlowChart" style="max-height: 300px;"></canvas>
        </div>
    </div>
</div>

<style>
    .kpi-card {
        transition: transform 0.2s, box-shadow 0.2s;
    }
    
    .kpi-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.12) !important;
    }

    .kpi-value {
        font-size: 2rem;
        font-weight: 700;
        color: #0071e3;
        margin: 10px 0;
    }

    .kpi-label {
        font-size: 0.9rem;
        color: #86868b;
        font-weight: 500;
    }

    .kpi-change {
        font-size: 0.85rem;
        margin-top: 8px;
    }

    .kpi-change.positive {
        color: #34c759;
    }

    .kpi-change.negative {
        color: #ff3b30;
    }
</style>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Fetch KPI data
    async function loadKPIs() {
        try {
            const response = await fetch('/api/kpis', {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });
            const result = await response.json();
            
            if (result.success) {
                renderKPIs(result.data);
            }
        } catch (error) {
            console.error('Error loading KPIs:', error);
        }
    }

    function renderKPIs(data) {
        const container = document.getElementById('kpi-container');
        
        const kpis = [
            { label: 'الإيرادات السنوية', value: formatCurrency(data.financial.yearly_revenue), icon: 'trending-up' },
            { label: 'الربح السنوي', value: formatCurrency(data.financial.yearly_profit), icon: 'dollar-sign' },
            { label: 'هامش الربح', value: data.financial.profit_margin + '%', icon: 'percent' },
            { label: 'الرصيد النقدي', value: formatCurrency(data.financial.cash_balance), icon: 'wallet' },
            { label: 'المشاريع النشطة', value: data.project.active_projects, icon: 'briefcase' },
            { label: 'متوسط التقدم', value: data.project.average_progress + '%', icon: 'activity' },
            { label: 'قيمة المخزون', value: formatCurrency(data.operational.inventory_value), icon: 'package' },
            { label: 'عدد الموظفين', value: data.hr.employee_count, icon: 'users' },
        ];

        container.innerHTML = kpis.map(kpi => `
            <div class="kpi-card" style="background: white; border-radius: 12px; padding: 24px; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 12px;">
                    <i data-lucide="${kpi.icon}" style="width: 24px; height: 24px; color: #0071e3;"></i>
                </div>
                <div class="kpi-value">${kpi.value}</div>
                <div class="kpi-label">${kpi.label}</div>
            </div>
        `).join('');

        lucide.createIcons();
    }

    function formatCurrency(value) {
        return new Intl.NumberFormat('ar-SA', {
            style: 'currency',
            currency: 'SAR',
            minimumFractionDigits: 0,
            maximumFractionDigits: 0,
        }).format(value);
    }

    // Load charts
    async function loadCharts() {
        await loadChart('revenueTrendChart', 'revenue-trend', 'line');
        await loadChart('projectStatusChart', 'project-status', 'pie');
        await loadChart('expenseBreakdownChart', 'expense-breakdown', 'pie');
        await loadChart('cashFlowChart', 'cash-flow', 'line');
    }

    async function loadChart(canvasId, chartType, type) {
        try {
            const response = await fetch(`/api/charts/${chartType}`, {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });
            const result = await response.json();
            
            if (result.success) {
                const ctx = document.getElementById(canvasId).getContext('2d');
                new Chart(ctx, {
                    type: type,
                    data: result.data,
                    options: {
                        responsive: true,
                        maintainAspectRatio: true,
                        plugins: {
                            legend: {
                                position: 'bottom',
                            }
                        }
                    }
                });
            }
        } catch (error) {
            console.error(`Error loading chart ${chartType}:`, error);
        }
    }

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
        loadKPIs();
        loadCharts();
    });
</script>
@endpush
@endsection
