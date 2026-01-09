@extends('layouts.app')

@section('content')
<div style="padding: 80px 20px 40px; max-width: 1400px; margin: 0 auto;">
    <div style="margin-bottom: 30px;">
        <h1 style="font-size: 32px; font-weight: 700; margin-bottom: 10px;">تقرير الكتاب المفتوح</h1>
        <p style="color: #666; font-size: 16px;">محاسبة شفافة 100% مع توثيق كامل</p>
    </div>

    <!-- Contract Selection -->
    <div style="background: white; border-radius: 12px; padding: 24px; margin-bottom: 24px; box-shadow: 0 2px 8px rgba(0,0,0,0.06);">
        <form method="GET" action="{{ route('cost-plus.open-book-report') }}">
            <label style="display: block; margin-bottom: 8px; font-weight: 600;">اختر العقد</label>
            <div style="display: flex; gap: 12px;">
                <select name="contract_id" required style="flex: 1; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-family: 'Cairo', sans-serif;">
                    <option value="">اختر عقد Cost Plus</option>
                    @foreach($contracts as $contract)
                        <option value="{{ $contract->id }}" {{ request('contract_id') == $contract->id ? 'selected' : '' }}>
                            {{ $contract->contract->contract_number }} - {{ $contract->project->name }}
                        </option>
                    @endforeach
                </select>
                <button type="submit" style="background: var(--accent); color: white; padding: 12px 32px; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; font-family: 'Cairo', sans-serif;">
                    عرض التقرير
                </button>
            </div>
        </form>
    </div>

    @if(isset($data))
    <!-- Summary Statistics -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 30px;">
        <div style="background: white; border-radius: 12px; padding: 24px; box-shadow: 0 2px 8px rgba(0,0,0,0.06);">
            <div style="color: #666; font-size: 14px; margin-bottom: 8px;">إجمالي التكاليف</div>
            <div style="font-size: 32px; font-weight: 700; color: var(--accent);">
                {{ number_format($data['total_costs'], 2) }}
            </div>
        </div>

        <div style="background: white; border-radius: 12px; padding: 24px; box-shadow: 0 2px 8px rgba(0,0,0,0.06);">
            <div style="color: #666; font-size: 14px; margin-bottom: 8px;">إجمالي المفوتر</div>
            <div style="font-size: 32px; font-weight: 700; color: #28a745;">
                {{ number_format($data['total_invoiced'], 2) }}
            </div>
        </div>

        <div style="background: white; border-radius: 12px; padding: 24px; box-shadow: 0 2px 8px rgba(0,0,0,0.06);">
            <div style="color: #666; font-size: 14px; margin-bottom: 8px;">نسبة التوثيق</div>
            <div style="font-size: 32px; font-weight: 700; color: {{ $data['documentation_rate'] >= 80 ? '#28a745' : '#ffc107' }};">
                {{ number_format($data['documentation_rate'], 1) }}%
            </div>
        </div>
    </div>

    <!-- Cost Breakdown by Type -->
    <div style="background: white; border-radius: 12px; padding: 32px; margin-bottom: 24px; box-shadow: 0 2px 8px rgba(0,0,0,0.06);">
        <h2 style="font-size: 22px; font-weight: 700; margin-bottom: 24px;">تفصيل التكاليف حسب النوع</h2>
        
        <div style="display: grid; gap: 16px;">
            @foreach($data['transactions_by_type'] as $type => $details)
            <div style="border: 1px solid #e0e0e0; border-radius: 8px; padding: 20px;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                    <div>
                        <h3 style="font-size: 18px; font-weight: 600; margin-bottom: 4px;">
                            @switch($type)
                                @case('material') مواد @break
                                @case('labor') عمالة @break
                                @case('equipment') معدات @break
                                @case('subcontract') مقاولي باطن @break
                                @case('overhead') مصاريف غير مباشرة @break
                                @case('other') أخرى @break
                            @endswitch
                        </h3>
                        <p style="color: #666; font-size: 14px;">{{ $details['count'] }} معاملة</p>
                    </div>
                    <div style="text-align: left;">
                        <div style="font-size: 24px; font-weight: 700; color: var(--accent);">
                            {{ number_format($details['total'], 2) }}
                        </div>
                    </div>
                </div>
                
                <!-- Progress bar showing percentage of total -->
                @php
                    $percentage = ($details['total'] / max($data['total_costs'], 1)) * 100;
                @endphp
                <div style="background: #f0f0f0; height: 8px; border-radius: 4px; overflow: hidden;">
                    <div style="background: var(--accent); height: 100%; width: {{ $percentage }}%; transition: width 0.3s;"></div>
                </div>
                <div style="color: #666; font-size: 13px; margin-top: 4px;">
                    {{ number_format($percentage, 1) }}% من الإجمالي
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <!-- Documentation Status -->
    <div style="background: white; border-radius: 12px; padding: 32px; box-shadow: 0 2px 8px rgba(0,0,0,0.06);">
        <h2 style="font-size: 22px; font-weight: 700; margin-bottom: 16px;">حالة التوثيق</h2>
        <div style="background: #e8f5e9; border-right: 4px solid #28a745; padding: 16px; border-radius: 4px;">
            <div style="font-size: 16px; margin-bottom: 8px;">
                <strong>محاسبة الكتاب المفتوح (Open Book Accounting)</strong>
            </div>
            <p style="color: #666; font-size: 14px; line-height: 1.6;">
                جميع التكاليف موثقة بالمستندات الأربعة المطلوبة:
                <span style="margin-right: 12px;">✓ الفاتورة الأصلية</span>
                <span style="margin-right: 12px;">✓ إيصال الدفع</span>
                <span style="margin-right: 12px;">✓ إشعار استلام البضائع (GRN)</span>
                <span style="margin-right: 12px;">✓ صورة مع GPS + Timestamp</span>
            </p>
        </div>
    </div>
    @endif
</div>
@endsection
