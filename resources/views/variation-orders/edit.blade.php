@extends('layouts.app')

@section('content')
<div style="padding: 40px; max-width: 1000px; margin: 0 auto;">
    <h1 style="margin-bottom: 30px;">تعديل أمر التغيير: {{ $variationOrder->vo_number }}</h1>
    
    @if(session('error'))
        <div style="background: #f8d7da; color: #721c24; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
            {{ session('error') }}
        </div>
    @endif

    @if($errors->any())
        <div style="background: #f8d7da; color: #721c24; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
            <ul style="margin: 0; padding-right: 20px;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    
    <form method="POST" action="{{ route('variation-orders.update', $variationOrder) }}" style="background: white; padding: 30px; border-radius: 10px;">
        @csrf
        @method('PUT')
        
        <div style="margin-bottom: 20px;">
            <label style="display: block; margin-bottom: 5px; font-weight: 600;">العنوان *</label>
            <input type="text" name="title" value="{{ old('title', $variationOrder->title) }}" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
        </div>

        <div style="margin-bottom: 20px;">
            <label style="display: block; margin-bottom: 5px; font-weight: 600;">الوصف *</label>
            <textarea name="description" required rows="4" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">{{ old('description', $variationOrder->description) }}</textarea>
        </div>

        <div style="margin-bottom: 20px;">
            <label style="display: block; margin-bottom: 5px; font-weight: 600;">المبررات</label>
            <textarea name="justification" rows="3" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">{{ old('justification', $variationOrder->justification) }}</textarea>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
            <div>
                <label style="display: block; margin-bottom: 5px; font-weight: 600;">النوع *</label>
                <select name="type" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
                    <option value="addition" {{ old('type', $variationOrder->type) == 'addition' ? 'selected' : '' }}>إضافة أعمال</option>
                    <option value="omission" {{ old('type', $variationOrder->type) == 'omission' ? 'selected' : '' }}>حذف أعمال</option>
                    <option value="modification" {{ old('type', $variationOrder->type) == 'modification' ? 'selected' : '' }}>تعديل أعمال</option>
                    <option value="substitution" {{ old('type', $variationOrder->type) == 'substitution' ? 'selected' : '' }}>استبدال</option>
                </select>
            </div>

            <div>
                <label style="display: block; margin-bottom: 5px; font-weight: 600;">المصدر *</label>
                <select name="source" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
                    <option value="client" {{ old('source', $variationOrder->source) == 'client' ? 'selected' : '' }}>طلب العميل</option>
                    <option value="consultant" {{ old('source', $variationOrder->source) == 'consultant' ? 'selected' : '' }}>طلب الاستشاري</option>
                    <option value="contractor" {{ old('source', $variationOrder->source) == 'contractor' ? 'selected' : '' }}>طلب المقاول</option>
                    <option value="design_change" {{ old('source', $variationOrder->source) == 'design_change' ? 'selected' : '' }}>تغيير التصميم</option>
                    <option value="site_condition" {{ old('source', $variationOrder->source) == 'site_condition' ? 'selected' : '' }}>ظروف الموقع</option>
                </select>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px; margin-bottom: 20px;">
            <div>
                <label style="display: block; margin-bottom: 5px; font-weight: 600;">القيمة المقدرة</label>
                <input type="number" name="estimated_value" value="{{ old('estimated_value', $variationOrder->estimated_value) }}" step="0.01" min="0" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
            </div>

            <div>
                <label style="display: block; margin-bottom: 5px; font-weight: 600;">العملة</label>
                <select name="currency" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
                    <option value="SAR" {{ old('currency', $variationOrder->currency) == 'SAR' ? 'selected' : '' }}>ريال سعودي (SAR)</option>
                    <option value="USD" {{ old('currency', $variationOrder->currency) == 'USD' ? 'selected' : '' }}>دولار أمريكي (USD)</option>
                    <option value="EUR" {{ old('currency', $variationOrder->currency) == 'EUR' ? 'selected' : '' }}>يورو (EUR)</option>
                    <option value="AED" {{ old('currency', $variationOrder->currency) == 'AED' ? 'selected' : '' }}>درهم إماراتي (AED)</option>
                </select>
            </div>

            <div>
                <label style="display: block; margin-bottom: 5px; font-weight: 600;">التأثير على المدة (أيام)</label>
                <input type="number" name="time_impact_days" value="{{ old('time_impact_days', $variationOrder->time_impact_days) }}" min="0" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
            <div>
                <label style="display: block; margin-bottom: 5px; font-weight: 600;">تاريخ التحديد *</label>
                <input type="date" name="identification_date" value="{{ old('identification_date', $variationOrder->identification_date?->format('Y-m-d')) }}" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
            </div>

            <div>
                <label style="display: block; margin-bottom: 5px; font-weight: 600;">الأولوية *</label>
                <select name="priority" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
                    <option value="low" {{ old('priority', $variationOrder->priority) == 'low' ? 'selected' : '' }}>منخفضة</option>
                    <option value="medium" {{ old('priority', $variationOrder->priority) == 'medium' ? 'selected' : '' }}>متوسطة</option>
                    <option value="high" {{ old('priority', $variationOrder->priority) == 'high' ? 'selected' : '' }}>عالية</option>
                    <option value="critical" {{ old('priority', $variationOrder->priority) == 'critical' ? 'selected' : '' }}>حرجة</option>
                </select>
            </div>
        </div>

        <div style="margin-bottom: 20px;">
            <label style="display: block; margin-bottom: 5px; font-weight: 600;">ملاحظات</label>
            <textarea name="notes" rows="3" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">{{ old('notes', $variationOrder->notes) }}</textarea>
        </div>
        
        <div style="display: flex; gap: 15px;">
            <button type="submit" style="background: #0071e3; color: white; padding: 12px 30px; border: none; border-radius: 8px; cursor: pointer; font-family: 'Cairo', sans-serif; font-weight: 600;">
                حفظ التغييرات
            </button>
            <a href="{{ route('variation-orders.show', $variationOrder) }}" style="padding: 12px 30px; text-decoration: none; color: #666; border: 1px solid #ddd; border-radius: 8px; background: white;">
                إلغاء
            </a>
        </div>
    </form>
</div>
@endsection
