@extends('layouts.app')

@section('content')
<div style="padding: 40px; max-width: 800px; margin: 0 auto;">
    <h1 style="margin-bottom: 30px;">تعديل المدينة</h1>
    
    @if ($errors->any())
    <div style="background: #f8d7da; color: #721c24; padding: 15px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #f5c6cb;">
        <ul style="margin: 0; padding-right: 20px;">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif
    
    <form method="POST" action="{{ route('cities.update', $city) }}" style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
        @csrf
        @method('PUT')
        
        <div style="margin-bottom: 20px;">
            <label style="display: block; margin-bottom: 5px; font-weight: 600;">الدولة *</label>
            <select name="country_id" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif; background: white;">
                <option value="">اختر الدولة</option>
                @foreach($countries as $country)
                <option value="{{ $country->id }}" {{ (old('country_id', $city->country_id) == $country->id) ? 'selected' : '' }}>
                    {{ $country->name }}
                </option>
                @endforeach
            </select>
        </div>
        
        <div style="margin-bottom: 20px;">
            <label style="display: block; margin-bottom: 5px; font-weight: 600;">اسم المدينة *</label>
            <input type="text" name="name" value="{{ old('name', $city->name) }}" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
        </div>
        
        <div style="margin-bottom: 20px;">
            <label style="display: block; margin-bottom: 5px; font-weight: 600;">اسم المدينة (بالإنجليزية)</label>
            <input type="text" name="name_en" value="{{ old('name_en', $city->name_en) }}" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
        </div>
        
        <div style="margin-bottom: 20px;">
            <label>
                <input type="checkbox" name="is_active" value="1" {{ old('is_active', $city->is_active) ? 'checked' : '' }}>
                المدينة نشطة
            </label>
        </div>
        
        <div>
            <button type="submit" style="background: #0071e3; color: white; padding: 12px 30px; border: none; border-radius: 8px; cursor: pointer; font-family: 'Cairo', sans-serif; font-weight: 600;">تحديث</button>
            <a href="{{ route('cities.index') }}" style="margin-right: 15px; padding: 12px 30px; text-decoration: none; color: #666;">إلغاء</a>
        </div>
    </form>
</div>
@endsection
