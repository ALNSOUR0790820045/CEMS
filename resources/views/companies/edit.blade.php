@extends('layouts.app')

@section('content')
<div style="padding: 40px; max-width: 800px; margin: 0 auto; font-family: 'Cairo', sans-serif;">
    <h1 style="margin-bottom: 30px; font-family: 'Cairo', sans-serif;">تعديل بيانات الشركة</h1>
    
    @if($errors->any())
    <div style="background: #fee; border: 1px solid #fcc; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
        <ul style="margin: 0; padding: 0 0 0 20px; color: #c33;">
            @foreach($errors->all() as $error)
            <li style="font-family: 'Cairo', sans-serif;">{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif
    
    <form method="POST" action="{{ route('companies.update', $company) }}" style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
        @csrf
        @method('PUT')
        
        <div style="margin-bottom: 20px;">
            <label style="display: block; margin-bottom: 5px; font-weight: 600; font-family: 'Cairo', sans-serif;">اسم الشركة (عربي) *</label>
            <input type="text" name="name" value="{{ old('name', $company->name) }}" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif; font-size: 16px;">
        </div>
        
        <div style="margin-bottom: 20px;">
            <label style="display: block; margin-bottom: 5px; font-weight: 600; font-family: 'Cairo', sans-serif;">اسم الشركة (English)</label>
            <input type="text" name="name_en" value="{{ old('name_en', $company->name_en) }}" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif; font-size: 16px;">
        </div>
        
        <div style="margin-bottom: 20px;">
            <label style="display: block; margin-bottom: 5px; font-weight: 600; font-family: 'Cairo', sans-serif;">البريد الإلكتروني</label>
            <input type="email" name="email" value="{{ old('email', $company->email) }}" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif; font-size: 16px;">
        </div>
        
        <div style="margin-bottom: 20px;">
            <label style="display: block; margin-bottom: 5px; font-weight: 600; font-family: 'Cairo', sans-serif;">رقم الهاتف</label>
            <input type="text" name="phone" value="{{ old('phone', $company->phone) }}" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif; font-size: 16px;">
        </div>
        
        <div style="margin-bottom: 20px;">
            <label style="display: block; margin-bottom: 5px; font-weight: 600; font-family: 'Cairo', sans-serif;">العنوان</label>
            <input type="text" name="address" value="{{ old('address', $company->address) }}" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif; font-size: 16px;">
        </div>
        
        <div style="margin-bottom: 20px;">
            <label style="display: block; margin-bottom: 5px; font-weight: 600; font-family: 'Cairo', sans-serif;">المدينة</label>
            <input type="text" name="city" value="{{ old('city', $company->city) }}" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif; font-size: 16px;">
        </div>
        
        <div style="margin-bottom: 20px;">
            <label style="display: block; margin-bottom: 5px; font-weight: 600; font-family: 'Cairo', sans-serif;">الدولة *</label>
            <select name="country" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif; font-size: 16px;">
                <option value="">اختر الدولة</option>
                <option value="SA" {{ old('country', $company->country) == 'SA' ? 'selected' : '' }}>السعودية</option>
                <option value="AE" {{ old('country', $company->country) == 'AE' ? 'selected' : '' }}>الإمارات</option>
                <option value="KW" {{ old('country', $company->country) == 'KW' ? 'selected' : '' }}>الكويت</option>
                <option value="QA" {{ old('country', $company->country) == 'QA' ? 'selected' : '' }}>قطر</option>
                <option value="BH" {{ old('country', $company->country) == 'BH' ? 'selected' : '' }}>البحرين</option>
                <option value="OM" {{ old('country', $company->country) == 'OM' ? 'selected' : '' }}>عمان</option>
                <option value="JO" {{ old('country', $company->country) == 'JO' ? 'selected' : '' }}>الأردن</option>
                <option value="EG" {{ old('country', $company->country) == 'EG' ? 'selected' : '' }}>مصر</option>
            </select>
        </div>
        
        <div style="margin-bottom: 20px;">
            <label style="display: block; margin-bottom: 5px; font-weight: 600; font-family: 'Cairo', sans-serif;">السجل التجاري</label>
            <input type="text" name="commercial_registration" value="{{ old('commercial_registration', $company->commercial_registration) }}" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif; font-size: 16px;">
        </div>
        
        <div style="margin-bottom: 20px;">
            <label style="display: block; margin-bottom: 5px; font-weight: 600; font-family: 'Cairo', sans-serif;">الرقم الضريبي</label>
            <input type="text" name="tax_number" value="{{ old('tax_number', $company->tax_number) }}" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif; font-size: 16px;">
        </div>
        
        <div style="margin-bottom: 20px;">
            <label style="font-family: 'Cairo', sans-serif; font-weight: 600;">
                <input type="checkbox" name="is_active" value="1" {{ old('is_active', $company->is_active) ? 'checked' : '' }}>
                الشركة نشطة
            </label>
        </div>
        
        <div>
            <button type="submit" style="background: #0071e3; color: white; padding: 12px 30px; border: none; border-radius: 8px; cursor: pointer; font-family: 'Cairo', sans-serif; font-weight: 600; font-size: 16px;">حفظ التعديلات</button>
            <a href="{{ route('companies.index') }}" style="margin-right: 15px; padding: 12px 30px; text-decoration: none; color: #666; font-family: 'Cairo', sans-serif; font-weight: 600;">إلغاء</a>
        </div>
    </form>
</div>
@endsection
