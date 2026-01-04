@extends('layouts.app')

@section('content')
<div style="max-width: 1400px; margin: 0 auto;">
    <!-- Header -->
    <div style="margin-bottom: 30px;">
        <h1 style="font-size: 2rem; font-weight: 600; color: #1d1d1f; margin-bottom: 10px;">
            لوحة تحكم المشروع
        </h1>
        <p style="color: #86868b; font-size: 0.95rem;">
            تفاصيل ومؤشرات أداء المشروع
        </p>
    </div>

    <!-- Project Selector -->
    <div style="background: white; border-radius: 12px; padding: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); margin-bottom: 30px;">
        <label style="display: block; margin-bottom: 10px; font-weight: 500; color: #1d1d1f;">اختر المشروع:</label>
        <select id="projectSelector" style="width: 100%; padding: 12px; border: 1px solid #d2d2d7; border-radius: 8px; font-family: 'Cairo', sans-serif;">
            <option value="">-- اختر مشروع --</option>
        </select>
    </div>

    <!-- Project Info Card -->
    <div id="projectInfo" style="display: none; background: white; border-radius: 12px; padding: 24px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); margin-bottom: 30px;">
        <h2 id="projectName" style="font-size: 1.5rem; font-weight: 600; color: #1d1d1f; margin-bottom: 20px;"></h2>
        
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px;">
            <div>
                <div style="color: #86868b; font-size: 0.85rem; margin-bottom: 5px;">الحالة</div>
                <div id="projectStatus" style="font-size: 1.1rem; font-weight: 600;"></div>
            </div>
            <div>
                <div style="color: #86868b; font-size: 0.85rem; margin-bottom: 5px;">العميل</div>
                <div id="projectClient" style="font-size: 1.1rem; font-weight: 600;"></div>
            </div>
            <div>
                <div style="color: #86868b; font-size: 0.85rem; margin-bottom: 5px;">الموقع</div>
                <div id="projectLocation" style="font-size: 1.1rem; font-weight: 600;"></div>
            </div>
            <div>
                <div style="color: #86868b; font-size: 0.85rem; margin-bottom: 5px;">نسبة الإنجاز</div>
                <div id="projectProgress" style="font-size: 1.1rem; font-weight: 600; color: #0071e3;"></div>
            </div>
        </div>
    </div>

    <!-- EVM Metrics -->
    <div id="evmMetrics" style="display: none; margin-bottom: 30px;">
        <h3 style="font-size: 1.3rem; font-weight: 600; margin-bottom: 20px; color: #1d1d1f;">
            مؤشرات إدارة القيمة المكتسبة (EVM)
        </h3>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px;">
            <div class="kpi-card" style="background: white; border-radius: 12px; padding: 24px; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
                <div style="color: #86868b; font-size: 0.9rem; margin-bottom: 8px;">القيمة المخططة (PV)</div>
                <div id="pvValue" class="kpi-value" style="font-size: 1.8rem; font-weight: 700; color: #0071e3;"></div>
            </div>
            <div class="kpi-card" style="background: white; border-radius: 12px; padding: 24px; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
                <div style="color: #86868b; font-size: 0.9rem; margin-bottom: 8px;">القيمة المكتسبة (EV)</div>
                <div id="evValue" class="kpi-value" style="font-size: 1.8rem; font-weight: 700; color: #34c759;"></div>
            </div>
            <div class="kpi-card" style="background: white; border-radius: 12px; padding: 24px; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
                <div style="color: #86868b; font-size: 0.9rem; margin-bottom: 8px;">التكلفة الفعلية (AC)</div>
                <div id="acValue" class="kpi-value" style="font-size: 1.8rem; font-weight: 700; color: #ff9500;"></div>
            </div>
            <div class="kpi-card" style="background: white; border-radius: 12px; padding: 24px; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
                <div style="color: #86868b; font-size: 0.9rem; margin-bottom: 8px;">مؤشر أداء الجدول (SPI)</div>
                <div id="spiValue" class="kpi-value" style="font-size: 1.8rem; font-weight: 700; color: #0071e3;"></div>
            </div>
            <div class="kpi-card" style="background: white; border-radius: 12px; padding: 24px; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
                <div style="color: #86868b; font-size: 0.9rem; margin-bottom: 8px;">مؤشر أداء التكلفة (CPI)</div>
                <div id="cpiValue" class="kpi-value" style="font-size: 1.8rem; font-weight: 700; color: #34c759;"></div>
            </div>
        </div>
    </div>

    <!-- Budget vs Actual -->
    <div id="budgetSection" style="display: none;">
        <h3 style="font-size: 1.3rem; font-weight: 600; margin-bottom: 20px; color: #1d1d1f;">
            الميزانية مقابل الفعلي
        </h3>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px; margin-bottom: 30px;">
            <div style="background: white; border-radius: 12px; padding: 24px; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
                <h4 style="color: #86868b; font-size: 0.9rem; margin-bottom: 8px;">الميزانية</h4>
                <div id="budgetValue" style="font-size: 1.8rem; font-weight: 700; color: #0071e3;"></div>
            </div>
            <div style="background: white; border-radius: 12px; padding: 24px; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
                <h4 style="color: #86868b; font-size: 0.9rem; margin-bottom: 8px;">التكلفة الفعلية</h4>
                <div id="actualCostValue" style="font-size: 1.8rem; font-weight: 700; color: #ff9500;"></div>
            </div>
            <div style="background: white; border-radius: 12px; padding: 24px; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
                <h4 style="color: #86868b; font-size: 0.9rem; margin-bottom: 8px;">الميزانية المتبقية</h4>
                <div id="remainingBudget" style="font-size: 1.8rem; font-weight: 700; color: #34c759;"></div>
            </div>
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
</style>

