@extends('layouts.app')

@section('content')
<style>
    .eot-edit {
        max-width: 1000px;
        margin: 0 auto;
    }
    
    .page-header {
        background: white;
        border-radius: 12px;
        padding: 30px;
        margin-bottom: 30px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    }
    
    .page-header h1 {
        font-size: 1.8rem;
        font-weight: 700;
        margin: 0 0 10px 0;
        color: #1d1d1f;
    }
    
    .form-container {
        background: white;
        border-radius: 12px;
        padding: 40px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    }
    
    .form-group {
        margin-bottom: 25px;
    }
    
    .form-group label {
        display: block;
        margin-bottom: 8px;
        font-weight: 600;
        color: #1d1d1f;
        font-size: 0.9rem;
    }
    
    .form-group label .required {
        color: #d32f2f;
    }
    
    .form-control {
        width: 100%;
        padding: 12px;
        border: 1px solid #d1d1d6;
        border-radius: 8px;
        font-size: 0.9rem;
        font-family: 'Cairo', sans-serif;
        transition: border-color 0.2s;
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
    
    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
    }
    
    .form-row-3 {
        display: grid;
        grid-template-columns: 1fr 1fr 1fr;
        gap: 20px;
    }
    
    .btn-primary {
        background: #0071e3;
        color: white;
        border: none;
        padding: 12px 30px;
        border-radius: 8px;
        font-size: 0.9rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
    }
    
    .btn-primary:hover {
        background: #0077ed;
        transform: translateY(-2px);
    }
    
    .btn-secondary {
        background: #6c757d;
        color: white;
        border: none;
        padding: 12px 30px;
        border-radius: 8px;
        font-size: 0.9rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
        text-decoration: none;
        display: inline-block;
    }
    
    .btn-secondary:hover {
        background: #5a6268;
    }
    
    .form-actions {
        margin-top: 30px;
        display: flex;
        gap: 15px;
        justify-content: flex-end;
    }
    
    .alert {
        padding: 15px 20px;
        border-radius: 8px;
        margin-bottom: 20px;
    }
    
    .alert-danger {
        background: #ffebee;
        color: #c62828;
        border: 1px solid #ef5350;
    }
    
    .help-text {
        font-size: 0.8rem;
        color: #86868b;
        margin-top: 5px;
    }
    
    .section-title {
        font-size: 1.2rem;
        font-weight: 600;
        color: #1d1d1f;
        margin-bottom: 20px;
        padding-bottom: 10px;
        border-bottom: 2px solid #f5f5f7;
    }
</style>

