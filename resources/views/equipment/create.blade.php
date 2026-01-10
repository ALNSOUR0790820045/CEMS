@extends('layouts.app')

@section('content')
<style>
    .form-container {
        padding: 40px;
        max-width: 1200px;
        margin: 0 auto;
    }

    .form-header {
        margin-bottom: 30px;
    }

    .form-title {
        font-size: 2rem;
        font-weight: 700;
        color: var(--text);
        margin-bottom: 10px;
    }

    .form-card {
        background: white;
        padding: 30px;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    }

    .form-section {
        margin-bottom: 30px;
    }

    .section-title {
        font-size: 1.2rem;
        font-weight: 600;
        color: var(--text);
        margin-bottom: 20px;
        padding-bottom: 10px;
        border-bottom: 2px solid #0071e3;
    }

    .form-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 20px;
    }

    .form-group {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .form-group.full-width {
        grid-column: 1 / -1;
    }

    .form-label {
        font-size: 0.9rem;
        font-weight: 600;
        color: #333;
    }

    .form-label.required::after {
        content: ' *';
        color: #ff3b30;
    }

    .form-input, .form-select, .form-textarea {
        padding: 12px;
        border: 1px solid #ddd;
        border-radius: 8px;
        font-family: 'Cairo', sans-serif;
        font-size: 0.95rem;
        transition: all 0.2s;
    }

    .form-input:focus, .form-select:focus, .form-textarea:focus {
        outline: none;
        border-color: #0071e3;
        box-shadow: 0 0 0 3px rgba(0, 113, 227, 0.1);
    }

    .form-textarea {
        resize: vertical;
        min-height: 100px;
    }

    .form-checkbox {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 15px;
        background: #f8f9fa;
        border-radius: 8px;
    }

    .form-checkbox input {
        width: 20px;
        height: 20px;
        cursor: pointer;
    }

    .form-actions {
        display: flex;
        gap: 15px;
        justify-content: flex-start;
        padding-top: 20px;
        border-top: 1px solid #eee;
        margin-top: 30px;
    }

    .btn {
        padding: 12px 30px;
        border-radius: 8px;
        font-family: 'Cairo', sans-serif;
        font-weight: 600;
        font-size: 0.95rem;
        cursor: pointer;
        transition: all 0.2s;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .btn-primary {
        background: #0071e3;
        color: white;
        border: none;
    }

    .btn-primary:hover {
        background: #0077ed;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 113, 227, 0.3);
    }

    .btn-secondary {
        background: white;
        color: #666;
        border: 1px solid #ddd;
    }

    .btn-secondary:hover {
        background: #f5f5f5;
    }

    .error-message {
        color: #ff3b30;
        font-size: 0.85rem;
        margin-top: 5px;
    }
</style>

<div class="form-container">
    <div class="form-header">
        <h1 class="form-title">إضافة معدة جديدة</h1>
    </div>

    <form method="POST" action="{{ route('equipment.store') }}" class="form-card">
        @csrf

        <!-- معلومات أساسية -->
        <div class="form-section">
            <h3 class="section-title">معلومات أساسية</h3>
            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label required">رقم المعدة</label>
                    <input type="text" name="equipment_number" class="form-input" value="{{ old('equipment_number') }}" required placeholder="EQP-001">
                    @error('equipment_number')<span class="error-message">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label class="form-label required">اسم المعدة</label>
                    <input type="text" name="name" class="form-input" value="{{ old('name') }}" required>
                    @error('name')<span class="error-message">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label class="form-label">الاسم بالإنجليزية</label>
                    <input type="text" name="name_en" class="form-input" value="{{ old('name_en') }}">
                </div>

                <div class="form-group">
                    <label class="form-label required">التصنيف</label>
                    <select name="category_id" class="form-select" required>
                        <option value="">اختر التصنيف</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('category_id')<span class="error-message">{{ $message }}</span>@enderror
                </div>

                <div class="form-group full-width">
                    <label class="form-label">الوصف</label>
                    <textarea name="description" class="form-textarea">{{ old('description') }}</textarea>
                </div>
            </div>
        </div>

        <!-- مواصفات المعدة -->
        <div class="form-section">
            <h3 class="section-title">مواصفات المعدة</h3>
            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label">العلامة التجارية</label>
                    <input type="text" name="brand" class="form-input" value="{{ old('brand') }}">
                </div>

                <div class="form-group">
                    <label class="form-label">الموديل</label>
                    <input type="text" name="model" class="form-input" value="{{ old('model') }}">
                </div>

                <div class="form-group">
                    <label class="form-label">سنة الصنع</label>
                    <input type="text" name="year" class="form-input" value="{{ old('year') }}" maxlength="4" placeholder="2024">
                </div>

                <div class="form-group">
                    <label class="form-label">الرقم التسلسلي</label>
                    <input type="text" name="serial_number" class="form-input" value="{{ old('serial_number') }}">
                </div>

                <div class="form-group">
                    <label class="form-label">رقم اللوحة</label>
                    <input type="text" name="plate_number" class="form-input" value="{{ old('plate_number') }}">
                </div>

                <div class="form-group">
                    <label class="form-label">السعة</label>
                    <input type="text" name="capacity" class="form-input" value="{{ old('capacity') }}" placeholder="مثال: 5 طن">
                </div>

                <div class="form-group">
                    <label class="form-label">القدرة</label>
                    <input type="text" name="power" class="form-input" value="{{ old('power') }}" placeholder="مثال: 150 حصان">
                </div>

                <div class="form-group">
                    <label class="form-label">نوع الوقود</label>
                    <input type="text" name="fuel_type" class="form-input" value="{{ old('fuel_type') }}" placeholder="ديزل، بنزين">
                </div>

                <div class="form-group">
                    <label class="form-label">استهلاك الوقود (لتر/ساعة)</label>
                    <input type="number" step="0.01" name="fuel_consumption" class="form-input" value="{{ old('fuel_consumption') }}">
                </div>
            </div>
        </div>

        <!-- معلومات الملكية -->
        <div class="form-section">
            <h3 class="section-title">معلومات الملكية</h3>
            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label required">نوع الملكية</label>
                    <select name="ownership" class="form-select" required>
                        <option value="owned" {{ old('ownership') == 'owned' ? 'selected' : '' }}>ملك</option>
                        <option value="rented" {{ old('ownership') == 'rented' ? 'selected' : '' }}>مستأجر</option>
                        <option value="leased" {{ old('ownership') == 'leased' ? 'selected' : '' }}>تأجير تمويلي</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">سعر الشراء</label>
                    <input type="number" step="0.01" name="purchase_price" class="form-input" value="{{ old('purchase_price') }}">
                </div>

                <div class="form-group">
                    <label class="form-label">تاريخ الشراء</label>
                    <input type="date" name="purchase_date" class="form-input" value="{{ old('purchase_date') }}">
                </div>

                <div class="form-group">
                    <label class="form-label">القيمة الحالية</label>
                    <input type="number" step="0.01" name="current_value" class="form-input" value="{{ old('current_value') }}">
                </div>

                <div class="form-group">
                    <label class="form-label">سعر الساعة</label>
                    <input type="number" step="0.01" name="hourly_rate" class="form-input" value="{{ old('hourly_rate', 0) }}">
                </div>

                <div class="form-group">
                    <label class="form-label">سعر اليوم</label>
                    <input type="number" step="0.01" name="daily_rate" class="form-input" value="{{ old('daily_rate', 0) }}">
                </div>

                <div class="form-group">
                    <label class="form-label">تكلفة التشغيل/ساعة</label>
                    <input type="number" step="0.01" name="operating_cost_per_hour" class="form-input" value="{{ old('operating_cost_per_hour', 0) }}">
                </div>
            </div>
        </div>

        <!-- الحالة والموقع -->
        <div class="form-section">
            <h3 class="section-title">الحالة والموقع</h3>
            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label required">الحالة</label>
                    <select name="status" class="form-select" required>
                        <option value="available" {{ old('status', 'available') == 'available' ? 'selected' : '' }}>متاح</option>
                        <option value="in_use" {{ old('status') == 'in_use' ? 'selected' : '' }}>قيد الاستخدام</option>
                        <option value="maintenance" {{ old('status') == 'maintenance' ? 'selected' : '' }}>صيانة</option>
                        <option value="breakdown" {{ old('status') == 'breakdown' ? 'selected' : '' }}>عطل</option>
                        <option value="disposed" {{ old('status') == 'disposed' ? 'selected' : '' }}>تم التخلص</option>
                        <option value="rented_out" {{ old('status') == 'rented_out' ? 'selected' : '' }}>مؤجر للغير</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">المشروع الحالي</label>
                    <select name="current_project_id" class="form-select">
                        <option value="">لا يوجد</option>
                        @foreach($projects as $project)
                            <option value="{{ $project->id }}" {{ old('current_project_id') == $project->id ? 'selected' : '' }}>
                                {{ $project->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">الموقع الحالي</label>
                    <input type="text" name="current_location" class="form-input" value="{{ old('current_location') }}">
                </div>

                <div class="form-group">
                    <label class="form-label">المشغل المخصص</label>
                    <select name="assigned_operator_id" class="form-select">
                        <option value="">لا يوجد</option>
                        @foreach($employees as $employee)
                            <option value="{{ $employee->id }}" {{ old('assigned_operator_id') == $employee->id ? 'selected' : '' }}>
                                {{ $employee->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <!-- معلومات الصيانة -->
        <div class="form-section">
            <h3 class="section-title">معلومات الصيانة</h3>
            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label">فترة الصيانة (ساعات)</label>
                    <input type="number" name="maintenance_interval_hours" class="form-input" value="{{ old('maintenance_interval_hours') }}" placeholder="250">
                </div>
            </div>
        </div>

        <!-- التأمين والترخيص -->
        <div class="form-section">
            <h3 class="section-title">التأمين والترخيص</h3>
            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label">شركة التأمين</label>
                    <input type="text" name="insurance_company" class="form-input" value="{{ old('insurance_company') }}">
                </div>

                <div class="form-group">
                    <label class="form-label">رقم وثيقة التأمين</label>
                    <input type="text" name="insurance_policy_number" class="form-input" value="{{ old('insurance_policy_number') }}">
                </div>

                <div class="form-group">
                    <label class="form-label">تاريخ انتهاء التأمين</label>
                    <input type="date" name="insurance_expiry_date" class="form-input" value="{{ old('insurance_expiry_date') }}">
                </div>

                <div class="form-group">
                    <label class="form-label">تاريخ انتهاء الترخيص</label>
                    <input type="date" name="registration_expiry_date" class="form-input" value="{{ old('registration_expiry_date') }}">
                </div>
            </div>
        </div>

        <!-- ملاحظات -->
        <div class="form-section">
            <h3 class="section-title">ملاحظات</h3>
            <div class="form-group full-width">
                <label class="form-label">ملاحظات</label>
                <textarea name="notes" class="form-textarea">{{ old('notes') }}</textarea>
            </div>
        </div>

        <!-- الحالة النشطة -->
        <div class="form-section">
            <div class="form-checkbox">
                <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                <label for="is_active" class="form-label" style="margin: 0;">المعدة نشطة</label>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">
                <i data-lucide="save"></i>
                حفظ المعدة
            </button>
            <a href="{{ route('equipment.index') }}" class="btn btn-secondary">
                <i data-lucide="x"></i>
                إلغاء
            </a>
        </div>
    </form>
</div>

@push('scripts')
<script>
    lucide.createIcons();
</script>
@endpush
@endsection
