@extends('layouts.app')

@section('content')
<style>
    .page-header {
        background: white;
        padding: 25px 30px;
        border-radius: 12px;
        margin-bottom: 25px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    }

    .page-title {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--text);
        margin: 0;
    }

    .form-card {
        background: white;
        padding: 30px;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        margin-bottom: 25px;
    }

    .form-section-title {
        font-size: 1.1rem;
        font-weight: 600;
        color: var(--text);
        margin: 0 0 20px 0;
        padding-bottom: 10px;
        border-bottom: 2px solid var(--border);
    }

    .form-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
        margin-bottom: 20px;
    }

    .form-group {
        display: flex;
        flex-direction: column;
    }

    .form-group label {
        font-size: 0.9rem;
        font-weight: 600;
        color: var(--text);
        margin-bottom: 8px;
    }

    .form-group input,
    .form-group select,
    .form-group textarea {
        padding: 12px;
        border: 1px solid var(--border);
        border-radius: 8px;
        font-size: 0.9rem;
        font-family: 'Cairo', sans-serif;
        transition: all 0.2s;
    }

    .form-group input:focus,
    .form-group select:focus,
    .form-group textarea:focus {
        outline: none;
        border-color: var(--accent);
        box-shadow: 0 0 0 3px rgba(0, 113, 227, 0.1);
    }

    .form-group textarea {
        resize: vertical;
        min-height: 100px;
    }

    .checkbox-group {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 15px;
    }

    .checkbox-group input[type="checkbox"] {
        width: 18px;
        height: 18px;
        cursor: pointer;
    }

    .checkbox-group label {
        margin: 0;
        cursor: pointer;
    }

    .form-actions {
        display: flex;
        gap: 15px;
        justify-content: flex-end;
        margin-top: 30px;
    }

    .btn {
        padding: 12px 24px;
        border: none;
        border-radius: 8px;
        font-size: 0.9rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
        text-decoration: none;
        display: inline-block;
        font-family: 'Cairo', sans-serif;
    }

    .btn-primary {
        background: var(--accent);
        color: white;
    }

    .btn-primary:hover {
        background: #005bb5;
    }

    .btn-secondary {
        background: #f5f5f7;
        color: var(--text);
    }

    .btn-secondary:hover {
        background: #e5e5e7;
    }

    .required::after {
        content: ' *';
        color: #ff3b30;
    }

    .help-text {
        font-size: 0.8rem;
        color: #86868b;
        margin-top: 5px;
    }
</style>

<div class="page-header">
    <h1 class="page-title">إضافة حزمة شراء جديدة</h1>
</div>

