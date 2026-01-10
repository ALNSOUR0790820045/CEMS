@extends('layouts.app')

@section('content')
<div style="padding: 40px; max-width: 900px; margin: 0 auto;">
    <div style="margin-bottom: 30px;">
        <h1 style="font-size: 2rem; color: #1d1d1f; margin-bottom: 5px;">إضافة شرط دفع جديد</h1>
        <p style="color: #86868b;">أدخل بيانات شرط الدفع الجديد</p>
    </div>

    @if($errors->any())
    <div style="background: #f8d7da; color: #721c24; padding: 15px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #f5c6cb;">
        <ul style="list-style: none; margin: 0; padding: 0;">
            @foreach($errors->all() as $error)
            <li>• {{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form method="POST" action="{{ route('payment-terms.store') }}" style="background: white; padding: 40px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
        @csrf

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
            <div>
                <label style="display: block; margin-bottom: 8px; color: #1d1d1f; font-weight: 600;">
                    الاسم (عربي) <span style="color: #ff3b30;">*</span>
                </label>
                <input type="text" name="name" value="{{ old('name') }}" required placeholder="نقدي، آجل 30 يوم"
                    style="width: 100%; padding: 12px; border: 1px solid #d2d2d7; border-radius: 8px; font-size: 1rem; font-family: 'Cairo', sans-serif;">
            </div>

            <div>
                <label style="display: block; margin-bottom: 8px; color: #1d1d1f; font-weight: 600;">
                    الاسم (English)
                </label>
                <input type="text" name="name_en" value="{{ old('name_en') }}" placeholder="Cash, Net 30"
                    style="width: 100%; padding: 12px; border: 1px solid #d2d2d7; border-radius: 8px; font-size: 1rem; font-family: 'Cairo', sans-serif;">
            </div>
        </div>

        <div style="margin-bottom: 20px;">
            <label style="display: block; margin-bottom: 8px; color: #1d1d1f; font-weight: 600;">
                عدد الأيام <span style="color: #ff3b30;">*</span>
            </label>
            <input type="number" name="days" value="{{ old('days', 0) }}" required min="0" 
                style="width: 100%; padding: 12px; border: 1px solid #d2d2d7; border-radius: 8px; font-size: 1rem; font-family: 'Cairo', sans-serif;">
            <small style="color: #86868b; display: block; margin-top: 5px;">أدخل 0 للدفع النقدي، أو عدد أيام الآجل (مثلاً: 30، 60، 90)</small>
        </div>

        <div style="margin-bottom: 20px;">
            <label style="display: block; margin-bottom: 8px; color: #1d1d1f; font-weight: 600;">الوصف</label>
            <textarea name="description" rows="4" placeholder="وصف شرط الدفع (اختياري)"
                style="width: 100%; padding: 12px; border: 1px solid #d2d2d7; border-radius: 8px; font-size: 1rem; font-family: 'Cairo', sans-serif; resize: vertical;">{{ old('description') }}</textarea>
        </div>

        <div style="margin-bottom: 30px;">
            <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}
                    style="width: 18px; height: 18px; cursor: pointer;">
                <span style="color: #1d1d1f; font-weight: 500;">شرط الدفع نشط</span>
            </label>
        </div>

        <div style="display: flex; gap: 10px; justify-content: flex-end;">
            <a href="{{ route('payment-terms.index') }}" 
                style="background: #f5f5f7; color: #1d1d1f; padding: 12px 24px; border-radius: 8px; text-decoration: none; font-weight: 600;">
                إلغاء
            </a>
            <button type="submit" 
                style="background: linear-gradient(135deg, #0071e3 0%, #00c4cc 100%); color: white; padding: 12px 24px; border-radius: 8px; border: none; cursor: pointer; font-weight: 600; font-family: 'Cairo', sans-serif;">
                حفظ
            </button>
        </div>
    </form>
</div>
@endsection
