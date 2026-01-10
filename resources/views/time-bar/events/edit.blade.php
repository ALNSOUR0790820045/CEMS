@extends('layouts.app')

@section('content')
<div style="padding: 40px;">
    <div style="max-width: 900px; margin: 0 auto;">
        <div style="margin-bottom: 30px;">
            <h1 style="font-size: 2rem; font-weight: 700; margin-bottom: 5px;">تعديل الحدث</h1>
            <p style="color: #86868b;">تحديث بيانات الحدث</p>
        </div>

        <form method="POST" action="{{ route('time-bar.events.update', $event->id) }}" style="background: white; padding: 40px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
            @csrf
            @method('PUT')

            <!-- Project Selection -->
            <div style="margin-bottom: 25px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #1d1d1f;">المشروع *</label>
                <select name="project_id" id="project_id" required style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-family: 'Cairo', sans-serif; font-size: 0.95rem;">
                    <option value="">اختر المشروع</option>
                    @foreach($projects as $project)
                    <option value="{{ $project->id }}" {{ $event->project_id == $project->id ? 'selected' : '' }}>{{ $project->name }}</option>
                    @endforeach
                </select>
                @error('project_id')
                <span style="color: #ff3b30; font-size: 0.85rem; margin-top: 5px; display: block;">{{ $message }}</span>
                @enderror
            </div>

            <!-- Contract Selection -->
            <div style="margin-bottom: 25px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #1d1d1f;">العقد</label>
                <select name="contract_id" id="contract_id" style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-family: 'Cairo', sans-serif; font-size: 0.95rem;">
                    <option value="">اختر العقد (اختياري)</option>
                    @foreach($contracts as $contract)
                    <option value="{{ $contract->id }}" {{ $event->contract_id == $contract->id ? 'selected' : '' }}>{{ $contract->title }} - {{ $contract->contract_number }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Event Title -->
            <div style="margin-bottom: 25px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #1d1d1f;">عنوان الحدث *</label>
                <input type="text" name="title" required value="{{ old('title', $event->title) }}" style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-family: 'Cairo', sans-serif; font-size: 0.95rem;">
                @error('title')
                <span style="color: #ff3b30; font-size: 0.85rem; margin-top: 5px; display: block;">{{ $message }}</span>
                @enderror
            </div>

            <!-- Description -->
            <div style="margin-bottom: 25px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #1d1d1f;">وصف الحدث *</label>
                <textarea name="description" required rows="5" style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-family: 'Cairo', sans-serif; font-size: 0.95rem; resize: vertical;">{{ old('description', $event->description) }}</textarea>
                @error('description')
                <span style="color: #ff3b30; font-size: 0.85rem; margin-top: 5px; display: block;">{{ $message }}</span>
                @enderror
            </div>

            <!-- Event Type -->
            <div style="margin-bottom: 25px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #1d1d1f;">نوع الحدث *</label>
                <select name="event_type" required style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-family: 'Cairo', sans-serif; font-size: 0.95rem;">
                    <option value="">اختر نوع الحدث</option>
                    <option value="delay" {{ $event->event_type == 'delay' ? 'selected' : '' }}>تأخير</option>
                    <option value="disruption" {{ $event->event_type == 'disruption' ? 'selected' : '' }}>إعاقة</option>
                    <option value="variation_instruction" {{ $event->event_type == 'variation_instruction' ? 'selected' : '' }}>تعليمات تغيير</option>
                    <option value="differing_conditions" {{ $event->event_type == 'differing_conditions' ? 'selected' : '' }}>ظروف مختلفة</option>
                    <option value="force_majeure" {{ $event->event_type == 'force_majeure' ? 'selected' : '' }}>قوة قاهرة</option>
                    <option value="suspension" {{ $event->event_type == 'suspension' ? 'selected' : '' }}>إيقاف</option>
                    <option value="client_default" {{ $event->event_type == 'client_default' ? 'selected' : '' }}>إخلال العميل</option>
                    <option value="design_error" {{ $event->event_type == 'design_error' ? 'selected' : '' }}>خطأ تصميم</option>
                    <option value="late_information" {{ $event->event_type == 'late_information' ? 'selected' : '' }}>تأخر المعلومات</option>
                    <option value="access_delay" {{ $event->event_type == 'access_delay' ? 'selected' : '' }}>تأخر الوصول للموقع</option>
                    <option value="payment_delay" {{ $event->event_type == 'payment_delay' ? 'selected' : '' }}>تأخر الدفع</option>
                    <option value="other" {{ $event->event_type == 'other' ? 'selected' : '' }}>أخرى</option>
                </select>
                @error('event_type')
                <span style="color: #ff3b30; font-size: 0.85rem; margin-top: 5px; display: block;">{{ $message }}</span>
                @enderror
            </div>

            <!-- Dates Row -->
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 25px;">
                <div>
                    <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #1d1d1f;">تاريخ وقوع الحدث *</label>
                    <input type="date" name="event_date" required value="{{ old('event_date', $event->event_date->format('Y-m-d')) }}" style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-family: 'Cairo', sans-serif; font-size: 0.95rem;">
                    @error('event_date')
                    <span style="color: #ff3b30; font-size: 0.85rem; margin-top: 5px; display: block;">{{ $message }}</span>
                    @enderror
                </div>
                <div>
                    <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #1d1d1f;">تاريخ اكتشاف الحدث *</label>
                    <input type="date" name="discovery_date" required value="{{ old('discovery_date', $event->discovery_date->format('Y-m-d')) }}" style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-family: 'Cairo', sans-serif; font-size: 0.95rem;">
                    @error('discovery_date')
                    <span style="color: #ff3b30; font-size: 0.85rem; margin-top: 5px; display: block;">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <!-- Notice Period -->
            <div style="margin-bottom: 25px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #1d1d1f;">فترة الإشعار (بالأيام)</label>
                <input type="number" name="notice_period_days" min="1" max="90" value="{{ old('notice_period_days', $event->notice_period_days) }}" style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-family: 'Cairo', sans-serif; font-size: 0.95rem;">
                <small style="color: #86868b; font-size: 0.85rem; display: block; margin-top: 5px;">الافتراضي 28 يوم (يتم أخذه من العقد إذا كان محدد)</small>
            </div>

            <!-- Impact Row -->
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 25px;">
                <div>
                    <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #1d1d1f;">التأخير المتوقع (بالأيام)</label>
                    <input type="number" name="estimated_delay_days" min="0" value="{{ old('estimated_delay_days', $event->estimated_delay_days) }}" style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-family: 'Cairo', sans-serif; font-size: 0.95rem;">
                </div>
                <div>
                    <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #1d1d1f;">التأثير المالي المتوقع</label>
                    <input type="number" name="estimated_cost_impact" min="0" step="0.01" value="{{ old('estimated_cost_impact', $event->estimated_cost_impact) }}" style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-family: 'Cairo', sans-serif; font-size: 0.95rem;">
                </div>
            </div>

            <!-- Priority and Assignment -->
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 25px;">
                <div>
                    <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #1d1d1f;">الحالة</label>
                    <select name="status" style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-family: 'Cairo', sans-serif; font-size: 0.95rem;">
                        <option value="identified" {{ $event->status == 'identified' ? 'selected' : '' }}>تم تحديده</option>
                        <option value="notice_pending" {{ $event->status == 'notice_pending' ? 'selected' : '' }}>بانتظار الإشعار</option>
                        <option value="notice_sent" {{ $event->status == 'notice_sent' ? 'selected' : '' }}>تم الإشعار</option>
                        <option value="claim_submitted" {{ $event->status == 'claim_submitted' ? 'selected' : '' }}>تم تقديم المطالبة</option>
                        <option value="resolved" {{ $event->status == 'resolved' ? 'selected' : '' }}>تمت التسوية</option>
                        <option value="time_barred" {{ $event->status == 'time_barred' ? 'selected' : '' }}>سقط الحق</option>
                        <option value="cancelled" {{ $event->status == 'cancelled' ? 'selected' : '' }}>ملغي</option>
                    </select>
                </div>
                <div>
                    <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #1d1d1f;">الأولوية</label>
                    <select name="priority" style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-family: 'Cairo', sans-serif; font-size: 0.95rem;">
                        <option value="low" {{ $event->priority == 'low' ? 'selected' : '' }}>منخفضة</option>
                        <option value="medium" {{ $event->priority == 'medium' ? 'selected' : '' }}>متوسطة</option>
                        <option value="high" {{ $event->priority == 'high' ? 'selected' : '' }}>عالية</option>
                        <option value="critical" {{ $event->priority == 'critical' ? 'selected' : '' }}>حرجة</option>
                    </select>
                </div>
            </div>

            <!-- Assignment -->
            <div style="margin-bottom: 25px;">
                <div>
                    <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #1d1d1f;">المسؤول</label>
                    <select name="assigned_to" style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-family: 'Cairo', sans-serif; font-size: 0.95rem;">
                        <option value="">اختر المسؤول (اختياري)</option>
                        <!-- Users will be loaded dynamically or from controller -->
                    </select>
                </div>
            </div>

            <!-- Notes -->
            <div style="margin-bottom: 30px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #1d1d1f;">ملاحظات</label>
                <textarea name="notes" rows="3" style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-family: 'Cairo', sans-serif; font-size: 0.95rem; resize: vertical;">{{ old('notes', $event->notes) }}</textarea>
            </div>

            <!-- Actions -->
            <div style="display: flex; gap: 15px;">
                <button type="submit" style="background: #0071e3; color: white; padding: 14px 30px; border: none; border-radius: 8px; cursor: pointer; font-family: 'Cairo', sans-serif; font-weight: 600; font-size: 1rem; display: flex; align-items: center; gap: 8px;">
                    <i data-lucide="save" style="width: 18px; height: 18px;"></i>
                    تحديث الحدث
                </button>
                <a href="{{ route('time-bar.events.show', $event->id) }}" style="padding: 14px 30px; text-decoration: none; color: #666; border: 1px solid #ddd; border-radius: 8px; font-weight: 600; display: flex; align-items: center;">إلغاء</a>
            </div>
        </form>
    </div>
</div>

<script>
    lucide.createIcons();
</script>
@endsection
