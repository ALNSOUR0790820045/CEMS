@extends('layouts.app')

@section('content')
<div style="padding: 40px; max-width: 800px; margin: 0 auto;">
    <h1 style="margin-bottom: 30px;">تعديل وحدة القياس</h1>
    
    @if($errors->any())
    <div style="background: #f8d7da; color: #721c24; padding: 15px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #f5c6cb;">
        <ul style="margin: 0; padding-right: 20px;">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif
    
    <form method="POST" action="{{ route('units.update', $unit) }}" style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
        @csrf
        @method('PUT')
        
        <div style="margin-bottom: 20px;">
            <label style="display: block; margin-bottom: 5px; font-weight: 600;">الاسم بالعربية *</label>
            <input type="text" name="name" value="{{ old('name', $unit->name) }}" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
        </div>
        
        <div style="margin-bottom: 20px;">
            <label style="display: block; margin-bottom: 5px; font-weight: 600;">الاسم بالإنجليزية</label>
            <input type="text" name="name_en" value="{{ old('name_en', $unit->name_en) }}" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
        </div>
        
        <div style="margin-bottom: 20px;">
            <label style="display: block; margin-bottom: 5px; font-weight: 600;">الكود *</label>
            <input type="text" name="code" value="{{ old('code', $unit->code) }}" required placeholder="مثال: KG, M, L" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
            <small style="color: #86868b; font-size: 0.85rem;">يجب أن يكون الكود فريداً (مثل: KG, M, L, PCS)</small>
        </div>
        
        <div style="margin-bottom: 20px;">
            <label style="display: block; margin-bottom: 5px; font-weight: 600;">النوع *</label>
            <select name="type" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
                <option value="">اختر النوع</option>
                <option value="weight" {{ old('type', $unit->type) == 'weight' ? 'selected' : '' }}>وزن</option>
                <option value="length" {{ old('type', $unit->type) == 'length' ? 'selected' : '' }}>طول</option>
                <option value="volume" {{ old('type', $unit->type) == 'volume' ? 'selected' : '' }}>حجم</option>
                <option value="quantity" {{ old('type', $unit->type) == 'quantity' ? 'selected' : '' }}>كمية</option>
            </select>
        </div>
        
        <div style="margin-bottom: 20px;">
            <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                <input type="checkbox" name="is_active" value="1" {{ old('is_active', $unit->is_active) ? 'checked' : '' }} style="width: 18px; height: 18px; cursor: pointer;">
                <span style="font-weight: 600;">الوحدة نشطة</span>
            </label>
        </div>
        
        <div>
            <button type="submit" style="background: #0071e3; color: white; padding: 12px 30px; border: none; border-radius: 8px; cursor: pointer; font-family: 'Cairo', sans-serif; font-weight: 600;">تحديث</button>
            <a href="{{ route('units.index') }}" style="margin-right: 15px; padding: 12px 30px; text-decoration: none; color: #666; font-weight: 600;">إلغاء</a>
        </div>
    </form>
</div>
@endsection
