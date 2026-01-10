@extends('layouts.app')

@section('content')
<style>
    .pe-container {
        max-width: 1400px;
        margin: 0 auto;
    }
    
    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 30px;
    }
    
    .page-title {
        font-size: 1.8rem;
        font-weight: 700;
        color: #1d1d1f;
    }
    
    .btn-primary {
        background: #0071e3;
        color: white;
        padding: 12px 24px;
        border-radius: 8px;
        text-decoration: none;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.2s;
        border: none;
        cursor: pointer;
    }
    
    .btn-primary:hover {
        background: #0077ed;
    }
    
    .table-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 12px rgba(0,0,0,0.06);
        overflow: hidden;
    }
    
    table {
        width: 100%;
        border-collapse: collapse;
    }
    
    thead {
        background: #f5f5f7;
    }
    
    th {
        padding: 15px 20px;
        text-align: right;
        font-size: 0.85rem;
        font-weight: 600;
        color: #86868b;
        text-transform: uppercase;
    }
    
    td {
        padding: 20px;
        border-bottom: 1px solid #f5f5f7;
    }
    
    tbody tr:hover {
        background: #fafafa;
    }
    
    .calc-number {
        font-weight: 700;
        color: #0071e3;
        text-decoration: none;
    }
    
    .calc-number:hover {
        text-decoration: underline;
    }
    
    .status-badge {
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 600;
    }
    
    .status-calculated {
        background: #e0e7ff;
        color: #4338ca;
    }
    
    .status-pending_approval {
        background: #fef3c7;
        color: #92400e;
    }
    
    .status-approved {
        background: #d1f4dd;
        color: #047857;
    }
    
    .status-paid {
        background: #cffafe;
        color: #0e7490;
    }
    
    .status-rejected {
        background: #fee;
        color: #dc2626;
    }
    
    .percentage {
        font-weight: 600;
        font-size: 1.1rem;
    }
    
    .percentage.positive {
        color: #34c759;
    }
    
    .percentage.negative {
        color: #ff3b30;
    }
    
    .amount {
        font-weight: 600;
        color: #1d1d1f;
    }
    
    .threshold-indicator {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        font-size: 0.85rem;
    }
    
    .threshold-met {
        color: #34c759;
    }
    
    .threshold-not-met {
        color: #ff3b30;
    }
    
    .pagination {
        padding: 20px;
        display: flex;
        justify-content: center;
        gap: 10px;
    }
</style>

<div class="pe-container">
    <div class="page-header">
        <h1 class="page-title">حسابات فروقات الأسعار</h1>
        <a href="{{ route('price-escalation.calculate') }}" class="btn-primary">
            <i data-lucide="plus" style="width: 18px; height: 18px;"></i>
            حساب جديد
        </a>
    </div>
    
    @if(session('success'))
        <div style="background: #d1f4dd; color: #047857; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
            {{ session('success') }}
        </div>
    @endif
    
    <div class="table-card">
        <table>
            <thead>
                <tr>
                    <th>الرقم</th>
                    <th>المشروع</th>
                    <th>الفترة</th>
                    <th>المستخلص</th>
                    <th>نسبة التغير</th>
                    <th>المبلغ</th>
                    <th>العتبة</th>
                    <th>الحالة</th>
                    <th>التاريخ</th>
                </tr>
            </thead>
            <tbody>
                @forelse($calculations as $calc)
                    <tr>
                        <td>
                            <a href="{{ route('price-escalation.calculations.show', $calc) }}" class="calc-number">
                                {{ $calc->calculation_number }}
                            </a>
                        </td>
                        <td>
                            <strong>{{ $calc->contract->project->name }}</strong><br>
                            <small style="color: #86868b;">{{ $calc->contract->project->code }}</small>
                        </td>
                        <td>
                            {{ $calc->period_from->format('Y-m-d') }}<br>
                            <small style="color: #86868b;">إلى {{ $calc->period_to->format('Y-m-d') }}</small>
                        </td>
                        <td>
                            @if($calc->ipc)
                                {{ $calc->ipc->ipc_number }}
                            @else
                                <span style="color: #86868b;">--</span>
                            @endif
                        </td>
                        <td>
                            <span class="percentage {{ $calc->escalation_percentage >= 0 ? 'positive' : 'negative' }}">
                                {{ $calc->escalation_percentage > 0 ? '+' : '' }}{{ number_format($calc->escalation_percentage, 2) }}%
                            </span>
                        </td>
                        <td>
                            <div class="amount">{{ number_format($calc->escalation_amount, 0) }} د.أ</div>
                            <small style="color: #86868b;">من {{ number_format($calc->ipc_amount, 0) }}</small>
                        </td>
                        <td>
                            @if($calc->threshold_met)
                                <span class="threshold-indicator threshold-met">
                                    <i data-lucide="check-circle" style="width: 16px; height: 16px;"></i>
                                    نعم
                                </span>
                            @else
                                <span class="threshold-indicator threshold-not-met">
                                    <i data-lucide="x-circle" style="width: 16px; height: 16px;"></i>
                                    لا
                                </span>
                            @endif
                        </td>
                        <td>
                            <span class="status-badge status-{{ $calc->status }}">
                                @if($calc->status === 'calculated') محسوب
                                @elseif($calc->status === 'pending_approval') في الانتظار
                                @elseif($calc->status === 'approved') معتمد
                                @elseif($calc->status === 'paid') مدفوع
                                @else مرفوض
                                @endif
                            </span>
                        </td>
                        <td>
                            {{ $calc->calculation_date->format('Y-m-d') }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" style="text-align: center; padding: 40px; color: #86868b;">
                            لا توجد حسابات حتى الآن
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        
        <div class="pagination">
            {{ $calculations->links() }}
        </div>
    </div>
</div>

<script>
    lucide.createIcons();
</script>
@endsection
