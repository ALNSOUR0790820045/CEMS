@extends('layouts.app')

@section('content')
<div style="padding: 40px; max-width: 1000px; margin: 0 auto;">
    <h1 style="margin-bottom: 30px;">تعديل خطاب الضمان</h1>

    @if($errors->any())
        <div style="background: #f8d7da; color: #721c24; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
            <ul style="margin: 0; padding-right: 20px;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('guarantees.update', $guarantee) }}" style="background: white; padding: 30px; border-radius: 10px;">
        @csrf
        @method('PUT')

        <!-- Use same fields as create.blade.php but with values -->
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
            <div>
                <label style="display: block; margin-bottom: 5px; font-weight: 600;">نوع الخطاب *</label>
                <select name="type" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
                    <option value="bid" {{ old('type', $guarantee->type) == 'bid' ? 'selected' : '' }}>ضمان ابتدائي</option>
                    <option value="performance" {{ old('type', $guarantee->type) == 'performance' ? 'selected' : '' }}>ضمان حسن التنفيذ</option>
                    <option value="advance_payment" {{ old('type', $guarantee->type) == 'advance_payment' ? 'selected' : '' }}>ضمان الدفعة المقدمة</option>
                    <option value="maintenance" {{ old('type', $guarantee->type) == 'maintenance' ? 'selected' : '' }}>ضمان الصيانة</option>
                    <option value="retention" {{ old('type', $guarantee->type) == 'retention' ? 'selected' : '' }}>ضمان الاحتجاز</option>
                </select>
            </div>

            <div>
                <label style="display: block; margin-bottom: 5px; font-weight: 600;">البنك *</label>
                <select name="bank_id" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
                    @foreach($banks as $bank)
                        <option value="{{ $bank->id }}" {{ old('bank_id', $guarantee->bank_id) == $bank->id ? 'selected' : '' }}>{{ $bank->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div style="margin-bottom: 20px;">
            <label style="display: block; margin-bottom: 5px; font-weight: 600;">الجهة المستفيدة *</label>
            <input type="text" name="beneficiary" value="{{ old('beneficiary', $guarantee->beneficiary) }}" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
        </div>

        <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 20px; margin-bottom: 20px;">
            <div>
                <label style="display: block; margin-bottom: 5px; font-weight: 600;">المبلغ *</label>
                <input type="number" name="amount" value="{{ old('amount', $guarantee->amount) }}" step="0.01" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
            </div>

            <div>
                <label style="display: block; margin-bottom: 5px; font-weight: 600;">العملة *</label>
                <select name="currency" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
                    <option value="SAR" {{ old('currency', $guarantee->currency) == 'SAR' ? 'selected' : '' }}>SAR</option>
                    <option value="USD" {{ old('currency', $guarantee->currency) == 'USD' ? 'selected' : '' }}>USD</option>
                    <option value="EUR" {{ old('currency', $guarantee->currency) == 'EUR' ? 'selected' : '' }}>EUR</option>
                </select>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
            <div>
                <label style="display: block; margin-bottom: 5px; font-weight: 600;">تاريخ الإصدار *</label>
                <input type="date" name="issue_date" value="{{ old('issue_date', $guarantee->issue_date->format('Y-m-d')) }}" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
            </div>

            <div>
                <label style="display: block; margin-bottom: 5px; font-weight: 600;">تاريخ الانتهاء *</label>
                <input type="date" name="expiry_date" value="{{ old('expiry_date', $guarantee->expiry_date->format('Y-m-d')) }}" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
            </div>
        </div>

        <div style="display: flex; gap: 15px; margin-top: 30px;">
            <button type="submit" style="background: #0071e3; color: white; padding: 12px 30px; border: none; border-radius: 8px; cursor: pointer; font-family: 'Cairo', sans-serif; font-weight: 600;">حفظ التعديلات</button>
            <a href="{{ route('guarantees.show', $guarantee) }}" style="background: #f5f5f7; color: #1d1d1f; padding: 12px 30px; border-radius: 8px; text-decoration: none; font-family: 'Cairo', sans-serif; font-weight: 600;">إلغاء</a>
        </div>
    </form>
</div>
@endsection
