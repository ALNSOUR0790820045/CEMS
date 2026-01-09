@extends('layouts.app')

@section('content')
<div style="padding: 20px; max-width: 1200px; margin: 0 auto;">
    <div style="margin-bottom: 20px;">
        <a href="{{ route('price-lists.show', $priceList) }}" style="color: var(--accent); text-decoration: none; font-size: 14px;">← العودة للقائمة</a>
        <h1 style="font-size: 28px; font-weight: 600; margin: 10px 0;">تعديل قائمة الأسعار</h1>
    </div>

    <div style="background: white; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); padding: 24px;">
        <form action="{{ route('price-lists.update', $priceList) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px;">
                <div>
                    <label style="display: block; font-weight: 600; margin-bottom: 8px;">الكود *</label>
                    <input type="text" name="code" required value="{{ old('code', $priceList->code) }}"
                           style="width: 100%; padding: 12px; border: 1px solid #dee2e6; border-radius: 8px; font-size: 14px;">
                    @error('code')
                        <span style="color: #dc3545; font-size: 13px;">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label style="display: block; font-weight: 600; margin-bottom: 8px;">الاسم *</label>
                    <input type="text" name="name" required value="{{ old('name', $priceList->name) }}"
                           style="width: 100%; padding: 12px; border: 1px solid #dee2e6; border-radius: 8px; font-size: 14px;">
                    @error('name')
                        <span style="color: #dc3545; font-size: 13px;">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label style="display: block; font-weight: 600; margin-bottom: 8px;">الاسم بالإنجليزية</label>
                    <input type="text" name="name_en" value="{{ old('name_en', $priceList->name_en) }}"
                           style="width: 100%; padding: 12px; border: 1px solid #dee2e6; border-radius: 8px; font-size: 14px;">
                </div>

                <div>
                    <label style="display: block; font-weight: 600; margin-bottom: 8px;">النوع *</label>
                    <select name="type" required
                            style="width: 100%; padding: 12px; border: 1px solid #dee2e6; border-radius: 8px; font-size: 14px;">
                        <option value="">اختر النوع</option>
                        <option value="material" {{ old('type', $priceList->type) == 'material' ? 'selected' : '' }}>مواد</option>
                        <option value="labor" {{ old('type', $priceList->type) == 'labor' ? 'selected' : '' }}>عمالة</option>
                        <option value="equipment" {{ old('type', $priceList->type) == 'equipment' ? 'selected' : '' }}>معدات</option>
                        <option value="subcontract" {{ old('type', $priceList->type) == 'subcontract' ? 'selected' : '' }}>مقاولين</option>
                        <option value="composite" {{ old('type', $priceList->type) == 'composite' ? 'selected' : '' }}>مركب</option>
                    </select>
                    @error('type')
                        <span style="color: #dc3545; font-size: 13px;">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label style="display: block; font-weight: 600; margin-bottom: 8px;">المصدر *</label>
                    <select name="source" required
                            style="width: 100%; padding: 12px; border: 1px solid #dee2e6; border-radius: 8px; font-size: 14px;">
                        <option value="">اختر المصدر</option>
                        <option value="internal" {{ old('source', $priceList->source) == 'internal' ? 'selected' : '' }}>داخلي</option>
                        <option value="ministry" {{ old('source', $priceList->source) == 'ministry' ? 'selected' : '' }}>وزارة الأشغال</option>
                        <option value="syndicate" {{ old('source', $priceList->source) == 'syndicate' ? 'selected' : '' }}>النقابة</option>
                        <option value="market" {{ old('source', $priceList->source) == 'market' ? 'selected' : '' }}>السوق</option>
                        <option value="vendor" {{ old('source', $priceList->source) == 'vendor' ? 'selected' : '' }}>مورد</option>
                    </select>
                    @error('source')
                        <span style="color: #dc3545; font-size: 13px;">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label style="display: block; font-weight: 600; margin-bottom: 8px;">تاريخ السريان *</label>
                    <input type="date" name="effective_date" required value="{{ old('effective_date', $priceList->effective_date->format('Y-m-d')) }}"
                           style="width: 100%; padding: 12px; border: 1px solid #dee2e6; border-radius: 8px; font-size: 14px;">
                    @error('effective_date')
                        <span style="color: #dc3545; font-size: 13px;">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label style="display: block; font-weight: 600; margin-bottom: 8px;">تاريخ الانتهاء</label>
                    <input type="date" name="expiry_date" value="{{ old('expiry_date') }}"
                           style="width: 100%; padding: 12px; border: 1px solid #dee2e6; border-radius: 8px; font-size: 14px;">
                </div>

                <div>
                    <label style="display: block; font-weight: 600; margin-bottom: 8px;">العملة *</label>
                    <select name="currency" required
                            style="width: 100%; padding: 12px; border: 1px solid #dee2e6; border-radius: 8px; font-size: 14px;">
                        <option value="JOD" {{ old('currency', 'JOD') == 'JOD' ? 'selected' : '' }}>دينار أردني (JOD)</option>
                        <option value="USD" {{ old('currency') == 'USD' ? 'selected' : '' }}>دولار أمريكي (USD)</option>
                        <option value="EUR" {{ old('currency') == 'EUR' ? 'selected' : '' }}>يورو (EUR)</option>
                    </select>
                </div>

                <div>
                    <label style="display: block; font-weight: 600; margin-bottom: 8px;">المنطقة</label>
                    <select name="region_id"
                            style="width: 100%; padding: 12px; border: 1px solid #dee2e6; border-radius: 8px; font-size: 14px;">
                        <option value="">بدون منطقة محددة</option>
                        @foreach($regions as $region)
                            <option value="{{ $region->id }}" {{ old('region_id') == $region->id ? 'selected' : '' }}>
                                {{ $region->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div style="margin-top: 20px;">
                <label style="display: block; font-weight: 600; margin-bottom: 8px;">ملاحظات</label>
                <textarea name="notes" rows="4"
                          style="width: 100%; padding: 12px; border: 1px solid #dee2e6; border-radius: 8px; font-size: 14px;">{{ old('notes') }}</textarea>
            </div>

            <div style="margin-top: 24px; display: flex; gap: 12px;">
                <button type="submit"
                        style="background: var(--accent); color: white; padding: 12px 32px; border: none; border-radius: 8px; font-weight: 600; cursor: pointer;">
                    حفظ
                </button>
                <a href="{{ route('price-lists.index') }}"
                   style="background: #6c757d; color: white; padding: 12px 32px; border-radius: 8px; text-decoration: none; font-weight: 600;">
                    إلغاء
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
