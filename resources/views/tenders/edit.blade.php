@extends('layouts.app')

@section('content')
<style>
    .container { max-width: 1000px; margin: 0 auto; padding: 20px; }
    .header { margin-bottom: 30px; }
    .header h1 { font-size: 1.8rem; font-weight: 700; margin: 0; }
    .form-card { background: white; padding: 30px; border-radius: 12px; }
    .form-section { margin-bottom: 30px; }
    .form-section h3 { font-size: 1.1rem; font-weight: 700; margin-bottom: 15px; color: #0071e3; border-bottom: 2px solid #0071e3; padding-bottom: 8px; }
    .form-row { display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px; margin-bottom: 20px; }
    .form-row-full { display: grid; grid-template-columns: 1fr; gap: 20px; margin-bottom: 20px; }
    .form-group { display: flex; flex-direction: column; gap: 5px; }
    .form-group label { font-weight: 600; font-size: 0.9rem; color: #333; }
    .form-group input, .form-group select, .form-group textarea { padding: 10px 12px; border: 1px solid #ddd; border-radius: 6px; font-family: 'Cairo', sans-serif; font-size: 0.95rem; }
    .form-group input:focus, .form-group select:focus, .form-group textarea:focus { outline: none; border-color: #0071e3; box-shadow: 0 0 0 3px rgba(0, 113, 227, 0.1); }
    .form-group textarea { resize: vertical; min-height: 100px; }
    .form-actions { display: flex; gap: 15px; justify-content: flex-end; padding-top: 20px; border-top: 1px solid #eee; }
    .btn { padding: 12px 30px; border-radius: 8px; text-decoration: none; font-weight: 600; transition: all 0.2s; display: inline-flex; align-items: center; gap: 8px; border: none; cursor: pointer; font-family: 'Cairo', sans-serif; font-size: 0.95rem; }
    .btn-primary { background: #0071e3; color: white; }
    .btn-primary:hover { background: #0077ed; transform: translateY(-1px); box-shadow: 0 4px 12px rgba(0, 113, 227, 0.3); }
    .btn-secondary { background: #f5f5f7; color: #1d1d1f; }
    .btn-secondary:hover { background: #e8e8ed; }
    .required { color: #d32f2f; }
</style>

<div class="container">
    <div class="header">
        <h1>تعديل المناقصة: {{ $tender->name }}</h1>
    </div>

    <form method="POST" action="{{ route('tenders.update', $tender) }}" class="form-card">
        @csrf
        @method('PUT')

        <div class="form-section">
            <h3>المعلومات الأساسية</h3>
            
            <div class="form-row">
                <div class="form-group">
                    <label>اسم المناقصة <span class="required">*</span></label>
                    <input type="text" name="name" required value="{{ old('name', $tender->name) }}">
                    @error('name')<small style="color: #d32f2f;">{{ $message }}</small>@enderror
                </div>
                <div class="form-group">
                    <label>الاسم بالإنجليزية</label>
                    <input type="text" name="name_en" value="{{ old('name_en', $tender->name_en) }}">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>رقم المناقصة المرجعي</label>
                    <input type="text" name="reference_number" value="{{ old('reference_number', $tender->reference_number) }}">
                </div>
                <div class="form-group">
                    <label>الأولوية</label>
                    <select name="priority">
                        <option value="medium">متوسطة</option>
                        <option value="low">منخفضة</option>
                        <option value="high">عالية</option>
                        <option value="critical">حرجة</option>
                    </select>
                </div>
            </div>

            <div class="form-row-full">
                <div class="form-group">
                    <label>الوصف</label>
                    <textarea name="description">{{ old('description') }}</textarea>
                </div>
            </div>
        </div>

        <div class="form-section">
            <h3>الجهة المالكة</h3>
            
            <div class="form-row">
                <div class="form-group">
                    <label>اختر العميل</label>
                    <select name="client_id">
                        <option value="">-- اختر --</option>
                        @foreach($clients as $client)
                        <option value="{{ $client->id }}">{{ $client->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>أو اسم الجهة</label>
                    <input type="text" name="client_name" value="{{ old('client_name') }}">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>اسم جهة الاتصال</label>
                    <input type="text" name="client_contact" value="{{ old('client_contact') }}">
                </div>
                <div class="form-group">
                    <label>هاتف جهة الاتصال</label>
                    <input type="text" name="client_phone" value="{{ old('client_phone') }}">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>بريد جهة الاتصال</label>
                    <input type="email" name="client_email" value="{{ old('client_email') }}">
                </div>
                <div class="form-group">
                    <label>القطاع</label>
                    <input type="text" name="sector" placeholder="حكومي، خاص، شبه حكومي" value="{{ old('sector') }}">
                </div>
            </div>
        </div>

        <div class="form-section">
            <h3>التصنيف والموقع</h3>
            
            <div class="form-row">
                <div class="form-group">
                    <label>نوع المناقصة <span class="required">*</span></label>
                    <select name="type" required>
                        <option value="public">عامة</option>
                        <option value="private">خاصة</option>
                        <option value="limited">محدودة</option>
                        <option value="direct_order">أمر مباشر</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>فئة المناقصة <span class="required">*</span></label>
                    <select name="category" required>
                        <option value="building">مباني</option>
                        <option value="infrastructure">بنية تحتية</option>
                        <option value="industrial">صناعي</option>
                        <option value="maintenance">صيانة</option>
                        <option value="supply">توريدات</option>
                        <option value="other">أخرى</option>
                    </select>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>الموقع</label>
                    <input type="text" name="location" value="{{ old('location') }}">
                </div>
                <div class="form-group">
                    <label>المدينة</label>
                    <input type="text" name="city" value="{{ old('city') }}">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>الدولة</label>
                    <input type="text" name="country" value="{{ old('country', 'Saudi Arabia') }}">
                </div>
                <div class="form-group"></div>
            </div>
        </div>

        <div class="form-section">
            <h3>التواريخ المهمة</h3>
            
            <div class="form-row">
                <div class="form-group">
                    <label>تاريخ الإعلان</label>
                    <input type="date" name="announcement_date" value="{{ old('announcement_date') }}">
                </div>
                <div class="form-group">
                    <label>آخر موعد لشراء الكراسة</label>
                    <input type="date" name="documents_deadline" value="{{ old('documents_deadline') }}">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>آخر موعد للاستفسارات</label>
                    <input type="date" name="questions_deadline" value="{{ old('questions_deadline') }}">
                </div>
                <div class="form-group">
                    <label>آخر موعد للتقديم <span class="required">*</span></label>
                    <input type="date" name="submission_deadline" required value="{{ old('submission_deadline') }}">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>وقت التقديم</label>
                    <input type="time" name="submission_time" value="{{ old('submission_time') }}">
                </div>
                <div class="form-group">
                    <label>تاريخ فتح المظاريف</label>
                    <input type="date" name="opening_date" value="{{ old('opening_date') }}">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>تاريخ الترسية المتوقع</label>
                    <input type="date" name="expected_award_date" value="{{ old('expected_award_date') }}">
                </div>
                <div class="form-group"></div>
            </div>
        </div>

        <div class="form-section">
            <h3>القيم المالية</h3>
            
            <div class="form-row">
                <div class="form-group">
                    <label>القيمة المقدرة</label>
                    <input type="number" step="0.01" name="estimated_value" value="{{ old('estimated_value') }}">
                </div>
                <div class="form-group">
                    <label>العملة</label>
                    <select name="currency">
                        <option value="SAR">ريال سعودي (SAR)</option>
                        <option value="USD">دولار (USD)</option>
                        <option value="EUR">يورو (EUR)</option>
                        <option value="AED">درهم إماراتي (AED)</option>
                    </select>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>تكلفة شراء الكراسة</label>
                    <input type="number" step="0.01" name="documents_cost" value="{{ old('documents_cost', 0) }}">
                </div>
                <div class="form-group">
                    <label>قيمة الضمان الابتدائي</label>
                    <input type="number" step="0.01" name="bid_bond_amount" value="{{ old('bid_bond_amount') }}">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>نسبة الضمان الابتدائي (%)</label>
                    <input type="number" step="0.01" name="bid_bond_percentage" value="{{ old('bid_bond_percentage') }}">
                </div>
                <div class="form-group"></div>
            </div>
        </div>

        <div class="form-section">
            <h3>التكليف</h3>
            
            <div class="form-row">
                <div class="form-group">
                    <label>المكلف بالمتابعة</label>
                    <select name="assigned_to">
                        <option value="">-- اختر --</option>
                        @foreach($users as $user)
                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>مسؤول التسعير</label>
                    <select name="estimator_id">
                        <option value="">-- اختر --</option>
                        @foreach($users as $user)
                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <div class="form-section">
            <h3>ملاحظات</h3>
            
            <div class="form-row-full">
                <div class="form-group">
                    <label>ملاحظات إضافية</label>
                    <textarea name="notes">{{ old('notes') }}</textarea>
                </div>
            </div>
        </div>

        <div class="form-actions">
            <a href="{{ route('tenders.index') }}" class="btn btn-secondary">إلغاء</a>
            <button type="submit" class="btn btn-primary">
                <i data-lucide="save" style="width: 18px; height: 18px;"></i>
                حفظ المناقصة
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
    lucide.createIcons();
</script>
@endpush
@endsection
