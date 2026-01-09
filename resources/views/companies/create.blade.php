@extends('layouts.app')

@section('content')
<div style="padding: 40px; max-width: 800px; margin: 0 auto;">
    <h1 style="margin-bottom: 30px;">إضافة شركة جديدة</h1>
    
    <form method="POST" action="{{ route('companies.store') }}" enctype="multipart/form-data" style="background: white; padding: 30px; border-radius: 10px;">
        @csrf
        
        <div style="margin-bottom:  20px;">
            <label style="display: block; margin-bottom: 5px; font-weight: 600;">اسم الشركة *</label>
            <input type="text" name="name" required style="width: 100%; padding:  10px; border: 1px solid #ddd; border-radius:  5px; font-family: 'Cairo', sans-serif;">
        </div>
        
        <div style="margin-bottom: 20px;">
            <label style="display: block; margin-bottom: 5px; font-weight: 600;">البريد الإلكتروني</label>
            <input type="email" name="email" style="width: 100%; padding:  10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
        </div>
        
        <div style="margin-bottom: 20px;">
            <label style="display: block; margin-bottom: 5px; font-weight: 600;">الهاتف</label>
            <input type="text" name="phone" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius:  5px; font-family:  'Cairo', sans-serif;">
        </div>
        
        <div style="margin-bottom: 20px;">
            <label style="display: block; margin-bottom: 5px; font-weight: 600;">المدينة</label>
            <input type="text" name="city" style="width: 100%; padding: 10px; border:  1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
        </div>
        
        <div style="margin-bottom: 20px;">
            <label style="display: block; margin-bottom: 5px; font-weight: 600;">الشعار</label>
            <input type="file" name="logo" accept="image/*" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
            <small style="color: #666;">الصيغ المدعومة: JPG, PNG, GIF (حد أقصى: 2MB)</small>
        </div>
        
        <div style="margin-bottom: 20px;">
            <label style="display: block; margin-bottom: 5px; font-weight: 600;">الدولة *</label>
            <select name="country" required style="width:  100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
                <option value="">اختر الدولة</option>
                <option value="SA">السعودية</option>
                <option value="AE">الإمارات</option>
                <option value="KW">الكويت</option>
            </select>
        </div>
        
        <div style="margin-bottom: 20px;">
            <label>
                <input type="checkbox" name="is_active" value="1" checked>
                الشركة نشطة
            </label>
        </div>
        
        <div>
            <button type="submit" style="background: #0071e3; color: white; padding: 12px 30px; border:  none; border-radius: 8px; cursor: pointer; font-family: 'Cairo', sans-serif; font-weight: 600;">حفظ</button>
            <a href="{{ route('companies.index') }}" style="margin-right: 15px; padding: 12px 30px; text-decoration: none; color: #666;">إلغاء</a>
        </div>
    </form>
</div>
@endsection
