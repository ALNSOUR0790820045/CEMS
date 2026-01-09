@extends('layouts.app')

@section('content')
<div style="padding: 40px; max-width: 1000px; margin: 0 auto;">
    <h1 style="margin-bottom: 30px;">إنشاء مطالبة جديدة</h1>
    
    <form method="POST" action="{{ route('claims.store') }}" style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
        @csrf
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
            <div>
                <label style="display: block; margin-bottom: 5px; font-weight: 600;">المشروع *</label>
                <select name="project_id" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
                    <option value="">اختر المشروع</option>
                    @foreach($projects as $project)
                        <option value="{{ $project->id }}">{{ $project->name }}</option>
                    @endforeach
                </select>
                @error('project_id')<span style="color: #dc3545; font-size: 0.875rem;">{{ $message }}</span>@enderror
            </div>
            
            <div>
                <label style="display: block; margin-bottom: 5px; font-weight: 600;">العقد</label>
                <select name="contract_id" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
                    <option value="">اختر العقد (اختياري)</option>
                    @foreach($contracts as $contract)
                        <option value="{{ $contract->id }}">{{ $contract->title }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div style="margin-bottom: 20px;">
            <label style="display: block; margin-bottom: 5px; font-weight: 600;">عنوان المطالبة *</label>
            <input type="text" name="title" required value="{{ old('title') }}" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
            @error('title')<span style="color: #dc3545; font-size: 0.875rem;">{{ $message }}</span>@enderror
        </div>

        <div style="margin-bottom: 20px;">
            <label style="display: block; margin-bottom: 5px; font-weight: 600;">الوصف *</label>
            <textarea name="description" required rows="4" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">{{ old('description') }}</textarea>
            @error('description')<span style="color: #dc3545; font-size: 0.875rem;">{{ $message }}</span>@enderror
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
            <div>
                <label style="display: block; margin-bottom: 5px; font-weight: 600;">نوع المطالبة *</label>
                <select name="type" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
                    <option value="cost_compensation">تعويض مالي</option>
                    <option value="time_extension">تمديد وقت</option>
                    <option value="time_and_cost">وقت ومال</option>
                    <option value="acceleration">تسريع</option>
                    <option value="disruption">إعاقة</option>
                    <option value="prolongation">إطالة</option>
                    <option value="loss_of_productivity">فقدان الإنتاجية</option>
                </select>
            </div>

            <div>
                <label style="display: block; margin-bottom: 5px; font-weight: 600;">السبب *</label>
                <select name="cause" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
                    <option value="client_delay">تأخير العميل</option>
                    <option value="design_changes">تغييرات التصميم</option>
                    <option value="differing_conditions">ظروف مختلفة</option>
                    <option value="force_majeure">قوة قاهرة</option>
                    <option value="suspension">إيقاف</option>
                    <option value="late_payment">تأخر الدفع</option>
                    <option value="acceleration_order">أمر بالتسريع</option>
                    <option value="other">أخرى</option>
                </select>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px; margin-bottom: 20px;">
            <div>
                <label style="display: block; margin-bottom: 5px; font-weight: 600;">المبلغ المطالب *</label>
                <input type="number" name="claimed_amount" required value="{{ old('claimed_amount', 0) }}" step="0.01" min="0" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
            </div>

            <div>
                <label style="display: block; margin-bottom: 5px; font-weight: 600;">الأيام المطالبة *</label>
                <input type="number" name="claimed_days" required value="{{ old('claimed_days', 0) }}" min="0" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
            </div>

            <div>
                <label style="display: block; margin-bottom: 5px; font-weight: 600;">العملة *</label>
                <select name="currency" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
                    <option value="SAR">ريال سعودي (SAR)</option>
                    <option value="USD">دولار أمريكي (USD)</option>
                    <option value="EUR">يورو (EUR)</option>
                    <option value="AED">درهم إماراتي (AED)</option>
                </select>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px; margin-bottom: 20px;">
            <div>
                <label style="display: block; margin-bottom: 5px; font-weight: 600;">تاريخ بداية الحدث *</label>
                <input type="date" name="event_start_date" required value="{{ old('event_start_date') }}" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
            </div>

            <div>
                <label style="display: block; margin-bottom: 5px; font-weight: 600;">تاريخ نهاية الحدث</label>
                <input type="date" name="event_end_date" value="{{ old('event_end_date') }}" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
            </div>

            <div>
                <label style="display: block; margin-bottom: 5px; font-weight: 600;">تاريخ الإشعار *</label>
                <input type="date" name="notice_date" required value="{{ old('notice_date') }}" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
            </div>
        </div>

        <div style="margin-bottom: 20px;">
            <label style="display: block; margin-bottom: 5px; font-weight: 600;">الأولوية *</label>
            <select name="priority" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
                <option value="low">منخفضة</option>
                <option value="medium" selected>متوسطة</option>
                <option value="high">عالية</option>
                <option value="critical">حرجة</option>
            </select>
        </div>

        <div style="margin-bottom: 20px;">
            <label style="display: block; margin-bottom: 5px; font-weight: 600;">الأساس التعاقدي</label>
            <textarea name="contractual_basis" rows="3" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">{{ old('contractual_basis') }}</textarea>
        </div>

        <div style="margin-bottom: 20px;">
            <label style="display: block; margin-bottom: 5px; font-weight: 600;">الوقائع</label>
            <textarea name="facts" rows="3" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">{{ old('facts') }}</textarea>
        </div>

        <div style="display: flex; gap: 15px;">
            <button type="submit" style="background: #0071e3; color: white; padding: 12px 30px; border: none; border-radius: 8px; cursor: pointer; font-family: 'Cairo', sans-serif; font-weight: 600;">حفظ</button>
            <a href="{{ route('claims.index') }}" style="padding: 12px 30px; text-decoration: none; color: #666; background: #f1f3f5; border-radius: 8px; display: inline-block;">إلغاء</a>
        </div>
    </form>
</div>
@endsection
