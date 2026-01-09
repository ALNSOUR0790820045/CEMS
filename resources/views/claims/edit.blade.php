@extends('layouts.app')

@section('content')
<div style="padding: 40px; max-width: 1000px; margin: 0 auto;">
    <h1 style="margin-bottom: 30px;">تعديل المطالبة: {{ $claim->claim_number }}</h1>
    
    <form method="POST" action="{{ route('claims.update', $claim) }}" style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
        @csrf
        @method('PUT')
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
            <div>
                <label style="display: block; margin-bottom: 5px; font-weight: 600;">المشروع *</label>
                <select name="project_id" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
                    @foreach($projects as $project)
                        <option value="{{ $project->id }}" {{ $claim->project_id == $project->id ? 'selected' : '' }}>{{ $project->name }}</option>
                    @endforeach
                </select>
            </div>
            
            <div>
                <label style="display: block; margin-bottom: 5px; font-weight: 600;">العقد</label>
                <select name="contract_id" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
                    <option value="">اختر العقد (اختياري)</option>
                    @foreach($contracts as $contract)
                        <option value="{{ $contract->id }}" {{ $claim->contract_id == $contract->id ? 'selected' : '' }}>{{ $contract->title }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div style="margin-bottom: 20px;">
            <label style="display: block; margin-bottom: 5px; font-weight: 600;">عنوان المطالبة *</label>
            <input type="text" name="title" required value="{{ old('title', $claim->title) }}" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
        </div>

        <div style="margin-bottom: 20px;">
            <label style="display: block; margin-bottom: 5px; font-weight: 600;">الوصف *</label>
            <textarea name="description" required rows="4" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">{{ old('description', $claim->description) }}</textarea>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
            <div>
                <label style="display: block; margin-bottom: 5px; font-weight: 600;">نوع المطالبة *</label>
                <select name="type" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
                    <option value="cost_compensation" {{ $claim->type == 'cost_compensation' ? 'selected' : '' }}>تعويض مالي</option>
                    <option value="time_extension" {{ $claim->type == 'time_extension' ? 'selected' : '' }}>تمديد وقت</option>
                    <option value="time_and_cost" {{ $claim->type == 'time_and_cost' ? 'selected' : '' }}>وقت ومال</option>
                    <option value="acceleration" {{ $claim->type == 'acceleration' ? 'selected' : '' }}>تسريع</option>
                    <option value="disruption" {{ $claim->type == 'disruption' ? 'selected' : '' }}>إعاقة</option>
                    <option value="prolongation" {{ $claim->type == 'prolongation' ? 'selected' : '' }}>إطالة</option>
                    <option value="loss_of_productivity" {{ $claim->type == 'loss_of_productivity' ? 'selected' : '' }}>فقدان الإنتاجية</option>
                </select>
            </div>

            <div>
                <label style="display: block; margin-bottom: 5px; font-weight: 600;">السبب *</label>
                <select name="cause" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
                    <option value="client_delay" {{ $claim->cause == 'client_delay' ? 'selected' : '' }}>تأخير العميل</option>
                    <option value="design_changes" {{ $claim->cause == 'design_changes' ? 'selected' : '' }}>تغييرات التصميم</option>
                    <option value="differing_conditions" {{ $claim->cause == 'differing_conditions' ? 'selected' : '' }}>ظروف مختلفة</option>
                    <option value="force_majeure" {{ $claim->cause == 'force_majeure' ? 'selected' : '' }}>قوة قاهرة</option>
                    <option value="suspension" {{ $claim->cause == 'suspension' ? 'selected' : '' }}>إيقاف</option>
                    <option value="late_payment" {{ $claim->cause == 'late_payment' ? 'selected' : '' }}>تأخر الدفع</option>
                    <option value="acceleration_order" {{ $claim->cause == 'acceleration_order' ? 'selected' : '' }}>أمر بالتسريع</option>
                    <option value="other" {{ $claim->cause == 'other' ? 'selected' : '' }}>أخرى</option>
                </select>
            </div>
        </div>

        <h3 style="margin-top: 30px; margin-bottom: 15px;">القيم المطالبة</h3>
        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px; margin-bottom: 20px;">
            <div>
                <label style="display: block; margin-bottom: 5px; font-weight: 600;">المبلغ المطالب *</label>
                <input type="number" name="claimed_amount" required value="{{ old('claimed_amount', $claim->claimed_amount) }}" step="0.01" min="0" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
            </div>

            <div>
                <label style="display: block; margin-bottom: 5px; font-weight: 600;">الأيام المطالبة *</label>
                <input type="number" name="claimed_days" required value="{{ old('claimed_days', $claim->claimed_days) }}" min="0" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
            </div>

            <div>
                <label style="display: block; margin-bottom: 5px; font-weight: 600;">العملة *</label>
                <select name="currency" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
                    <option value="SAR" {{ $claim->currency == 'SAR' ? 'selected' : '' }}>ريال سعودي (SAR)</option>
                    <option value="USD" {{ $claim->currency == 'USD' ? 'selected' : '' }}>دولار أمريكي (USD)</option>
                    <option value="EUR" {{ $claim->currency == 'EUR' ? 'selected' : '' }}>يورو (EUR)</option>
                    <option value="AED" {{ $claim->currency == 'AED' ? 'selected' : '' }}>درهم إماراتي (AED)</option>
                </select>
            </div>
        </div>

        <h3 style="margin-top: 30px; margin-bottom: 15px;">القيم المقيمة</h3>
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
            <div>
                <label style="display: block; margin-bottom: 5px; font-weight: 600;">المبلغ المقيم</label>
                <input type="number" name="assessed_amount" value="{{ old('assessed_amount', $claim->assessed_amount) }}" step="0.01" min="0" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
            </div>

            <div>
                <label style="display: block; margin-bottom: 5px; font-weight: 600;">الأيام المقيمة</label>
                <input type="number" name="assessed_days" value="{{ old('assessed_days', $claim->assessed_days) }}" min="0" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
            </div>
        </div>

        <h3 style="margin-top: 30px; margin-bottom: 15px;">القيم المعتمدة</h3>
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
            <div>
                <label style="display: block; margin-bottom: 5px; font-weight: 600;">المبلغ المعتمد</label>
                <input type="number" name="approved_amount" value="{{ old('approved_amount', $claim->approved_amount) }}" step="0.01" min="0" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
            </div>

            <div>
                <label style="display: block; margin-bottom: 5px; font-weight: 600;">الأيام المعتمدة</label>
                <input type="number" name="approved_days" value="{{ old('approved_days', $claim->approved_days) }}" min="0" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
            </div>
        </div>

        <h3 style="margin-top: 30px; margin-bottom: 15px;">التواريخ</h3>
        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px; margin-bottom: 20px;">
            <div>
                <label style="display: block; margin-bottom: 5px; font-weight: 600;">تاريخ بداية الحدث *</label>
                <input type="date" name="event_start_date" required value="{{ old('event_start_date', $claim->event_start_date?->format('Y-m-d')) }}" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
            </div>

            <div>
                <label style="display: block; margin-bottom: 5px; font-weight: 600;">تاريخ نهاية الحدث</label>
                <input type="date" name="event_end_date" value="{{ old('event_end_date', $claim->event_end_date?->format('Y-m-d')) }}" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
            </div>

            <div>
                <label style="display: block; margin-bottom: 5px; font-weight: 600;">تاريخ الإشعار *</label>
                <input type="date" name="notice_date" required value="{{ old('notice_date', $claim->notice_date?->format('Y-m-d')) }}" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px; margin-bottom: 20px;">
            <div>
                <label style="display: block; margin-bottom: 5px; font-weight: 600;">تاريخ التقديم</label>
                <input type="date" name="submission_date" value="{{ old('submission_date', $claim->submission_date?->format('Y-m-d')) }}" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
            </div>

            <div>
                <label style="display: block; margin-bottom: 5px; font-weight: 600;">تاريخ استحقاق الرد</label>
                <input type="date" name="response_due_date" value="{{ old('response_due_date', $claim->response_due_date?->format('Y-m-d')) }}" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
            </div>

            <div>
                <label style="display: block; margin-bottom: 5px; font-weight: 600;">تاريخ الرد</label>
                <input type="date" name="response_date" value="{{ old('response_date', $claim->response_date?->format('Y-m-d')) }}" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
            </div>
        </div>

        <div style="margin-bottom: 20px;">
            <label style="display: block; margin-bottom: 5px; font-weight: 600;">تاريخ التسوية</label>
            <input type="date" name="resolution_date" value="{{ old('resolution_date', $claim->resolution_date?->format('Y-m-d')) }}" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
        </div>

        <div style="margin-bottom: 20px;">
            <label style="display: block; margin-bottom: 5px; font-weight: 600;">الأولوية *</label>
            <select name="priority" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
                <option value="low" {{ $claim->priority == 'low' ? 'selected' : '' }}>منخفضة</option>
                <option value="medium" {{ $claim->priority == 'medium' ? 'selected' : '' }}>متوسطة</option>
                <option value="high" {{ $claim->priority == 'high' ? 'selected' : '' }}>عالية</option>
                <option value="critical" {{ $claim->priority == 'critical' ? 'selected' : '' }}>حرجة</option>
            </select>
        </div>

        <div style="margin-bottom: 20px;">
            <label style="display: block; margin-bottom: 5px; font-weight: 600;">الأساس التعاقدي</label>
            <textarea name="contractual_basis" rows="3" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">{{ old('contractual_basis', $claim->contractual_basis) }}</textarea>
        </div>

        <div style="margin-bottom: 20px;">
            <label style="display: block; margin-bottom: 5px; font-weight: 600;">الوقائع</label>
            <textarea name="facts" rows="3" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">{{ old('facts', $claim->facts) }}</textarea>
        </div>

        <div style="margin-bottom: 20px;">
            <label style="display: block; margin-bottom: 5px; font-weight: 600;">رد العميل</label>
            <textarea name="client_response" rows="3" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">{{ old('client_response', $claim->client_response) }}</textarea>
        </div>

        <div style="margin-bottom: 20px;">
            <label style="display: block; margin-bottom: 5px; font-weight: 600;">ملاحظات التسوية</label>
            <textarea name="resolution_notes" rows="3" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">{{ old('resolution_notes', $claim->resolution_notes) }}</textarea>
        </div>

        <div style="margin-bottom: 20px;">
            <label style="display: block; margin-bottom: 5px; font-weight: 600;">الدروس المستفادة</label>
            <textarea name="lessons_learned" rows="3" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">{{ old('lessons_learned', $claim->lessons_learned) }}</textarea>
        </div>

        <div style="display: flex; gap: 15px;">
            <button type="submit" style="background: #0071e3; color: white; padding: 12px 30px; border: none; border-radius: 8px; cursor: pointer; font-family: 'Cairo', sans-serif; font-weight: 600;">حفظ التعديلات</button>
            <a href="{{ route('claims.show', $claim) }}" style="padding: 12px 30px; text-decoration: none; color: #666; background: #f1f3f5; border-radius: 8px; display: inline-block;">إلغاء</a>
        </div>
    </form>
</div>
@endsection
