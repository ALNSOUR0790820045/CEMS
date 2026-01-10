@extends('layouts.app')

@section('content')
<div style="padding: 40px; max-width: 1000px; margin: 0 auto;">
    <h1 style="margin-bottom: 30px;">تعديل عنصر WBS</h1>
    <p style="color: #86868b; margin-bottom: 30px;">{{ $tender->name }} - {{ $tender->reference_number }}</p>
    
    <form method="POST" action="{{ route('tender-wbs.update', [$tender->id, $wbs->id]) }}" style="background: white; padding: 30px; border-radius: 10px;">
        @csrf
        @method('PUT')
        
        <!-- Basic Information -->
        <div style="border-bottom: 2px solid #f0f0f0; padding-bottom: 20px; margin-bottom: 20px;">
            <h3 style="color: #1d1d1f; margin-bottom: 15px;">المعلومات الأساسية</h3>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                <div>
                    <label style="display: block; margin-bottom: 5px; font-weight: 600;">كود WBS *</label>
                    <input type="text" name="wbs_code" value="{{ old('wbs_code', $wbs->wbs_code) }}" required 
                        style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;"
                        placeholder="مثال: 1.1.1">
                    @error('wbs_code')
                        <span style="color: #ff3b30; font-size: 0.85rem;">{{ $message }}</span>
                    @enderror
                </div>
                
                <div>
                    <label style="display: block; margin-bottom: 5px; font-weight: 600;">المستوى *</label>
                    <select name="level" required 
                        style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
                        <option value="">اختر المستوى</option>
                        <option value="1" {{ old('level', $wbs->level) == 1 ? 'selected' : '' }}>المستوى 1</option>
                        <option value="2" {{ old('level', $wbs->level) == 2 ? 'selected' : '' }}>المستوى 2</option>
                        <option value="3" {{ old('level', $wbs->level) == 3 ? 'selected' : '' }}>المستوى 3</option>
                        <option value="4" {{ old('level', $wbs->level) == 4 ? 'selected' : '' }}>المستوى 4</option>
                        <option value="5" {{ old('level', $wbs->level) == 5 ? 'selected' : '' }}>المستوى 5</option>
                    </select>
                    @error('level')
                        <span style="color: #ff3b30; font-size: 0.85rem;">{{ $message }}</span>
                    @enderror
                </div>
            </div>
            
            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 5px; font-weight: 600;">العنصر الأب</label>
                <select name="parent_id" 
                    style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
                    <option value="">لا يوجد (عنصر رئيسي)</option>
                    @foreach($parentWbsItems as $item)
                        <option value="{{ $item->id }}" {{ old('parent_id', $wbs->parent_id) == $item->id ? 'selected' : '' }}>
                            {{ $item->wbs_code }} - {{ $item->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 5px; font-weight: 600;">الاسم *</label>
                <input type="text" name="name" value="{{ old('name', $wbs->name) }}" required 
                    style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
                @error('name')
                    <span style="color: #ff3b30; font-size: 0.85rem;">{{ $message }}</span>
                @enderror
            </div>
            
            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 5px; font-weight: 600;">الاسم بالإنجليزية</label>
                <input type="text" name="name_en" value="{{ old('name_en', $wbs->name_en) }}" 
                    style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
            </div>
            
            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 5px; font-weight: 600;">الوصف</label>
                <textarea name="description" rows="3" 
                    style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">{{ old('description', $wbs->description) }}</textarea>
            </div>
            
            <div style="margin-bottom: 20px;">
                <label>
                    <input type="checkbox" name="is_summary" value="1" {{ old('is_summary', $wbs->is_summary) ? 'checked' : '' }}>
                    عنصر تجميعي (يحتوي على عناصر فرعية)
                </label>
            </div>
        </div>

        <!-- Cost Information -->
        <div style="border-bottom: 2px solid #f0f0f0; padding-bottom: 20px; margin-bottom: 20px;">
            <h3 style="color: #1d1d1f; margin-bottom: 15px;">التكاليف المقدرة</h3>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <div>
                    <label style="display: block; margin-bottom: 5px; font-weight: 600;">تكلفة المواد</label>
                    <input type="number" name="materials_cost" value="{{ old('materials_cost', $wbs->materials_cost) }}" step="0.01" min="0"
                        style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
                </div>
                
                <div>
                    <label style="display: block; margin-bottom: 5px; font-weight: 600;">تكلفة العمالة</label>
                    <input type="number" name="labor_cost" value="{{ old('labor_cost', $wbs->labor_cost) }}" step="0.01" min="0"
                        style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
                </div>
                
                <div>
                    <label style="display: block; margin-bottom: 5px; font-weight: 600;">تكلفة المعدات</label>
                    <input type="number" name="equipment_cost" value="{{ old('equipment_cost', $wbs->equipment_cost) }}" step="0.01" min="0"
                        style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
                </div>
                
                <div>
                    <label style="display: block; margin-bottom: 5px; font-weight: 600;">تكلفة مقاولي الباطن</label>
                    <input type="number" name="subcontractor_cost" value="{{ old('subcontractor_cost', $wbs->subcontractor_cost) }}" step="0.01" min="0"
                        style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
                </div>
                
                <div>
                    <label style="display: block; margin-bottom: 5px; font-weight: 600;">التكلفة الإجمالية المقدرة</label>
                    <input type="number" name="estimated_cost" value="{{ old('estimated_cost', $wbs->estimated_cost) }}" step="0.01" min="0"
                        style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
                </div>
            </div>
        </div>

        <!-- Schedule & Weight -->
        <div style="border-bottom: 2px solid #f0f0f0; padding-bottom: 20px; margin-bottom: 20px;">
            <h3 style="color: #1d1d1f; margin-bottom: 15px;">الجدول الزمني والوزن</h3>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <div>
                    <label style="display: block; margin-bottom: 5px; font-weight: 600;">المدة المقدرة (بالأيام)</label>
                    <input type="number" name="estimated_duration_days" value="{{ old('estimated_duration_days', $wbs->estimated_duration_days) }}" min="0"
                        style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
                </div>
                
                <div>
                    <label style="display: block; margin-bottom: 5px; font-weight: 600;">الوزن النسبي (%)</label>
                    <input type="number" name="weight_percentage" value="{{ old('weight_percentage', $wbs->weight_percentage) }}" step="0.01" min="0" max="100"
                        style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
                </div>
                
                <div>
                    <label style="display: block; margin-bottom: 5px; font-weight: 600;">ترتيب العرض</label>
                    <input type="number" name="sort_order" value="{{ old('sort_order', $wbs->sort_order) }}" min="0"
                        style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
                </div>
            </div>
        </div>
        
        <!-- Actions -->
        <div>
            <button type="submit" style="background: #0071e3; color: white; padding: 12px 30px; border: none; border-radius: 8px; cursor: pointer; font-family: 'Cairo', sans-serif; font-weight: 600;">تحديث</button>
            <a href="{{ route('tender-wbs.index', $tender->id) }}" style="margin-right: 15px; padding: 12px 30px; text-decoration: none; color: #666;">إلغاء</a>
        </div>
    </form>
</div>
@endsection
