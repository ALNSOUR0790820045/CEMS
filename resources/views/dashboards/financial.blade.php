@extends('layouts.app')

@section('content')
<div style="max-width: 1400px; margin: 0 auto;">
    <!-- Header -->
    <div style="margin-bottom: 30px;">
        <h1 style="font-size: 2rem; font-weight: 600; color: #1d1d1f; margin-bottom: 10px;">
            لوحة التحكم المالية
        </h1>
        <p style="color: #86868b; font-size: 0.95rem;">
            نظرة شاملة على الأداء المالي
        </p>
    </div>

    <!-- Financial KPIs -->
    <div id="financial-kpis" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 30px;">
        <!-- Loading state -->
        <div style="background: white; border-radius: 12px; padding: 24px; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
            <div style="height: 100px; display: flex; align-items: center; justify-content: center; color: #86868b;">
                جاري التحميل...
            </div>
        </div>
    </div>

    <!-- Charts Grid -->
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 30px;">
        <!-- Revenue Trend -->
        <div style="background: white; border-radius: 12px; padding: 24px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); grid-column: 1 / -1;">
            <h3 style="font-size: 1.1rem; font-weight: 600; margin-bottom: 20px; color: #1d1d1f;">
                اتجاه الإيرادات والمصروفات (آخر 12 شهر)
            </h3>
            <canvas id="revenueTrendChart" style="max-height: 300px;"></canvas>
        </div>

        <!-- Revenue by Project -->
        <div style="background: white; border-radius: 12px; padding: 24px; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
            <h3 style="font-size: 1.1rem; font-weight: 600; margin-bottom: 20px; color: #1d1d1f;">
                الإيرادات حسب المشروع
            </h3>
            <canvas id="revenueByProjectChart"></canvas>
        </div>

        <!-- Expense Breakdown -->
        <div style="background: white; border-radius: 12px; padding: 24px; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
            <h3 style="font-size: 1.1rem; font-weight: 600; margin-bottom: 20px; color: #1d1d1f;">
                تفصيل المصروفات
            </h3>
            <canvas id="expenseBreakdownChart"></canvas>
        </div>

        <!-- Cash Flow -->
        <div style="background: white; border-radius: 12px; padding: 24px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); grid-column: 1 / -1;">
            <h3 style="font-size: 1.1rem; font-weight: 600; margin-bottom: 20px; color: #1d1d1f;">
                التدفق النقدي التراكمي
            </h3>
            <canvas id="cashFlowChart" style="max-height: 300px;"></canvas>
        </div>
    </div>

    <!-- P&L Summary -->
    <div style="background: white; border-radius: 12px; padding: 24px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); margin-bottom: 30px;">
        <h3 style="font-size: 1.1rem; font-weight: 600; margin-bottom: 20px; color: #1d1d1f;">
            ملخص الأرباح والخسائر (السنة الحالية)
        </h3>
        <div id="pl-summary" style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px;">
            <div>
                <div style="color: #86868b; font-size: 0.85rem; margin-bottom: 5px;">إجمالي الإيرادات</div>
                <div id="totalRevenue" style="font-size: 1.5rem; font-weight: 700; color: #34c759;"></div>
            </div>
            <div>
                <div style="color: #86868b; font-size: 0.85rem; margin-bottom: 5px;">إجمالي المصروفات</div>
                <div id="totalExpenses" style="font-size: 1.5rem; font-weight: 700; color: #ff3b30;"></div>
            </div>
            <div>
                <div style="color: #86868b; font-size: 0.85rem; margin-bottom: 5px;">صافي الربح</div>
                <div id="netProfit" style="font-size: 1.5rem; font-weight: 700; color: #0071e3;"></div>
            </div>
        </div>
    </div>

    <!-- AR/AP Summary -->
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
        <div style="background: white; border-radius: 12px; padding: 24px; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
            <h3 style="font-size: 1.1rem; font-weight: 600; margin-bottom: 15px; color: #1d1d1f;">
                الذمم المدينة (AR)
            </h3>
            <div style="display: flex; align-items: center; gap: 15px;">
                <i data-lucide="trending-up" style="width: 40px; height: 40px; color: #34c759;"></i>
                <div>
                    <div id="arAmount" style="font-size: 1.8rem; font-weight: 700; color: #34c759;"></div>
                    <div style="color: #86868b; font-size: 0.85rem;">مستحقات معلقة</div>
                </div>
            </div>
        </div>

        <div style="background: white; border-radius: 12px; padding: 24px; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
            <h3 style="font-size: 1.1rem; font-weight: 600; margin-bottom: 15px; color: #1d1d1f;">
                الذمم الدائنة (AP)
            </h3>
            <div style="display: flex; align-items: center; gap: 15px;">
                <i data-lucide="trending-down" style="width: 40px; height: 40px; color: #ff3b30;"></i>
                <div>
                    <div id="apAmount" style="font-size: 1.8rem; font-weight: 700; color: #ff3b30;"></div>
                    <div style="color: #86868b; font-size: 0.85rem;">مدفوعات معلقة</div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Fetch financial data
    async function loadFinancialData() {
        try {
            const response = await fetch('/api/dashboard/financial', {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });
            const result = await response.json();
            
            if (result.success) {
                renderFinancialKPIs(result.data.financial_kpis);
            }
        } catch (error) {
            console.error('Error loading financial data:', error);
        }
    }

    function renderFinancialKPIs(data) {
        const container = document.getElementById('financial-kpis');
        
        const kpis = [
            { label: 'الإيرادات الشهرية', value: formatCurrency(data.monthly_revenue), icon: 'dollar-sign', color: '#34c759' },
            { label: 'المصروفات الشهرية', value: formatCurrency(data.monthly_expenses), icon: 'credit-card', color: '#ff3b30' },
            { label: 'الربح الشهري', value: formatCurrency(data.monthly_profit), icon: 'trending-up', color: '#0071e3' },
            { label: 'هامش الربح', value: data.profit_margin + '%', icon: 'percent', color: '#ff9500' },
            { label: 'الرصيد النقدي', value: formatCurrency(data.cash_balance), icon: 'wallet', color: '#34c759' },
        ];

        container.innerHTML = kpis.map(kpi => `
            <div style="background: white; border-radius: 12px; padding: 24px; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 12px;">
                    <i data-lucide="${kpi.icon}" style="width: 24px; height: 24px; color: ${kpi.color};"></i>
                </div>
                <div style="font-size: 1.8rem; font-weight: 700; color: ${kpi.color}; margin: 10px 0;">
                    ${kpi.value}
                </div>
                <div style="font-size: 0.9rem; color: #86868b; font-weight: 500;">
                    ${kpi.label}
                </div>
            </div>
        `).join('');

        // P&L Summary
        document.getElementById('totalRevenue').textContent = formatCurrency(data.yearly_revenue);
        document.getElementById('totalExpenses').textContent = formatCurrency(data.yearly_expenses);
        document.getElementById('netProfit').textContent = formatCurrency(data.yearly_profit);

        // AR/AP
        document.getElementById('arAmount').textContent = formatCurrency(data.accounts_receivable);
        document.getElementById('apAmount').textContent = formatCurrency(data.accounts_payable);

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
        await loadChart('revenueByProjectChart', 'revenue-by-project', 'bar');
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

    // Initialize
    document.addEventListener('DOMContentLoaded', function() {
        loadFinancialData();
        loadCharts();
    });
</script>
@endpush
@endsection
