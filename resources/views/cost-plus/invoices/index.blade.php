@extends('layouts.app')

@section('content')
<div style="padding: 80px 20px 40px; max-width: 1400px; margin: 0 auto;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
        <div>
            <h1 style="font-size: 32px; font-weight: 700; margin-bottom: 10px;">فواتير Cost Plus</h1>
            <p style="color: #666; font-size: 16px;">إدارة وإنشاء فواتير التكلفة + الربح</p>
        </div>
        <button onclick="document.getElementById('generateModal').style.display='block'" style="background: var(--accent); color: white; padding: 12px 24px; border-radius: 8px; border: none; font-weight: 600; cursor: pointer; font-family: 'Cairo', sans-serif;">
            + إنشاء فاتورة جديدة
        </button>
    </div>

    <div style="background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.06);">
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="background: #f8f9fa; border-bottom: 2px solid #dee2e6;">
                    <th style="padding: 16px; text-align: right; font-weight: 600;">رقم الفاتورة</th>
                    <th style="padding: 16px; text-align: right; font-weight: 600;">المشروع</th>
                    <th style="padding: 16px; text-align: right; font-weight: 600;">تاريخ الفاتورة</th>
                    <th style="padding: 16px; text-align: right; font-weight: 600;">الفترة</th>
                    <th style="padding: 16px; text-align: right; font-weight: 600;">التكاليف المباشرة</th>
                    <th style="padding: 16px; text-align: right; font-weight: 600;">الربح</th>
                    <th style="padding: 16px; text-align: right; font-weight: 600;">المجموع</th>
                    <th style="padding: 16px; text-align: center; font-weight: 600;">الحالة</th>
                    <th style="padding: 16px; text-align: center; font-weight: 600;">الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                @forelse($invoices as $invoice)
                <tr style="border-bottom: 1px solid #dee2e6;">
                    <td style="padding: 16px; font-weight: 600;">{{ $invoice->invoice_number }}</td>
                    <td style="padding: 16px;">{{ $invoice->project->name }}</td>
                    <td style="padding: 16px;">{{ $invoice->invoice_date->format('Y-m-d') }}</td>
                    <td style="padding: 16px; font-size: 14px;">
                        {{ $invoice->period_from->format('Y-m-d') }}<br>
                        إلى {{ $invoice->period_to->format('Y-m-d') }}
                    </td>
                    <td style="padding: 16px; font-weight: 600;">{{ number_format($invoice->total_direct_costs, 2) }}</td>
                    <td style="padding: 16px; color: #28a745; font-weight: 600;">{{ number_format($invoice->fee_amount, 2) }}</td>
                    <td style="padding: 16px; font-weight: 700; color: var(--accent);">{{ number_format($invoice->total_amount, 2) }}</td>
                    <td style="padding: 16px; text-align: center;">
                        @switch($invoice->status)
                            @case('draft')
                                <span style="background: #e2e3e5; color: #41464b; padding: 4px 12px; border-radius: 12px; font-size: 13px;">مسودة</span>
                                @break
                            @case('submitted')
                                <span style="background: #cfe2ff; color: #084298; padding: 4px 12px; border-radius: 12px; font-size: 13px;">مقدمة</span>
                                @break
                            @case('approved')
                                <span style="background: #d1e7dd; color: #0f5132; padding: 4px 12px; border-radius: 12px; font-size: 13px;">معتمدة</span>
                                @break
                            @case('paid')
                                <span style="background: #d1e7dd; color: #0f5132; padding: 4px 12px; border-radius: 12px; font-size: 13px;">مدفوعة</span>
                                @break
                            @case('rejected')
                                <span style="background: #f8d7da; color: #842029; padding: 4px 12px; border-radius: 12px; font-size: 13px;">مرفوضة</span>
                                @break
                        @endswitch
                        @if($invoice->gmp_exceeded)
                            <div style="color: #dc3545; font-size: 12px; margin-top: 4px;">⚠️ GMP متجاوز</div>
                        @endif
                    </td>
                    <td style="padding: 16px; text-align: center;">
                        <a href="{{ route('cost-plus.invoices.show', $invoice->id) }}" style="color: var(--accent); text-decoration: none; margin: 0 8px;">عرض</a>
                        <a href="{{ route('cost-plus.invoices.export', $invoice->id) }}" style="color: #28a745; text-decoration: none; margin: 0 8px;">تصدير</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" style="padding: 40px; text-align: center; color: #666;">
                        لا توجد فواتير مسجلة
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Generate Invoice Modal -->
<div id="generateModal" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); z-index: 9999; padding: 20px; overflow-y: auto;">
    <div style="max-width: 600px; margin: 50px auto; background: white; border-radius: 12px; padding: 32px; position: relative;">
        <button onclick="document.getElementById('generateModal').style.display='none'" style="position: absolute; top: 16px; left: 16px; background: none; border: none; font-size: 24px; cursor: pointer; color: #666;">×</button>
        
        <h2 style="margin-bottom: 24px; font-size: 24px; font-weight: 700;">إنشاء فاتورة جديدة</h2>
        
        <form action="{{ route('cost-plus.invoices.generate') }}" method="POST">
            @csrf
            
            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 600;">رقم الفاتورة</label>
                <input type="text" name="invoice_number" required style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-family: 'Cairo', sans-serif;">
            </div>
            
            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 600;">عقد Cost Plus</label>
                <select name="cost_plus_contract_id" required style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-family: 'Cairo', sans-serif;">
                    <option value="">اختر العقد</option>
                    @foreach($contracts as $contract)
                        <option value="{{ $contract->id }}">{{ $contract->contract->contract_number }} - {{ $contract->project->name }}</option>
                    @endforeach
                </select>
            </div>
            
            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 600;">المشروع</label>
                <select name="project_id" required style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-family: 'Cairo', sans-serif;">
                    <option value="">اختر المشروع</option>
                    @foreach($projects as $project)
                        <option value="{{ $project->id }}">{{ $project->name }}</option>
                    @endforeach
                </select>
            </div>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 20px;">
                <div>
                    <label style="display: block; margin-bottom: 8px; font-weight: 600;">من تاريخ</label>
                    <input type="date" name="period_from" required style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-family: 'Cairo', sans-serif;">
                </div>
                <div>
                    <label style="display: block; margin-bottom: 8px; font-weight: 600;">إلى تاريخ</label>
                    <input type="date" name="period_to" required style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-family: 'Cairo', sans-serif;">
                </div>
            </div>
            
            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 600;">نسبة الضريبة (%)</label>
                <input type="number" name="vat_percentage" value="16" step="0.01" min="0" max="100" style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-family: 'Cairo', sans-serif;">
            </div>
            
            <button type="submit" style="width: 100%; background: var(--accent); color: white; padding: 14px; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; font-family: 'Cairo', sans-serif;">
                إنشاء الفاتورة
            </button>
        </form>
    </div>
</div>
@endsection
