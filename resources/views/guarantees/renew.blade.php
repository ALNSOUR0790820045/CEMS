@extends('layouts.app')

@section('content')
<div style="padding: 40px; max-width: 800px; margin: 0 auto;">
    <h1 style="margin-bottom: 30px;">تجديد خطاب الضمان</h1>

    <div style="background: white; padding: 30px; border-radius: 10px; margin-bottom: 20px;">
        <p><strong>رقم الخطاب:</strong> {{ $guarantee->guarantee_number }}</p>
        <p><strong>تاريخ الانتهاء الحالي:</strong> {{ $guarantee->expiry_date->format('Y-m-d') }}</p>
        <p><strong>المبلغ الحالي:</strong> {{ number_format($guarantee->amount, 2) }} {{ $guarantee->currency }}</p>
    </div>

    <form method="POST" action="{{ route('guarantees.renew.store', $guarantee) }}" style="background: white; padding: 30px; border-radius: 10px;">
        @csrf

        <div style="margin-bottom: 20px;">
            <label style="display: block; margin-bottom: 5px; font-weight: 600;">تاريخ الانتهاء الجديد *</label>
            <input type="date" name="new_expiry_date" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
        </div>

        <div style="margin-bottom: 20px;">
            <label style="display: block; margin-bottom: 5px; font-weight: 600;">المبلغ الجديد (اختياري)</label>
            <input type="number" name="new_amount" step="0.01" min="0" placeholder="{{ $guarantee->amount }}" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
            <small style="color: #666;">اتركه فارغاً للإبقاء على المبلغ الحالي</small>
        </div>

        <div style="margin-bottom: 20px;">
            <label style="display: block; margin-bottom: 5px; font-weight: 600;">رسوم التجديد</label>
            <input type="number" name="renewal_charges" step="0.01" min="0" value="0" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
        </div>

        <div style="margin-bottom: 20px;">
            <label style="display: block; margin-bottom: 5px; font-weight: 600;">رقم المرجع البنكي</label>
            <input type="text" name="bank_reference" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
        </div>

        <div style="margin-bottom: 20px;">
            <label style="display: block; margin-bottom: 5px; font-weight: 600;">ملاحظات</label>
            <textarea name="notes" rows="3" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;"></textarea>
        </div>

        <div style="display: flex; gap: 15px;">
            <button type="submit" style="background: #0071e3; color: white; padding: 12px 30px; border: none; border-radius: 8px; cursor: pointer; font-family: 'Cairo', sans-serif; font-weight: 600;">تجديد الخطاب</button>
            <a href="{{ route('guarantees.show', $guarantee) }}" style="background: #f5f5f7; color: #1d1d1f; padding: 12px 30px; border-radius: 8px; text-decoration: none; font-family: 'Cairo', sans-serif; font-weight: 600;">إلغاء</a>
        </div>
    </form>
</div>
@endsection
