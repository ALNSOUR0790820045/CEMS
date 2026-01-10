@extends('layouts.app')

@section('content')
<div style="max-width: 900px; margin: 0 auto;">
    <div style="margin-bottom: 30px;">
        <h1 style="font-size: 2rem; font-weight: 700; margin-bottom: 5px;">تسجيل الدفع</h1>
        <p style="color: #86868b; font-size: 0.9rem;">{{ $mainIpc->ipc_number }} - {{ $mainIpc->project->name }}</p>
    </div>

    <!-- IPC Summary -->
    <div style="background: white; padding: 25px; border-radius: 12px; margin-bottom: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.05);">
        <h3 style="font-size: 1.2rem; font-weight: 600; margin-bottom: 20px;">ملخص المستخلص</h3>
        
        <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px; margin-bottom: 20px;">
            <div>
                <div style="color: #86868b; font-size: 0.85rem; margin-bottom: 5px;">رقم المستخلص</div>
                <div style="font-weight: 700; font-size: 1.2rem; color: var(--accent);">{{ $mainIpc->ipc_number }}</div>
            </div>
            
            <div>
                <div style="color: #86868b; font-size: 0.85rem; margin-bottom: 5px;">الفترة</div>
                <div style="font-weight: 600;">{{ $mainIpc->period_from->format('Y/m/d') }} - {{ $mainIpc->period_to->format('Y/m/d') }}</div>
            </div>
        </div>

        <div style="background: #f5f5f7; padding: 20px; border-radius: 8px;">
            <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                <span style="color: #86868b;">القيمة التراكمية</span>
                <span style="font-weight: 600;">{{ number_format($mainIpc->current_cumulative, 2) }} ر.س</span>
            </div>
            <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                <span style="color: #86868b;">الاستبقاء ({{ $mainIpc->retention_percent }}%)</span>
                <span style="color: #ff9500;">-{{ number_format($mainIpc->retention_amount, 2) }} ر.س</span>
            </div>
            @if($mainIpc->advance_payment_deduction > 0)
                <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                    <span style="color: #86868b;">استرداد دفعة مقدمة</span>
                    <span style="color: #ff9500;">-{{ number_format($mainIpc->advance_payment_deduction, 2) }} ر.س</span>
                </div>
            @endif
            @if($mainIpc->other_deductions > 0)
                <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                    <span style="color: #86868b;">خصومات أخرى</span>
                    <span style="color: #ff9500;">-{{ number_format($mainIpc->other_deductions, 2) }} ر.س</span>
                </div>
            @endif
            <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                <span style="color: #86868b;">الضريبة ({{ $mainIpc->tax_rate }}%)</span>
                <span style="color: #34c759;">+{{ number_format($mainIpc->tax_amount, 2) }} ر.س</span>
            </div>
            <div style="border-top: 2px solid #e5e5e7; padding-top: 10px; margin-top: 10px;">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <span style="font-weight: 700; font-size: 1.2rem;">المبلغ المستحق</span>
                    <span style="font-weight: 700; font-size: 1.6rem; color: #34c759;">
                        {{ number_format($mainIpc->client_approved_amount ?? $mainIpc->net_payable, 2) }} ر.س
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment Form -->
    <form method="POST" action="{{ route('main-ipcs.process-payment', $mainIpc) }}" style="background: white; padding: 25px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.05);">
        @csrf

        <h3 style="font-size: 1.2rem; font-weight: 600; margin-bottom: 20px;">معلومات الدفع</h3>

        <div style="margin-bottom: 20px;">
            <label style="display: block; margin-bottom: 8px; font-weight: 600; font-size: 0.9rem;">تاريخ الدفع *</label>
            <input type="date" name="payment_date" value="{{ date('Y-m-d') }}" required 
                   style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-family: 'Cairo', sans-serif;">
        </div>

        <div style="margin-bottom: 20px;">
            <label style="display: block; margin-bottom: 8px; font-weight: 600; font-size: 0.9rem;">رقم المرجع / الشيك *</label>
            <input type="text" name="payment_reference" required 
                   placeholder="أدخل رقم الشيك أو رقم التحويل"
                   style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-family: 'Cairo', sans-serif;">
        </div>

        <div style="margin-bottom: 20px;">
            <label style="display: block; margin-bottom: 8px; font-weight: 600; font-size: 0.9rem;">المبلغ المدفوع *</label>
            <input type="number" name="paid_amount" step="0.01" min="0" 
                   value="{{ $mainIpc->client_approved_amount ?? $mainIpc->net_payable }}" 
                   required 
                   style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-family: 'Cairo', sans-serif; font-size: 1.1rem; font-weight: 600;">
            <div style="font-size: 0.8rem; color: #86868b; margin-top: 5px;">
                المبلغ المستحق: {{ number_format($mainIpc->client_approved_amount ?? $mainIpc->net_payable, 2) }} ر.س
            </div>
        </div>

        <div style="background: rgba(255, 149, 0, 0.1); padding: 15px; border-radius: 8px; border-right: 4px solid #ff9500; margin-bottom: 20px;">
            <div style="font-weight: 600; color: #ff9500; margin-bottom: 5px;">⚠️ تنبيه</div>
            <div style="font-size: 0.85rem; color: #1d1d1f;">
                بعد تأكيد الدفع، سيتم تحديث حالة المستخلص إلى "تم الدفع" ولن يمكن التعديل عليه.
            </div>
        </div>

        <div style="display: flex; gap: 15px; justify-content: flex-end;">
            <a href="{{ route('main-ipcs.show', $mainIpc) }}" style="padding: 12px 30px; background: #f5f5f7; color: #1d1d1f; border-radius: 8px; text-decoration: none; font-weight: 600;">
                إلغاء
            </a>
            <button type="submit" style="padding: 12px 30px; background: #34c759; color: white; border: none; border-radius: 8px; cursor: pointer; font-family: 'Cairo', sans-serif; font-weight: 600; display: flex; align-items: center; gap: 8px;">
                <i data-lucide="check-circle" style="width: 18px; height: 18px;"></i>
                تأكيد الدفع
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
    lucide.createIcons();
</script>
@endpush
@endsection
