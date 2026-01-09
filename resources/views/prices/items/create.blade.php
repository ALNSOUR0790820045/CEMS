@extends('layouts.app')

@section('content')
<div style="padding: 20px; max-width: 1200px; margin: 0 auto;">
    <div style="margin-bottom: 20px;">
        <a href="{{ route('price-lists.show', $priceList) }}" style="color: var(--accent); text-decoration: none; font-size: 14px;">← العودة للقائمة</a>
        <h1 style="font-size: 28px; font-weight: 600; margin: 10px 0;">إضافة بند جديد</h1>
        <p style="color: #6c757d; margin: 5px 0;">قائمة الأسعار: {{ $priceList->name }}</p>
    </div>

    <div style="background: white; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); padding: 24px;">
        <form action="{{ route('price-list-items.store', $priceList) }}" method="POST">
            @csrf
            
            <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px;">
                <div>
                    <label style="display: block; font-weight: 600; margin-bottom: 8px;">كود البند *</label>
                    <input type="text" name="item_code" required value="{{ old('item_code') }}"
                           style="width: 100%; padding: 12px; border: 1px solid #dee2e6; border-radius: 8px; font-size: 14px;">
                    @error('item_code')
                        <span style="color: #dc3545; font-size: 13px;">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label style="display: block; font-weight: 600; margin-bottom: 8px;">اسم البند *</label>
                    <input type="text" name="item_name" required value="{{ old('item_name') }}"
                           style="width: 100%; padding: 12px; border: 1px solid #dee2e6; border-radius: 8px; font-size: 14px;">
                    @error('item_name')
                        <span style="color: #dc3545; font-size: 13px;">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label style="display: block; font-weight: 600; margin-bottom: 8px;">الاسم بالإنجليزية</label>
                    <input type="text" name="item_name_en" value="{{ old('item_name_en') }}"
                           style="width: 100%; padding: 12px; border: 1px solid #dee2e6; border-radius: 8px; font-size: 14px;">
                </div>

                <div>
                    <label style="display: block; font-weight: 600; margin-bottom: 8px;">الوحدة *</label>
                    <input type="text" name="unit" required value="{{ old('unit') }}" placeholder="مثل: متر، طن، ساعة"
                           style="width: 100%; padding: 12px; border: 1px solid #dee2e6; border-radius: 8px; font-size: 14px;">
                    @error('unit')
                        <span style="color: #dc3545; font-size: 13px;">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label style="display: block; font-weight: 600; margin-bottom: 8px;">سعر الوحدة *</label>
                    <input type="number" name="unit_price" required step="0.0001" value="{{ old('unit_price') }}"
                           style="width: 100%; padding: 12px; border: 1px solid #dee2e6; border-radius: 8px; font-size: 14px;">
                    @error('unit_price')
                        <span style="color: #dc3545; font-size: 13px;">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label style="display: block; font-weight: 600; margin-bottom: 8px;">أقل سعر</label>
                    <input type="number" name="min_price" step="0.0001" value="{{ old('min_price') }}"
                           style="width: 100%; padding: 12px; border: 1px solid #dee2e6; border-radius: 8px; font-size: 14px;">
                </div>

                <div>
                    <label style="display: block; font-weight: 600; margin-bottom: 8px;">أعلى سعر</label>
                    <input type="number" name="max_price" step="0.0001" value="{{ old('max_price') }}"
                           style="width: 100%; padding: 12px; border: 1px solid #dee2e6; border-radius: 8px; font-size: 14px;">
                </div>

                @if($priceList->type == 'material')
                <div>
                    <label style="display: block; font-weight: 600; margin-bottom: 8px;">المادة</label>
                    <select name="material_id"
                            style="width: 100%; padding: 12px; border: 1px solid #dee2e6; border-radius: 8px; font-size: 14px;">
                        <option value="">اختر المادة</option>
                        @foreach($materials as $material)
                            <option value="{{ $material->id }}" {{ old('material_id') == $material->id ? 'selected' : '' }}>
                                {{ $material->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label style="display: block; font-weight: 600; margin-bottom: 8px;">العلامة التجارية</label>
                    <input type="text" name="brand" value="{{ old('brand') }}"
                           style="width: 100%; padding: 12px; border: 1px solid #dee2e6; border-radius: 8px; font-size: 14px;">
                </div>

                <div>
                    <label style="display: block; font-weight: 600; margin-bottom: 8px;">بلد المنشأ</label>
                    <input type="text" name="origin" value="{{ old('origin') }}"
                           style="width: 100%; padding: 12px; border: 1px solid #dee2e6; border-radius: 8px; font-size: 14px;">
                </div>
                @endif

                @if($priceList->type == 'labor')
                <div>
                    <label style="display: block; font-weight: 600; margin-bottom: 8px;">فئة العمالة</label>
                    <select name="labor_category_id"
                            style="width: 100%; padding: 12px; border: 1px solid #dee2e6; border-radius: 8px; font-size: 14px;">
                        <option value="">اختر الفئة</option>
                        @foreach($laborCategories as $category)
                            <option value="{{ $category->id }}" {{ old('labor_category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label style="display: block; font-weight: 600; margin-bottom: 8px;">نوع الأجر</label>
                    <select name="labor_rate_type"
                            style="width: 100%; padding: 12px; border: 1px solid #dee2e6; border-radius: 8px; font-size: 14px;">
                        <option value="">اختر نوع الأجر</option>
                        <option value="hourly" {{ old('labor_rate_type') == 'hourly' ? 'selected' : '' }}>ساعي</option>
                        <option value="daily" {{ old('labor_rate_type') == 'daily' ? 'selected' : '' }}>يومي</option>
                        <option value="monthly" {{ old('labor_rate_type') == 'monthly' ? 'selected' : '' }}>شهري</option>
                    </select>
                </div>
                @endif

                @if($priceList->type == 'equipment')
                <div>
                    <label style="display: block; font-weight: 600; margin-bottom: 8px;">فئة المعدات</label>
                    <select name="equipment_category_id"
                            style="width: 100%; padding: 12px; border: 1px solid #dee2e6; border-radius: 8px; font-size: 14px;">
                        <option value="">اختر الفئة</option>
                        @foreach($equipmentCategories as $category)
                            <option value="{{ $category->id }}" {{ old('equipment_category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label style="display: block; font-weight: 600; margin-bottom: 8px;">نوع التأجير</label>
                    <select name="equipment_rate_type"
                            style="width: 100%; padding: 12px; border: 1px solid #dee2e6; border-radius: 8px; font-size: 14px;">
                        <option value="">اختر نوع التأجير</option>
                        <option value="hourly" {{ old('equipment_rate_type') == 'hourly' ? 'selected' : '' }}>ساعي</option>
                        <option value="daily" {{ old('equipment_rate_type') == 'daily' ? 'selected' : '' }}>يومي</option>
                        <option value="monthly" {{ old('equipment_rate_type') == 'monthly' ? 'selected' : '' }}>شهري</option>
                    </select>
                </div>

                <div>
                    <label style="display: block; margin-top: 8px;">
                        <input type="checkbox" name="includes_operator" value="1" {{ old('includes_operator') ? 'checked' : '' }}>
                        يشمل المشغل
                    </label>
                </div>

                <div>
                    <label style="display: block; margin-top: 8px;">
                        <input type="checkbox" name="includes_fuel" value="1" {{ old('includes_fuel') ? 'checked' : '' }}>
                        يشمل الوقود
                    </label>
                </div>
                @endif
            </div>

            <div style="margin-top: 20px;">
                <label style="display: block; font-weight: 600; margin-bottom: 8px;">الوصف</label>
                <textarea name="description" rows="3"
                          style="width: 100%; padding: 12px; border: 1px solid #dee2e6; border-radius: 8px; font-size: 14px;">{{ old('description') }}</textarea>
            </div>

            <div style="margin-top: 20px;">
                <label style="display: block; font-weight: 600; margin-bottom: 8px;">المواصفات</label>
                <textarea name="specifications" rows="3"
                          style="width: 100%; padding: 12px; border: 1px solid #dee2e6; border-radius: 8px; font-size: 14px;">{{ old('specifications') }}</textarea>
            </div>

            <div style="margin-top: 24px; display: flex; gap: 12px;">
                <button type="submit"
                        style="background: var(--accent); color: white; padding: 12px 32px; border: none; border-radius: 8px; font-weight: 600; cursor: pointer;">
                    حفظ
                </button>
                <a href="{{ route('price-lists.show', $priceList) }}"
                   style="background: #6c757d; color: white; padding: 12px 32px; border-radius: 8px; text-decoration: none; font-weight: 600;">
                    إلغاء
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
