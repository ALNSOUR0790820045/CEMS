@extends('layouts.app')

@section('content')
<style>
    .page-header {
        background: white;
        border-radius: 12px;
        padding: 25px 30px;
        margin-bottom: 25px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.04);
    }
    
    .page-title {
        font-size: 1.75rem;
        font-weight: 700;
        color: #1d1d1f;
        margin: 0 0 5px 0;
    }
    
    .breadcrumb {
        display: flex;
        gap: 8px;
        align-items: center;
        font-size: 0.85rem;
        color: #86868b;
    }
    
    .breadcrumb a {
        color: #0071e3;
        text-decoration: none;
    }
    
    .cost-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }
    
    .cost-card {
        background: white;
        border-radius: 12px;
        padding: 20px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.04);
        border-left: 4px solid;
    }
    
    .cost-card.materials { border-left-color: #3b82f6; }
    .cost-card.labor { border-left-color: #10b981; }
    .cost-card.equipment { border-left-color: #f59e0b; }
    .cost-card.subcontract { border-left-color: #8b5cf6; }
    .cost-card.overhead { border-left-color: #ef4444; }
    
    .cost-label {
        font-size: 0.85rem;
        color: #86868b;
        margin-bottom: 8px;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    
    .cost-value {
        font-size: 1.8rem;
        font-weight: 700;
        color: #1d1d1f;
    }
    
    .cost-subtitle {
        font-size: 0.85rem;
        color: #6b7280;
        margin-top: 5px;
    }
    
    .chart-container {
        background: white;
        border-radius: 12px;
        padding: 25px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.04);
        margin-bottom: 25px;
    }
    
    .chart-title {
        font-size: 1.2rem;
        font-weight: 700;
        margin-bottom: 20px;
    }
    
    .pie-chart {
        max-width: 400px;
        margin: 0 auto;
    }
    
    .breakdown-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
    }
    
    .breakdown-table th {
        text-align: right;
        padding: 12px;
        background: #f9fafb;
        font-size: 0.9rem;
        font-weight: 600;
        border-bottom: 2px solid #e5e7eb;
    }
    
    .breakdown-table td {
        padding: 12px;
        border-bottom: 1px solid #f3f4f6;
    }
    
    .progress-bar {
        height: 8px;
        background: #f3f4f6;
        border-radius: 4px;
        overflow: hidden;
    }
    
    .progress-fill {
        height: 100%;
        background: linear-gradient(90deg, #0071e3, #00c4cc);
        border-radius: 4px;
        transition: width 0.3s;
    }
</style>

<div class="page-header">
    <h1 class="page-title">تحليل التكلفة</h1>
    <div class="breadcrumb">
        <a href="{{ route('boq.index') }}">جداول الكميات</a>
        <span>/</span>
        <a href="{{ route('boq.show', $boq) }}">{{ $boq->boq_number }}</a>
        <span>/</span>
        <span>تحليل التكلفة</span>
    </div>
</div>

<div class="cost-grid">
    <div class="cost-card materials">
        <div class="cost-label">
            <i data-lucide="package" style="width: 16px; height: 16px;"></i>
            تكلفة المواد
        </div>
        <div class="cost-value">{{ number_format($materialCost, 2) }}</div>
        <div class="cost-subtitle">{{ $boq->currency }}</div>
    </div>
    
    <div class="cost-card labor">
        <div class="cost-label">
            <i data-lucide="users" style="width: 16px; height: 16px;"></i>
            تكلفة العمالة
        </div>
        <div class="cost-value">{{ number_format($laborCost, 2) }}</div>
        <div class="cost-subtitle">{{ $boq->currency }}</div>
    </div>
    
    <div class="cost-card equipment">
        <div class="cost-label">
            <i data-lucide="truck" style="width: 16px; height: 16px;"></i>
            تكلفة المعدات
        </div>
        <div class="cost-value">{{ number_format($equipmentCost, 2) }}</div>
        <div class="cost-subtitle">{{ $boq->currency }}</div>
    </div>
    
    <div class="cost-card subcontract">
        <div class="cost-label">
            <i data-lucide="handshake" style="width: 16px; height: 16px;"></i>
            تكلفة المقاولين
        </div>
        <div class="cost-value">{{ number_format($subcontractCost, 2) }}</div>
        <div class="cost-subtitle">{{ $boq->currency }}</div>
    </div>
    
    <div class="cost-card overhead">
        <div class="cost-label">
            <i data-lucide="briefcase" style="width: 16px; height: 16px;"></i>
            التكاليف الإدارية
        </div>
        <div class="cost-value">{{ number_format($overheadCost, 2) }}</div>
        <div class="cost-subtitle">{{ $boq->currency }}</div>
    </div>
</div>

<div class="chart-container">
    <h3 class="chart-title">توزيع التكاليف</h3>
    
    @php
        $total = $materialCost + $laborCost + $equipmentCost + $subcontractCost + $overheadCost;
        $materialPercent = $total > 0 ? ($materialCost / $total) * 100 : 0;
        $laborPercent = $total > 0 ? ($laborCost / $total) * 100 : 0;
        $equipmentPercent = $total > 0 ? ($equipmentCost / $total) * 100 : 0;
        $subcontractPercent = $total > 0 ? ($subcontractCost / $total) * 100 : 0;
        $overheadPercent = $total > 0 ? ($overheadCost / $total) * 100 : 0;
    @endphp
    
    <table class="breakdown-table">
        <thead>
            <tr>
                <th>نوع التكلفة</th>
                <th>المبلغ</th>
                <th>النسبة</th>
                <th style="width: 200px;">التوزيع</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    <span style="display: inline-block; width: 12px; height: 12px; background: #3b82f6; border-radius: 2px; margin-left: 8px;"></span>
                    المواد
                </td>
                <td style="font-weight: 600;">{{ number_format($materialCost, 2) }} {{ $boq->currency }}</td>
                <td>{{ number_format($materialPercent, 1) }}%</td>
                <td>
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: {{ $materialPercent }}%; background: #3b82f6;"></div>
                    </div>
                </td>
            </tr>
            <tr>
                <td>
                    <span style="display: inline-block; width: 12px; height: 12px; background: #10b981; border-radius: 2px; margin-left: 8px;"></span>
                    العمالة
                </td>
                <td style="font-weight: 600;">{{ number_format($laborCost, 2) }} {{ $boq->currency }}</td>
                <td>{{ number_format($laborPercent, 1) }}%</td>
                <td>
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: {{ $laborPercent }}%; background: #10b981;"></div>
                    </div>
                </td>
            </tr>
            <tr>
                <td>
                    <span style="display: inline-block; width: 12px; height: 12px; background: #f59e0b; border-radius: 2px; margin-left: 8px;"></span>
                    المعدات
                </td>
                <td style="font-weight: 600;">{{ number_format($equipmentCost, 2) }} {{ $boq->currency }}</td>
                <td>{{ number_format($equipmentPercent, 1) }}%</td>
                <td>
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: {{ $equipmentPercent }}%; background: #f59e0b;"></div>
                    </div>
                </td>
            </tr>
            <tr>
                <td>
                    <span style="display: inline-block; width: 12px; height: 12px; background: #8b5cf6; border-radius: 2px; margin-left: 8px;"></span>
                    المقاولين
                </td>
                <td style="font-weight: 600;">{{ number_format($subcontractCost, 2) }} {{ $boq->currency }}</td>
                <td>{{ number_format($subcontractPercent, 1) }}%</td>
                <td>
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: {{ $subcontractPercent }}%; background: #8b5cf6;"></div>
                    </div>
                </td>
            </tr>
            <tr>
                <td>
                    <span style="display: inline-block; width: 12px; height: 12px; background: #ef4444; border-radius: 2px; margin-left: 8px;"></span>
                    التكاليف الإدارية
                </td>
                <td style="font-weight: 600;">{{ number_format($overheadCost, 2) }} {{ $boq->currency }}</td>
                <td>{{ number_format($overheadPercent, 1) }}%</td>
                <td>
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: {{ $overheadPercent }}%; background: #ef4444;"></div>
                    </div>
                </td>
            </tr>
            <tr style="border-top: 2px solid #1d1d1f;">
                <td style="font-weight: 700;">الإجمالي</td>
                <td style="font-weight: 700; font-size: 1.1rem;">{{ number_format($total, 2) }} {{ $boq->currency }}</td>
                <td style="font-weight: 700;">100%</td>
                <td></td>
            </tr>
        </tbody>
    </table>
</div>

<script>
    lucide.createIcons();
</script>
@endsection
