@extends('layouts.app')

@section('content')
<div style="padding: 40px; max-width: 1000px; margin: 0 auto;">
    <h1 style="margin-bottom: 30px;">تعديل المراسلة</h1>
    
    @if ($errors->any())
        <div style="background: #ff3b3022; border: 1px solid #ff3b30; padding: 15px; border-radius: 8px; margin-bottom: 20px; color: #ff3b30;">
            <ul style="margin: 0; padding-right: 20px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('correspondence.update', $correspondence) }}" style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
        @csrf
        @method('PUT')

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
            <div>
                <label style="display: block; margin-bottom: 5px; font-weight: 600;">التصنيف *</label>
                <select name="category" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
                    <option value="">اختر التصنيف</option>
                    <option value="letter" {{ old('category', $correspondence->category) === 'letter' ? 'selected' : '' }}>خطاب</option>
                    <option value="memo" {{ old('category', $correspondence->category) === 'memo' ? 'selected' : '' }}>مذكرة</option>
                    <option value="email" {{ old('category', $correspondence->category) === 'email' ? 'selected' : '' }}>بريد إلكتروني</option>
                    <option value="fax" {{ old('category', $correspondence->category) === 'fax' ? 'selected' : '' }}>فاكس</option>
                    <option value="notice" {{ old('category', $correspondence->category) === 'notice' ? 'selected' : '' }}>إشعار</option>
                    <option value="instruction" {{ old('category', $correspondence->category) === 'instruction' ? 'selected' : '' }}>تعليمات</option>
                    <option value="request" {{ old('category', $correspondence->category) === 'request' ? 'selected' : '' }}>طلب</option>
                    <option value="approval" {{ old('category', $correspondence->category) === 'approval' ? 'selected' : '' }}>موافقة</option>
                    <option value="rejection" {{ old('category', $correspondence->category) === 'rejection' ? 'selected' : '' }}>رفض</option>
                    <option value="claim" {{ old('category', $correspondence->category) === 'claim' ? 'selected' : '' }}>مطالبة</option>
                    <option value="variation" {{ old('category', $correspondence->category) === 'variation' ? 'selected' : '' }}>أمر تغيير</option>
                    <option value="payment" {{ old('category', $correspondence->category) === 'payment' ? 'selected' : '' }}>دفعة</option>
                    <option value="contract" {{ old('category', $correspondence->category) === 'contract' ? 'selected' : '' }}>عقد</option>
                    <option value="tender" {{ old('category', $correspondence->category) === 'tender' ? 'selected' : '' }}>مناقصة</option>
                    <option value="report" {{ old('category', $correspondence->category) === 'report' ? 'selected' : '' }}>تقرير</option>
                    <option value="minutes" {{ old('category', $correspondence->category) === 'minutes' ? 'selected' : '' }}>محضر اجتماع</option>
                    <option value="other" {{ old('category', $correspondence->category) === 'other' ? 'selected' : '' }}>أخرى</option>
                </select>
            </div>
            
            <div>
                <label style="display: block; margin-bottom: 5px; font-weight: 600;">الأولوية *</label>
                <select name="priority" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
                    <option value="normal" {{ old('priority', $correspondence->priority) === 'normal' ? 'selected' : '' }}>عادي</option>
                    <option value="urgent" {{ old('priority', $correspondence->priority) === 'urgent' ? 'selected' : '' }}>عاجل</option>
                    <option value="very_urgent" {{ old('priority', $correspondence->priority) === 'very_urgent' ? 'selected' : '' }}>عاجل جداً</option>
                    <option value="confidential" {{ old('priority', $correspondence->priority) === 'confidential' ? 'selected' : '' }}>سري</option>
                </select>
            </div>
        </div>

        <div style="margin-bottom: 20px;">
            <label style="display: block; margin-bottom: 5px; font-weight: 600;">الموضوع *</label>
            <input type="text" name="subject" required value="{{ old('subject', $correspondence->subject) }}" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
        </div>

        <div style="margin-bottom: 20px;">
            <label style="display: block; margin-bottom: 5px; font-weight: 600;">ملخص</label>
            <textarea name="summary" rows="2" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif; resize: vertical;">{{ old('summary', $correspondence->summary) }}</textarea>
        </div>

        <div style="margin-bottom: 20px;">
            <label style="display: block; margin-bottom: 5px; font-weight: 600;">المحتوى</label>
            <textarea name="content" rows="5" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif; resize: vertical;">{{ old('content', $correspondence->content) }}</textarea>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
            <div style="background: #f5f5f7; padding: 20px; border-radius: 8px;">
                <h3 style="margin: 0 0 15px 0; font-size: 1rem; color: #1d1d1f;">من</h3>
                
                <div style="margin-bottom: 15px;">
                    <label style="display: block; margin-bottom: 5px; font-weight: 600; font-size: 0.9rem;">الجهة *</label>
                    <input type="text" name="from_entity" required value="{{ old('from_entity', $correspondence->from_entity) }}" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
                </div>
                
                <div style="margin-bottom: 15px;">
                    <label style="display: block; margin-bottom: 5px; font-weight: 600; font-size: 0.9rem;">الشخص</label>
                    <input type="text" name="from_person" value="{{ old('from_person', $correspondence->from_person) }}" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
                </div>
                
                <div>
                    <label style="display: block; margin-bottom: 5px; font-weight: 600; font-size: 0.9rem;">المنصب</label>
                    <input type="text" name="from_position" value="{{ old('from_position', $correspondence->from_position) }}" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
                </div>
            </div>

            <div style="background: #f5f5f7; padding: 20px; border-radius: 8px;">
                <h3 style="margin: 0 0 15px 0; font-size: 1rem; color: #1d1d1f;">{{ request('type') === 'incoming' ? 'المستلم' : 'إلى' }}</h3>
                
                <div style="margin-bottom: 15px;">
                    <label style="display: block; margin-bottom: 5px; font-weight: 600; font-size: 0.9rem;">الجهة *</label>
                    <input type="text" name="to_entity" required value="{{ old('to_entity', $correspondence->to_entity) }}" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
                </div>
                
                <div style="margin-bottom: 15px;">
                    <label style="display: block; margin-bottom: 5px; font-weight: 600; font-size: 0.9rem;">الشخص</label>
                    <input type="text" name="to_person" value="{{ old('to_person', $correspondence->to_person) }}" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
                </div>
                
                <div>
                    <label style="display: block; margin-bottom: 5px; font-weight: 600; font-size: 0.9rem;">المنصب</label>
                    <input type="text" name="to_position" value="{{ old('to_position', $correspondence->to_position) }}" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
                </div>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px; margin-bottom: 20px;">
            <div>
                <label style="display: block; margin-bottom: 5px; font-weight: 600;">تاريخ المستند *</label>
                <input type="date" name="document_date" required value="{{ old('document_date', $correspondence->document_date->format('Y-m-d')) }}" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
            </div>
            
            @if(request('type') === 'incoming')
            <div>
                <label style="display: block; margin-bottom: 5px; font-weight: 600;">تاريخ الاستلام</label>
                <input type="date" name="received_date" value="{{ old('received_date', $correspondence->received_date?->format('Y-m-d')) }}" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
            </div>
            @else
            <div>
                <label style="display: block; margin-bottom: 5px; font-weight: 600;">تاريخ الإرسال</label>
                <input type="date" name="sent_date" value="{{ old('sent_date', $correspondence->sent_date?->format('Y-m-d')) }}" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
            </div>
            @endif
            
            <div>
                <label style="display: block; margin-bottom: 5px; font-weight: 600;">تاريخ الرد المطلوب</label>
                <input type="date" name="response_required_date" value="{{ old('response_required_date', $correspondence->response_required_date?->format('Y-m-d')) }}" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
            </div>
        </div>

        <div style="margin-bottom: 20px;">
            <label style="display: block; margin-bottom: 5px; font-weight: 600;">الرقم المرجعي (لديهم)</label>
            <input type="text" name="their_reference" value="{{ old('their_reference', $correspondence->their_reference) }}" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
        </div>

        <div style="margin-bottom: 20px;">
            <label style="display: flex; align-items: center; gap: 10px;">
                <input type="checkbox" name="requires_response" value="1" {{ old('requires_response', $correspondence->requires_response) ? 'checked' : '' }} style="width: 18px; height: 18px;">
                <span style="font-weight: 600;">يتطلب رداً</span>
            </label>
        </div>

        <div style="margin-bottom: 20px;">
            <label style="display: flex; align-items: center; gap: 10px;">
                <input type="checkbox" name="is_confidential" value="1" {{ old('is_confidential', $correspondence->is_confidential) ? 'checked' : '' }} style="width: 18px; height: 18px;">
                <span style="font-weight: 600;">سري</span>
            </label>
        </div>

        <div style="margin-bottom: 20px;">
            <label style="display: block; margin-bottom: 5px; font-weight: 600;">ملاحظات</label>
            <textarea name="notes" rows="3" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif; resize: vertical;">{{ old('notes', $correspondence->notes) }}</textarea>
        </div>

        <div style="display: flex; gap: 15px;">
            <button type="submit" style="background: #0071e3; color: white; padding: 12px 30px; border: none; border-radius: 8px; cursor: pointer; font-family: 'Cairo', sans-serif; font-weight: 600;">
                <i data-lucide="save" style="width: 18px; height: 18px; vertical-align: middle;"></i>
                حفظ
            </button>
            <a href="{{ route('correspondence.index') }}" style="background: #f5f5f7; color: #1d1d1f; padding: 12px 30px; border-radius: 8px; text-decoration: none; font-weight: 600; display: inline-block;">
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
