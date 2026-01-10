@extends('layouts.app')

@section('content')
<style>
    .card {
        background: white;
        border-radius: 12px;
        padding: 24px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        margin-bottom: 20px;
    }

    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 30px;
    }

    .page-title {
        font-size: 2rem;
        font-weight: 700;
        color: #1d1d1f;
    }

    .tabs {
        display: flex;
        gap: 2px;
        border-bottom: 2px solid #e5e5e7;
        margin-bottom: 30px;
    }

    .tab {
        padding: 12px 24px;
        cursor: pointer;
        border: none;
        background: transparent;
        font-size: 0.9rem;
        font-weight: 600;
        color: #86868b;
        border-bottom: 3px solid transparent;
        transition: all 0.2s;
        font-family: 'Cairo', sans-serif;
    }

    .tab.active {
        color: #0071e3;
        border-bottom-color: #0071e3;
    }

    .tab-content {
        display: none;
    }

    .tab-content.active {
        display: block;
    }

    .form-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 20px;
        margin-bottom: 20px;
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
        font-size: 0.85rem;
        font-weight: 600;
        color: #1d1d1f;
    }

    .form-label.required::after {
        content: ' *';
        color: #ff3b30;
    }

    .form-control {
        padding: 10px 12px;
        border: 1px solid #d2d2d7;
        border-radius: 6px;
        font-size: 0.9rem;
        font-family: 'Cairo', sans-serif;
    }

    .form-control:focus {
        outline: none;
        border-color: #0071e3;
        box-shadow: 0 0 0 3px rgba(0, 113, 227, 0.1);
    }

    textarea.form-control {
        min-height: 100px;
        resize: vertical;
    }

    .btn {
        padding: 12px 24px;
        border-radius: 8px;
        border: none;
        font-size: 0.9rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
        font-family: 'Cairo', sans-serif;
    }

    .btn-primary {
        background: #0071e3;
        color: white;
    }

    .btn-primary:hover {
        background: #0077ed;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 113, 227, 0.3);
    }

    .btn-secondary {
        background: #f5f5f7;
        color: #1d1d1f;
    }

    .btn-secondary:hover {
        background: #e5e5e7;
    }

    .form-actions {
        display: flex;
        gap: 15px;
        justify-content: flex-end;
        margin-top: 30px;
        padding-top: 30px;
        border-top: 1px solid #e5e5e7;
    }

    .help-text {
        font-size: 0.75rem;
        color: #86868b;
        margin-top: 4px;
    }

    .checkbox-group {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .checkbox-group input[type="checkbox"] {
        width: 20px;
        height: 20px;
        cursor: pointer;
    }
</style>

<div class="page-header">
    <h1 class="page-title">تعديل العطاء</h1>
    <a href="{{ route('tenders.show', $tender) }}" class="btn btn-secondary">
        <i data-lucide="arrow-right" style="width: 18px; height: 18px;"></i>
        رجوع
    </a>
</div>

<form method="POST" action="{{ route('tenders.update', $tender) }}" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <div class="card">
        <!-- Tabs -->
        <div class="tabs">
            <button type="button" class="tab active" data-tab="basic">معلومات أساسية</button>
            <button type="button" class="tab" data-tab="classification">التصنيف</button>
            <button type="button" class="tab" data-tab="location">الموقع</button>
            <button type="button" class="tab" data-tab="dates">التواريخ المهمة</button>
            <button type="button" class="tab" data-tab="bond">كفالة العطاء</button>
            <button type="button" class="tab" data-tab="requirements">متطلبات التأهيل</button>
        </div>

        <!-- Tab 1: Basic Info -->
        <div class="tab-content active" id="basic">
            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label">الرقم المرجعي (الحكومي)</label>
                    <input type="text" name="reference_number" class="form-control" value="{{ old('reference_number', $tender->reference_number) }}">
                </div>

                <div class="form-group full-width">
                    <label class="form-label required">اسم العطاء (عربي)</label>
                    <input type="text" name="tender_name" class="form-control" value="{{ old('tender_name', $tender->tender_name) }}" required>
                    @error('tender_name')
                        <span style="color: #ff3b30; font-size: 0.8rem;">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group full-width">
                    <label class="form-label">اسم العطاء (English)</label>
                    <input type="text" name="tender_name_en" class="form-control" value="{{ old('tender_name_en') }}">
                </div>

                <div class="form-group full-width">
                    <label class="form-label required">الوصف</label>
                    <textarea name="description" class="form-control" required>{{ old('description', $tender->description) }}</textarea>
                    @error('description')
                        <span style="color: #ff3b30; font-size: 0.8rem;">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group full-width">
                    <label class="form-label">الوصف (English)</label>
                    <textarea name="description_en" class="form-control">{{ old('description_en') }}</textarea>
                </div>

                <div class="form-group">
                    <label class="form-label required">الجهة المالكة</label>
                    <input type="text" name="owner_name" class="form-control" value="{{ old('owner_name', $tender->owner_name) }}" required>
                    @error('owner_name')
                        <span style="color: #ff3b30; font-size: 0.8rem;">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">جهة الاتصال</label>
                    <input type="text" name="owner_contact" class="form-control" value="{{ old('owner_contact') }}">
                </div>

                <div class="form-group">
                    <label class="form-label">البريد الإلكتروني</label>
                    <input type="email" name="owner_email" class="form-control" value="{{ old('owner_email') }}">
                </div>

                <div class="form-group">
                    <label class="form-label">رقم الهاتف</label>
                    <input type="text" name="owner_phone" class="form-control" value="{{ old('owner_phone') }}">
                </div>
            </div>
        </div>

        <!-- Tab 2: Classification -->
        <div class="tab-content" id="classification">
            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label required">نوع العطاء</label>
                    <select name="tender_type" class="form-control" required>
                        <option value="">اختر نوع العطاء</option>
                        <option value="construction" {{ old('tender_type') == 'construction' ? 'selected' : '' }}>إنشاءات</option>
                        <option value="infrastructure" {{ old('tender_type') == 'infrastructure' ? 'selected' : '' }}>بنية تحتية</option>
                        <option value="buildings" {{ old('tender_type') == 'buildings' ? 'selected' : '' }}>مباني</option>
                        <option value="roads" {{ old('tender_type') == 'roads' ? 'selected' : '' }}>طرق</option>
                        <option value="bridges" {{ old('tender_type') == 'bridges' ? 'selected' : '' }}>جسور</option>
                        <option value="water" {{ old('tender_type') == 'water' ? 'selected' : '' }}>مياه وصرف صحي</option>
                        <option value="electrical" {{ old('tender_type') == 'electrical' ? 'selected' : '' }}>كهرباء</option>
                        <option value="mechanical" {{ old('tender_type') == 'mechanical' ? 'selected' : '' }}>ميكانيكا</option>
                        <option value="maintenance" {{ old('tender_type') == 'maintenance' ? 'selected' : '' }}>صيانة</option>
                        <option value="consultancy" {{ old('tender_type') == 'consultancy' ? 'selected' : '' }}>استشارات</option>
                        <option value="other" {{ old('tender_type') == 'other' ? 'selected' : '' }}>أخرى</option>
                    </select>
                    @error('tender_type')
                        <span style="color: #ff3b30; font-size: 0.8rem;">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label required">نوع العقد</label>
                    <select name="contract_type" class="form-control" required>
                        <option value="">اختر نوع العقد</option>
                        <option value="lump_sum" {{ old('contract_type') == 'lump_sum' ? 'selected' : '' }}>مقطوعية</option>
                        <option value="unit_price" {{ old('contract_type') == 'unit_price' ? 'selected' : '' }}>أسعار وحدات</option>
                        <option value="cost_plus" {{ old('contract_type') == 'cost_plus' ? 'selected' : '' }}>تكلفة + ربح</option>
                        <option value="time_material" {{ old('contract_type') == 'time_material' ? 'selected' : '' }}>مياومة</option>
                        <option value="design_build" {{ old('contract_type') == 'design_build' ? 'selected' : '' }}>تصميم وتنفيذ</option>
                        <option value="epc" {{ old('contract_type') == 'epc' ? 'selected' : '' }}>EPC</option>
                        <option value="bot" {{ old('contract_type') == 'bot' ? 'selected' : '' }}>BOT</option>
                        <option value="other" {{ old('contract_type') == 'other' ? 'selected' : '' }}>أخرى</option>
                    </select>
                    @error('contract_type')
                        <span style="color: #ff3b30; font-size: 0.8rem;">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">القيمة التقديرية</label>
                    <input type="number" name="estimated_value" class="form-control" step="0.01" value="{{ old('estimated_value') }}">
                </div>

                <div class="form-group">
                    <label class="form-label required">العملة</label>
                    <select name="currency_id" class="form-control" required>
                        <option value="">اختر العملة</option>
                        @foreach($currencies as $currency)
                            <option value="{{ $currency->id }}" {{ old('currency_id') == $currency->id ? 'selected' : '' }}>
                                {{ $currency->name }} ({{ $currency->code }})
                            </option>
                        @endforeach
                    </select>
                    @error('currency_id')
                        <span style="color: #ff3b30; font-size: 0.8rem;">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">المدة المقدرة (شهور)</label>
                    <input type="number" name="estimated_duration_months" class="form-control" value="{{ old('estimated_duration_months') }}">
                </div>
            </div>
        </div>

        <!-- Tab 3: Location -->
        <div class="tab-content" id="location">
            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label required">الدولة</label>
                    <select name="country_id" id="country_id" class="form-control" required>
                        <option value="">اختر الدولة</option>
                        @foreach($countries as $country)
                            <option value="{{ $country->id }}" {{ old('country_id') == $country->id ? 'selected' : '' }}>
                                {{ $country->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('country_id')
                        <span style="color: #ff3b30; font-size: 0.8rem;">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">المدينة</label>
                    <select name="city_id" id="city_id" class="form-control">
                        <option value="">اختر المدينة</option>
                    </select>
                </div>

                <div class="form-group full-width">
                    <label class="form-label">موقع المشروع</label>
                    <textarea name="project_location" class="form-control">{{ old('project_location') }}</textarea>
                    <span class="help-text">الموقع التفصيلي للمشروع</span>
                </div>
            </div>
        </div>

        <!-- Tab 4: Important Dates -->
        <div class="tab-content" id="dates">
            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label">تاريخ الإعلان</label>
                    <input type="date" name="announcement_date" class="form-control" value="{{ old('announcement_date') }}">
                </div>

                <div class="form-group">
                    <label class="form-label">بيع الوثائق من</label>
                    <input type="date" name="document_sale_start" class="form-control" value="{{ old('document_sale_start') }}">
                </div>

                <div class="form-group">
                    <label class="form-label">بيع الوثائق إلى</label>
                    <input type="date" name="document_sale_end" class="form-control" value="{{ old('document_sale_end') }}">
                </div>

                <div class="form-group">
                    <label class="form-label">سعر الوثائق</label>
                    <input type="number" name="document_price" class="form-control" step="0.01" value="{{ old('document_price', 0) }}">
                </div>

                <div class="form-group">
                    <label class="form-label">تاريخ زيارة الموقع</label>
                    <input type="date" name="site_visit_date" class="form-control" value="{{ old('site_visit_date') }}">
                </div>

                <div class="form-group">
                    <label class="form-label">وقت زيارة الموقع</label>
                    <input type="time" name="site_visit_time" class="form-control" value="{{ old('site_visit_time') }}">
                </div>

                <div class="form-group">
                    <label class="form-label">آخر موعد للاستفسارات</label>
                    <input type="date" name="questions_deadline" class="form-control" value="{{ old('questions_deadline') }}">
                </div>

                <div class="form-group" style="grid-column: 1 / -1; background: #fff3cd; padding: 15px; border-radius: 8px;">
                    <label class="form-label required" style="font-size: 1rem;">📅 آخر موعد للتقديم</label>
                    <input type="date" name="submission_deadline" class="form-control" value="{{ old('submission_deadline') }}" required style="border: 2px solid #f57c00;">
                    @error('submission_deadline')
                        <span style="color: #ff3b30; font-size: 0.8rem;">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">وقت التقديم</label>
                    <input type="time" name="submission_time" class="form-control" value="{{ old('submission_time') }}">
                </div>

                <div class="form-group">
                    <label class="form-label">تاريخ الفتح</label>
                    <input type="date" name="opening_date" class="form-control" value="{{ old('opening_date') }}">
                </div>

                <div class="form-group">
                    <label class="form-label">وقت الفتح</label>
                    <input type="time" name="opening_time" class="form-control" value="{{ old('opening_time') }}">
                </div>
            </div>
        </div>

        <!-- Tab 5: Bid Bond -->
        <div class="tab-content" id="bond">
            <div class="form-grid">
                <div class="form-group">
                    <div class="checkbox-group">
                        <input type="checkbox" name="requires_bid_bond" id="requires_bid_bond" value="1" {{ old('requires_bid_bond', true) ? 'checked' : '' }}>
                        <label class="form-label" for="requires_bid_bond" style="cursor: pointer;">كفالة العطاء مطلوبة</label>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">نسبة الكفالة (%)</label>
                    <input type="number" name="bid_bond_percentage" class="form-control" step="0.01" value="{{ old('bid_bond_percentage', 1.00) }}">
                </div>

                <div class="form-group">
                    <label class="form-label">مبلغ الكفالة</label>
                    <input type="number" name="bid_bond_amount" class="form-control" step="0.01" value="{{ old('bid_bond_amount') }}">
                    <span class="help-text">يتم حسابه تلقائياً من القيمة التقديرية</span>
                </div>

                <div class="form-group">
                    <label class="form-label">مدة صلاحية الكفالة (أيام)</label>
                    <input type="number" name="bid_bond_validity_days" class="form-control" value="{{ old('bid_bond_validity_days', 90) }}">
                </div>
            </div>
        </div>

        <!-- Tab 6: Requirements -->
        <div class="tab-content" id="requirements">
            <div class="form-grid">
                <div class="form-group full-width">
                    <label class="form-label">معايير الأهلية</label>
                    <textarea name="eligibility_criteria" class="form-control" style="min-height: 150px;">{{ old('eligibility_criteria') }}</textarea>
                    <span class="help-text">اذكر شروط ومتطلبات التأهيل</span>
                </div>

                <div class="form-group full-width">
                    <label class="form-label">الملاحظات</label>
                    <textarea name="notes" class="form-control" style="min-height: 150px;">{{ old('notes') }}</textarea>
                </div>

                <div class="form-group">
                    <label class="form-label">تعيين إلى</label>
                    <select name="assigned_to" class="form-control">
                        <option value="">اختر المسؤول</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ old('assigned_to') == $user->id ? 'selected' : '' }}>
                                {{ $user->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="form-actions">
            <button type="button" class="btn btn-secondary" id="prevTab" style="display: none;">
                السابق
            </button>
            <button type="button" class="btn btn-primary" id="nextTab">
                التالي
            </button>
            <button type="submit" class="btn btn-primary" id="submitBtn" style="display: none;">
                <i data-lucide="save" style="width: 18px; height: 18px;"></i>
                تحديث العطاء
            </button>
            <a href="{{ route('tenders.show', $tender) }}" class="btn btn-secondary">
                إلغاء
            </a>
        </div>
    </div>
</form>

<script>
    lucide.createIcons();

    // Tab switching
    const tabs = document.querySelectorAll('.tab');
    const tabContents = document.querySelectorAll('.tab-content');
    const nextBtn = document.getElementById('nextTab');
    const prevBtn = document.getElementById('prevTab');
    const submitBtn = document.getElementById('submitBtn');
    let currentTab = 0;

    tabs.forEach((tab, index) => {
        tab.addEventListener('click', () => {
            showTab(index);
        });
    });

    nextBtn.addEventListener('click', () => {
        if (currentTab < tabs.length - 1) {
            showTab(currentTab + 1);
        }
    });

    prevBtn.addEventListener('click', () => {
        if (currentTab > 0) {
            showTab(currentTab - 1);
        }
    });

    function showTab(index) {
        tabs.forEach(t => t.classList.remove('active'));
        tabContents.forEach(tc => tc.classList.remove('active'));
        
        tabs[index].classList.add('active');
        tabContents[index].classList.add('active');
        
        currentTab = index;

        // Show/hide navigation buttons
        prevBtn.style.display = currentTab > 0 ? 'block' : 'none';
        nextBtn.style.display = currentTab < tabs.length - 1 ? 'block' : 'none';
        submitBtn.style.display = currentTab === tabs.length - 1 ? 'block' : 'none';
    }
</script>
@endsection