<div class="eot-edit">
    <div class="page-header">
        <h1>تعديل مطالبة EOT</h1>
        <p style="color: #86868b; margin: 0;">{{ $eotClaim->eot_number }}</p>
    </div>

    <div class="form-container">
        @if ($errors->any())
            <div class="alert alert-danger">
                <strong>يرجى تصحيح الأخطاء التالية:</strong>
                <ul style="margin: 10px 0 0 20px;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('eot.update', $eotClaim) }}">
            @csrf
            @method('PUT')

            <!-- معلومات المشروع -->
            <div class="section-title">
                <i data-lucide="folder" style="width: 20px; height: 20px; vertical-align: middle;"></i>
                معلومات المشروع
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>المشروع <span class="required">*</span></label>
                    <select name="project_id" class="form-control" required>
                        <option value="">اختر المشروع</option>
                        @foreach($projects as $project)
                            <option value="{{ $project->id }}" {{ old('project_id', $eotClaim->project_id) == $project->id ? 'selected' : '' }}>
                                {{ $project->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label>ربط بـ Time Bar Claim</label>
                    <select name="time_bar_claim_id" class="form-control">
                        <option value="">لا يوجد</option>
                        @foreach($timeBarClaims as $timeBarClaim)
                            <option value="{{ $timeBarClaim->id }}" {{ old('time_bar_claim_id', $eotClaim->time_bar_claim_id) == $timeBarClaim->id ? 'selected' : '' }}>
                                {{ $timeBarClaim->claim_number }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- معلومات الحدث -->
            <div class="section-title" style="margin-top: 40px;">
                <i data-lucide="alert-circle" style="width: 20px; height: 20px; vertical-align: middle;"></i>
                معلومات الحدث
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>تاريخ المطالبة <span class="required">*</span></label>
                    <input type="date" name="claim_date" class="form-control" value="{{ old('claim_date', $eotClaim->claim_date->format('Y-m-d')) }}" required>
                </div>

                <div class="form-group">
                    <label>السبب (FIDIC) <span class="required">*</span></label>
                    <select name="cause_category" class="form-control" required>
                        <option value="">اختر السبب</option>
                        <option value="client_delay" {{ old('cause_category', $eotClaim->cause_category) == 'client_delay' ? 'selected' : '' }}>تأخير المالك (FIDIC 8.4)</option>
                        <option value="consultant_delay" {{ old('cause_category', $eotClaim->cause_category) == 'consultant_delay' ? 'selected' : '' }}>تأخير الاستشاري</option>
                        <option value="variations" {{ old('cause_category', $eotClaim->cause_category) == 'variations' ? 'selected' : '' }}>أوامر تغييرية (FIDIC 13)</option>
                        <option value="unforeseeable_conditions" {{ old('cause_category', $eotClaim->cause_category) == 'unforeseeable_conditions' ? 'selected' : '' }}>ظروف مادية غير منظورة (FIDIC 4.12)</option>
                        <option value="force_majeure" {{ old('cause_category', $eotClaim->cause_category) == 'force_majeure' ? 'selected' : '' }}>قوة قاهرة (FIDIC 19)</option>
                        <option value="weather" {{ old('cause_category', $eotClaim->cause_category) == 'weather' ? 'selected' : '' }}>طقس استثنائي</option>
                        <option value="delays_by_others" {{ old('cause_category', $eotClaim->cause_category) == 'delays_by_others' ? 'selected' : '' }}>تأخير الآخرين</option>
                        <option value="suspension" {{ old('cause_category', $eotClaim->cause_category) == 'suspension' ? 'selected' : '' }}>إيقاف الأعمال (FIDIC 8.8)</option>
                        <option value="late_drawings" {{ old('cause_category', $eotClaim->cause_category) == 'late_drawings' ? 'selected' : '' }}>تأخر المخططات</option>
                        <option value="late_approvals" {{ old('cause_category', $eotClaim->cause_category) == 'late_approvals' ? 'selected' : '' }}>تأخر الموافقات</option>
                        <option value="other" {{ old('cause_category', $eotClaim->cause_category) == 'other' ? 'selected' : '' }}>أخرى</option>
                    </select>
                </div>
            </div>

            <div class="form-row-3">
                <div class="form-group">
                    <label>تاريخ بداية الحدث <span class="required">*</span></label>
                    <input type="date" name="event_start_date" class="form-control" value="{{ old('event_start_date', $eotClaim->event_start_date->format('Y-m-d')) }}" required>
                </div>

                <div class="form-group">
                    <label>تاريخ نهاية الحدث</label>
                    <input type="date" name="event_end_date" class="form-control" value="{{ old('event_end_date', $eotClaim->event_end_date?->format('Y-m-d')) }}">
                </div>

                <div class="form-group">
                    <label>مدة الحدث (أيام) <span class="required">*</span></label>
                    <input type="number" name="event_duration_days" class="form-control" value="{{ old('event_duration_days', $eotClaim->event_duration_days) }}" min="1" required>
                </div>
            </div>

            <div class="form-group">
                <label>المادة القانونية (FIDIC)</label>
                <input type="text" name="fidic_clause_reference" class="form-control" value="{{ old('fidic_clause_reference', $eotClaim->fidic_clause_reference) }}" placeholder="مثال: FIDIC 8.4">
                <div class="help-text">مثال: FIDIC 8.4, FIDIC 19.1</div>
            </div>

            <div class="form-group">
                <label>الوصف التفصيلي للحدث <span class="required">*</span></label>
                <textarea name="event_description" class="form-control" required>{{ old('event_description', $eotClaim->event_description) }}</textarea>
                <div class="help-text">صف الحدث المسبب للتأخير بالتفصيل</div>
            </div>

            <div class="form-group">
                <label>التأثير على المشروع <span class="required">*</span></label>
                <textarea name="impact_description" class="form-control" required>{{ old('impact_description', $eotClaim->impact_description) }}</textarea>
                <div class="help-text">وضح كيف أثر هذا الحدث على الأنشطة والجدول الزمني</div>
            </div>

            <div class="form-group">
                <label>المبررات القانونية <span class="required">*</span></label>
                <textarea name="justification" class="form-control" required>{{ old('justification', $eotClaim->justification) }}</textarea>
                <div class="help-text">اذكر الأساس القانوني للمطالبة بالتمديد</div>
            </div>

            <!-- التمديد المطلوب -->
            <div class="section-title" style="margin-top: 40px;">
                <i data-lucide="calendar" style="width: 20px; height: 20px; vertical-align: middle;"></i>
                التمديد المطلوب
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>عدد الأيام المطلوبة <span class="required">*</span></label>
                    <input type="number" name="requested_days" class="form-control" value="{{ old('requested_days', $eotClaim->requested_days) }}" min="1" required>
                </div>

                <div class="form-group">
                    <label>
                        <input type="checkbox" name="affects_critical_path" value="1" {{ old('affects_critical_path', $eotClaim->affects_critical_path) ? 'checked' : '' }}>
                        يؤثر على المسار الحرج
                    </label>
                    <div class="help-text">حدد هذا الخيار إذا كان الحدث يؤثر على الأنشطة الحرجة</div>
                </div>
            </div>

            <div class="form-actions">
                <a href="{{ route('eot.show', $eotClaim) }}" class="btn-secondary">إلغاء</a>
                <button type="submit" class="btn-primary">
                    <i data-lucide="save" style="width: 16px; height: 16px; vertical-align: middle;"></i>
                    حفظ التغييرات
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    lucide.createIcons();
</script>
@endsection
