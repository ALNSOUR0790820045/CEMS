@extends('layouts.app')

@section('content')
<div style="padding: 40px; max-width: 1000px; margin: 0 auto;">
    <h1 style="margin-bottom: 30px;">إنشاء أمر تغيير جديد</h1>
    
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
    
    <form method="POST" action="{{ route('variation-orders.store') }}" style="background: white; padding: 30px; border-radius: 10px;">
        @csrf
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
            <div>
                <label style="display: block; margin-bottom: 5px; font-weight: 600;">المشروع *</label>
                <select name="project_id" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
                    <option value="">اختر المشروع</option>
                    @foreach($projects as $project)
                        <option value="{{ $project->id }}" {{ old('project_id') == $project->id ? 'selected' : '' }}>
                            {{ $project->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label style="display: block; margin-bottom: 5px; font-weight: 600;">العقد</label>
                <select name="contract_id" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
                    <option value="">اختر العقد (اختياري)</option>
                    @foreach($contracts as $contract)
                        <option value="{{ $contract->id }}" {{ old('contract_id') == $contract->id ? 'selected' : '' }}>
                            {{ $contract->title }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div style="margin-bottom: 20px;">
            <label style="display: block; margin-bottom: 5px; font-weight: 600;">العنوان *</label>
            <input type="text" name="title" value="{{ old('title') }}" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
        </div>

        <div style="margin-bottom: 20px;">
            <label style="display: block; margin-bottom: 5px; font-weight: 600;">الوصف *</label>
            <textarea name="description" required rows="4" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">{{ old('description') }}</textarea>
        </div>

        <div style="margin-bottom: 20px;">
            <label style="display: block; margin-bottom: 5px; font-weight: 600;">المبررات</label>
            <textarea name="justification" rows="3" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">{{ old('justification') }}</textarea>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
            <div>
                <label style="display: block; margin-bottom: 5px; font-weight: 600;">النوع *</label>
                <select name="type" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
                    <option value="addition" {{ old('type') == 'addition' ? 'selected' : '' }}>إضافة أعمال</option>
                    <option value="omission" {{ old('type') == 'omission' ? 'selected' : '' }}>حذف أعمال</option>
                    <option value="modification" {{ old('type') == 'modification' ? 'selected' : '' }}>تعديل أعمال</option>
                    <option value="substitution" {{ old('type') == 'substitution' ? 'selected' : '' }}>استبدال</option>
                </select>
            </div>

            <div>
                <label style="display: block; margin-bottom: 5px; font-weight: 600;">المصدر *</label>
                <select name="source" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
                    <option value="client" {{ old('source') == 'client' ? 'selected' : '' }}>طلب العميل</option>
                    <option value="consultant" {{ old('source') == 'consultant' ? 'selected' : '' }}>طلب الاستشاري</option>
                    <option value="contractor" {{ old('source') == 'contractor' ? 'selected' : '' }}>طلب المقاول</option>
                    <option value="design_change" {{ old('source') == 'design_change' ? 'selected' : '' }}>تغيير التصميم</option>
                    <option value="site_condition" {{ old('source') == 'site_condition' ? 'selected' : '' }}>ظروف الموقع</option>
                </select>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px; margin-bottom: 20px;">
            <div>
                <label style="display: block; margin-bottom: 5px; font-weight: 600;">القيمة المقدرة</label>
                <input type="number" name="estimated_value" value="{{ old('estimated_value', 0) }}" step="0.01" min="0" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
            </div>

            <div>
                <label style="display: block; margin-bottom: 5px; font-weight: 600;">العملة</label>
                <select name="currency" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
                    <option value="SAR" {{ old('currency') == 'SAR' ? 'selected' : '' }}>ريال سعودي (SAR)</option>
                    <option value="USD" {{ old('currency') == 'USD' ? 'selected' : '' }}>دولار أمريكي (USD)</option>
                    <option value="EUR" {{ old('currency') == 'EUR' ? 'selected' : '' }}>يورو (EUR)</option>
                    <option value="AED" {{ old('currency') == 'AED' ? 'selected' : '' }}>درهم إماراتي (AED)</option>
                </select>
            </div>

            <div>
                <label style="display: block; margin-bottom: 5px; font-weight: 600;">التأثير على المدة (أيام)</label>
                <input type="number" name="time_impact_days" value="{{ old('time_impact_days', 0) }}" min="0" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
            <div>
                <label style="display: block; margin-bottom: 5px; font-weight: 600;">تاريخ التحديد *</label>
                <input type="date" name="identification_date" value="{{ old('identification_date', date('Y-m-d')) }}" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
            </div>

            <div>
                <label style="display: block; margin-bottom: 5px; font-weight: 600;">الأولوية *</label>
                <select name="priority" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
                    <option value="low" {{ old('priority') == 'low' ? 'selected' : '' }}>منخفضة</option>
                    <option value="medium" {{ old('priority', 'medium') == 'medium' ? 'selected' : '' }}>متوسطة</option>
                    <option value="high" {{ old('priority') == 'high' ? 'selected' : '' }}>عالية</option>
                    <option value="critical" {{ old('priority') == 'critical' ? 'selected' : '' }}>حرجة</option>
                </select>
            </div>
        </div>

        <div style="margin-bottom: 20px;">
            <label style="display: block; margin-bottom: 5px; font-weight: 600;">ملاحظات</label>
            <textarea name="notes" rows="3" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">{{ old('notes') }}</textarea>
        </div>
        
        <div style="display: flex; gap: 15px;">
            <button type="submit" style="background: #0071e3; color: white; padding: 12px 30px; border: none; border-radius: 8px; cursor: pointer; font-family: 'Cairo', sans-serif; font-weight: 600;">
                حفظ
            </button>
            <a href="{{ route('variation-orders.index') }}" style="padding: 12px 30px; text-decoration: none; color: #666; border: 1px solid #ddd; border-radius: 8px; background: white;">
                إلغاء
            </a>
        </div>
    </form>
</div>
@endsection
