@extends('layouts.app')

@section('content')
<div style="padding: 40px; max-width: 800px; margin: 0 auto;">
    <h1 style="margin-bottom: 30px;">تحرير خطاب الضمان</h1>

    <div style="background: white; padding: 30px; border-radius: 10px; margin-bottom: 20px;">
        <p><strong>رقم الخطاب:</strong> {{ $guarantee->guarantee_number }}</p>
        <p><strong>المبلغ:</strong> {{ number_format($guarantee->amount, 2) }} {{ $guarantee->currency }}</p>
    </div>

    <form method="POST" action="{{ route('guarantees.release.store', $guarantee) }}" style="background: white; padding: 30px; border-radius: 10px;">
        @csrf

        <div style="margin-bottom: 20px;">
            <label style="display: block; margin-bottom: 5px; font-weight: 600;">نوع التحرير *</label>
            <select name="release_type" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;" onchange="toggleReleaseAmount(this.value)">
                <option value="full">تحرير كلي</option>
                <option value="partial">تحرير جزئي</option>
            </select>
        </div>

        <div style="margin-bottom: 20px;" id="amount-field">
            <label style="display: block; margin-bottom: 5px; font-weight: 600;">المبلغ المحرر *</label>
            <input type="number" name="released_amount" step="0.01" min="0" max="{{ $guarantee->amount }}" value="{{ $guarantee->amount }}" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;" data-full-amount="{{ $guarantee->amount }}">
        </div>

        <div style="margin-bottom: 20px;">
            <label style="display: block; margin-bottom: 5px; font-weight: 600;">رقم تأكيد البنك</label>
            <input type="text" name="bank_confirmation_number" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
        </div>

        <div style="margin-bottom: 20px;">
            <label style="display: block; margin-bottom: 5px; font-weight: 600;">ملاحظات</label>
            <textarea name="notes" rows="3" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;"></textarea>
        </div>

        <div style="display: flex; gap: 15px;">
            <button type="submit" style="background: #0071e3; color: white; padding: 12px 30px; border: none; border-radius: 8px; cursor: pointer; font-family: 'Cairo', sans-serif; font-weight: 600;">تحرير الخطاب</button>
            <a href="{{ route('guarantees.show', $guarantee) }}" style="background: #f5f5f7; color: #1d1d1f; padding: 12px 30px; border-radius: 8px; text-decoration: none; font-family: 'Cairo', sans-serif; font-weight: 600;">إلغاء</a>
        </div>
    </form>
</div>

@push('scripts')
<script>
function toggleReleaseAmount(type) {
    const amountField = document.querySelector('[name="released_amount"]');
    const fullAmount = amountField.dataset.fullAmount;
    if (type === 'full') {
        amountField.value = fullAmount;
        amountField.readOnly = true;
    } else {
        amountField.readOnly = false;
    }
}
</script>
@endpush
@endsection
