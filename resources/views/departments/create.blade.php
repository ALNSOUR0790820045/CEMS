@extends('layouts.app')

@section('content')
<div style="padding: 40px; max-width: 800px; margin: 0 auto;">
    <h1 style="margin-bottom: 30px;">إضافة قسم جديد</h1>
    
    <form method="POST" action="{{ route('departments.store') }}" style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
        @csrf
        
        <div style="margin-bottom: 20px;">
            <label style="display: block; margin-bottom: 5px; font-weight: 600;">الفرع *</label>
            <select name="branch_id" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
                <option value="">اختر الفرع</option>
                @foreach($branches as $branch)
                <option value="{{ $branch->id }}" {{ old('branch_id') == $branch->id ? 'selected' : '' }}>
                    {{ $branch->name }}
                </option>
                @endforeach
            </select>
            @error('branch_id')
            <span style="color: #ff3b30; font-size: 0.85rem;">{{ $message }}</span>
            @enderror
        </div>
        
        <div style="margin-bottom: 20px;">
            <label style="display: block; margin-bottom: 5px; font-weight: 600;">اسم القسم *</label>
            <input type="text" name="name" value="{{ old('name') }}" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
            @error('name')
            <span style="color: #ff3b30; font-size: 0.85rem;">{{ $message }}</span>
            @enderror
        </div>
        
        <div style="margin-bottom: 20px;">
            <label style="display: block; margin-bottom: 5px; font-weight: 600;">اسم القسم (EN)</label>
            <input type="text" name="name_en" value="{{ old('name_en') }}" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
            @error('name_en')
            <span style="color: #ff3b30; font-size: 0.85rem;">{{ $message }}</span>
            @enderror
        </div>
        
        <div style="margin-bottom: 20px;">
            <label style="display: block; margin-bottom: 5px; font-weight: 600;">كود القسم *</label>
            <input type="text" name="code" value="{{ old('code') }}" required placeholder="مثال: HR, SALES" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
            @error('code')
            <span style="color: #ff3b30; font-size: 0.85rem;">{{ $message }}</span>
            @enderror
        </div>
        
        <div style="margin-bottom: 20px;">
            <label style="display: block; margin-bottom: 5px; font-weight: 600;">مدير القسم</label>
            <select name="manager_id" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
                <option value="">اختر المدير</option>
                @foreach($users as $user)
                <option value="{{ $user->id }}" {{ old('manager_id') == $user->id ? 'selected' : '' }}>
                    {{ $user->name }}
                </option>
                @endforeach
            </select>
            @error('manager_id')
            <span style="color: #ff3b30; font-size: 0.85rem;">{{ $message }}</span>
            @enderror
        </div>
        
        <div style="margin-bottom: 20px;">
            <label style="display: block; margin-bottom: 5px; font-weight: 600;">الوصف</label>
            <textarea name="description" rows="4" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">{{ old('description') }}</textarea>
            @error('description')
            <span style="color: #ff3b30; font-size: 0.85rem;">{{ $message }}</span>
            @enderror
        </div>
        
        <div style="margin-bottom: 20px;">
            <label>
                <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                القسم نشط
            </label>
        </div>
        
        <div>
            <button type="submit" style="background: #0071e3; color: white; padding: 12px 30px; border: none; border-radius: 8px; cursor: pointer; font-family: 'Cairo', sans-serif; font-weight: 600;">حفظ</button>
            <a href="{{ route('departments.index') }}" style="margin-right: 15px; padding: 12px 30px; text-decoration: none; color: #666;">إلغاء</a>
        </div>
    </form>
</div>
@endsection