<form method="POST" action="{{ route('tender-procurement.store', $tender->id) }}">
    @csrf

    <div class="form-card">
        <h3 class="form-section-title">المعلومات الأساسية</h3>
        
        <div class="form-grid">
            <div class="form-group">
                <label class="required">كود الحزمة</label>
                <input type="text" name="package_code" value="{{ old('package_code') }}" 
                       placeholder="PKG-001" required>
                @error('package_code')
                    <span class="help-text" style="color: #ff3b30;">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label class="required">اسم الحزمة</label>
                <input type="text" name="package_name" value="{{ old('package_name') }}" 
                       placeholder="اسم الحزمة" required>
                @error('package_name')
                    <span class="help-text" style="color: #ff3b30;">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label class="required">نوع الشراء</label>
                <select name="procurement_type" required>
                    <option value="">اختر النوع</option>
                    <option value="materials" {{ old('procurement_type') == 'materials' ? 'selected' : '' }}>مواد</option>
                    <option value="equipment" {{ old('procurement_type') == 'equipment' ? 'selected' : '' }}>معدات</option>
                    <option value="subcontract" {{ old('procurement_type') == 'subcontract' ? 'selected' : '' }}>مقاولة فرعية</option>
                    <option value="services" {{ old('procurement_type') == 'services' ? 'selected' : '' }}>خدمات</option>
                    <option value="rental" {{ old('procurement_type') == 'rental' ? 'selected' : '' }}>إيجار</option>
                </select>
                @error('procurement_type')
                    <span class="help-text" style="color: #ff3b30;">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label>التصنيف</label>
                <select name="category">
                    <option value="">اختر التصنيف</option>
                    <option value="civil" {{ old('category') == 'civil' ? 'selected' : '' }}>مدني</option>
                    <option value="structural" {{ old('category') == 'structural' ? 'selected' : '' }}>إنشائي</option>
                    <option value="architectural" {{ old('category') == 'architectural' ? 'selected' : '' }}>معماري</option>
                    <option value="electrical" {{ old('category') == 'electrical' ? 'selected' : '' }}>كهربائي</option>
                    <option value="mechanical" {{ old('category') == 'mechanical' ? 'selected' : '' }}>ميكانيكي</option>
                    <option value="plumbing" {{ old('category') == 'plumbing' ? 'selected' : '' }}>صحي</option>
                    <option value="finishing" {{ old('category') == 'finishing' ? 'selected' : '' }}>تشطيبات</option>
                    <option value="other" {{ old('category') == 'other' ? 'selected' : '' }}>أخرى</option>
                </select>
                @error('category')
                    <span class="help-text" style="color: #ff3b30;">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <div class="form-group">
            <label>الوصف</label>
            <textarea name="description" placeholder="وصف تفصيلي للحزمة">{{ old('description') }}</textarea>
            @error('description')
                <span class="help-text" style="color: #ff3b30;">{{ $message }}</span>
            @enderror
        </div>
    </div>

    <div class="form-card">
        <h3 class="form-section-title">نطاق العمل والكميات</h3>
        
        <div class="form-group">
            <label>نطاق العمل</label>
            <textarea name="scope_of_work" placeholder="تفصيل نطاق العمل والأعمال المطلوبة">{{ old('scope_of_work') }}</textarea>
            @error('scope_of_work')
                <span class="help-text" style="color: #ff3b30;">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label class="required">القيمة المقدرة (ريال)</label>
            <input type="number" name="estimated_value" value="{{ old('estimated_value', 0) }}" 
                   step="0.01" min="0" required>
            @error('estimated_value')
                <span class="help-text" style="color: #ff3b30;">{{ $message }}</span>
            @enderror
        </div>
    </div>

    <div class="form-card">
        <h3 class="form-section-title">الجدول الزمني</h3>
        
        <div class="form-grid">
            <div class="form-group">
                <label>تاريخ الحاجة</label>
                <input type="date" name="required_by_date" value="{{ old('required_by_date') }}">
                @error('required_by_date')
                    <span class="help-text" style="color: #ff3b30;">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label>مدة التوريد (أيام)</label>
                <input type="number" name="lead_time_days" value="{{ old('lead_time_days') }}" 
                       min="0" placeholder="عدد الأيام">
                @error('lead_time_days')
                    <span class="help-text" style="color: #ff3b30;">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label>تاريخ بدء الشراء</label>
                <input type="date" name="procurement_start" value="{{ old('procurement_start') }}">
                @error('procurement_start')
                    <span class="help-text" style="color: #ff3b30;">{{ $message }}</span>
                @enderror
            </div>
        </div>
    </div>

    <div class="form-card">
        <h3 class="form-section-title">استراتيجية الشراء</h3>
        
        <div class="form-grid">
            <div class="form-group">
                <label class="required">الاستراتيجية</label>
                <select name="strategy" required>
                    <option value="competitive_bidding" {{ old('strategy') == 'competitive_bidding' ? 'selected' : '' }}>منافسة</option>
                    <option value="direct_purchase" {{ old('strategy') == 'direct_purchase' ? 'selected' : '' }}>شراء مباشر</option>
                    <option value="framework_agreement" {{ old('strategy') == 'framework_agreement' ? 'selected' : '' }}>اتفاقية إطار</option>
                    <option value="preferred_supplier" {{ old('strategy') == 'preferred_supplier' ? 'selected' : '' }}>مورد مفضل</option>
                </select>
                @error('strategy')
                    <span class="help-text" style="color: #ff3b30;">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label>المسؤول</label>
                <select name="responsible_id">
                    <option value="">اختر المسؤول</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" {{ old('responsible_id') == $user->id ? 'selected' : '' }}>
                            {{ $user->name }}
                        </option>
                    @endforeach
                </select>
                @error('responsible_id')
                    <span class="help-text" style="color: #ff3b30;">{{ $message }}</span>
                @enderror
            </div>
        </div>
    </div>

    <div class="form-card">
        <h3 class="form-section-title">المتطلبات</h3>
        
        <div class="checkbox-group">
            <input type="checkbox" name="requires_technical_specs" id="requires_technical_specs" 
                   value="1" {{ old('requires_technical_specs', true) ? 'checked' : '' }}>
            <label for="requires_technical_specs">يتطلب مواصفات فنية</label>
        </div>

        <div class="checkbox-group">
            <input type="checkbox" name="requires_samples" id="requires_samples" 
                   value="1" {{ old('requires_samples') ? 'checked' : '' }}>
            <label for="requires_samples">يتطلب عينات</label>
        </div>

        <div class="checkbox-group">
            <input type="checkbox" name="requires_warranty" id="requires_warranty" 
                   value="1" {{ old('requires_warranty') ? 'checked' : '' }}>
            <label for="requires_warranty">يتطلب ضمان</label>
        </div>

        <div class="form-group" style="max-width: 300px;">
            <label>مدة الضمان (أشهر)</label>
            <input type="number" name="warranty_months" value="{{ old('warranty_months') }}" 
                   min="0" placeholder="عدد الأشهر">
            @error('warranty_months')
                <span class="help-text" style="color: #ff3b30;">{{ $message }}</span>
            @enderror
        </div>
    </div>

    <div class="form-actions">
        <a href="{{ route('tender-procurement.index', $tender->id) }}" class="btn btn-secondary">إلغاء</a>
        <button type="submit" class="btn btn-primary">حفظ الحزمة</button>
    </div>
</form>

<script>
    lucide.createIcons();
</script>
@endsection