@push('scripts')
<script>
    let projects = [];

    // Fetch projects list
    async function loadProjects() {
        try {
            const response = await fetch('/api/kpis', {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });
            const result = await response.json();
            
            if (result.success) {
                // For now, we'll fetch project data separately
                // In a real app, you'd have a projects endpoint
                populateProjectSelector();
            }
        } catch (error) {
            console.error('Error loading projects:', error);
        }
    }

    function populateProjectSelector() {
        // Mock projects - in real app, fetch from API
        const selector = document.getElementById('projectSelector');
        
        // Add mock options (you'd fetch real projects from API)
        for (let i = 1; i <= 4; i++) {
            const option = document.createElement('option');
            option.value = i;
            option.textContent = `Project ${i}`;
            selector.appendChild(option);
        }
    }

    // Load project details
    async function loadProjectDetails(projectId) {
        try {
            const response = await fetch(`/api/dashboard/project/${projectId}`, {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });
            const result = await response.json();
            
            if (result.success) {
                displayProjectDetails(result.data);
            }
        } catch (error) {
            console.error('Error loading project details:', error);
        }
    }

    function displayProjectDetails(data) {
        // Show sections
        document.getElementById('projectInfo').style.display = 'block';
        document.getElementById('evmMetrics').style.display = 'block';
        document.getElementById('budgetSection').style.display = 'block';

        // Project info
        document.getElementById('projectName').textContent = data.project.name;
        document.getElementById('projectStatus').textContent = data.project.status;
        document.getElementById('projectClient').textContent = data.project.client_name || 'N/A';
        document.getElementById('projectLocation').textContent = data.project.location || 'N/A';
        document.getElementById('projectProgress').textContent = data.progress + '%';

        // EVM metrics
        document.getElementById('pvValue').textContent = formatCurrency(data.planned_value);
        document.getElementById('evValue').textContent = formatCurrency(data.earned_value);
        document.getElementById('acValue').textContent = formatCurrency(data.actual_cost);
        document.getElementById('spiValue').textContent = data.spi;
        document.getElementById('cpiValue').textContent = data.cpi;

        // Budget
        document.getElementById('budgetValue').textContent = formatCurrency(data.budget);
        document.getElementById('actualCostValue').textContent = formatCurrency(data.actual_cost);
        document.getElementById('remainingBudget').textContent = formatCurrency(data.budget_remaining);
    }

    function formatCurrency(value) {
        return new Intl.NumberFormat('ar-SA', {
            style: 'currency',
            currency: 'SAR',
            minimumFractionDigits: 0,
            maximumFractionDigits: 0,
        }).format(value);
    }

    // Event listeners
    document.addEventListener('DOMContentLoaded', function() {
        loadProjects();
        
        document.getElementById('projectSelector').addEventListener('change', function(e) {
            if (e.target.value) {
                loadProjectDetails(e.target.value);
            } else {
                document.getElementById('projectInfo').style.display = 'none';
                document.getElementById('evmMetrics').style.display = 'none';
                document.getElementById('budgetSection').style.display = 'none';
            }
        });
    });
</script>
@endpush
@endsection
