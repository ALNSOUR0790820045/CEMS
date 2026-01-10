@extends('layouts.app')

@section('content')
<div style="padding: 40px; max-width: 1000px; margin: 0 auto;">
    <h1 style="margin-bottom: 30px;">{{ request('type') === 'incoming' ? 'تسجيل وارد جديد' : 'إنشاء مراسلة جديدة' }}</h1>
    
    @if ($errors->any())
        <div style="background: #ff3b3022; border: 1px solid #ff3b30; padding: 15px; border-radius: 8px; margin-bottom: 20px; color: #ff3b30;">
            <ul style="margin: 0; padding-right: 20px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('correspondence.store') }}" style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
        @csrf
        
        <input type="hidden" name="type" value="{{ request('type', 'outgoing') }}">

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
            <div>
                <label style="display: block; margin-bottom: 5px; font-weight: 600;">التصنيف *</label>
                <select name="category" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
                    <option value="">اختر التصنيف</option>
                    <option value="letter">خطاب</option>
                    <option value="memo">مذكرة</option>
                    <option value="email">بريد إلكتروني</option>
                    <option value="fax">فاكس</option>
                    <option value="notice">إشعار</option>
                    <option value="instruction">تعليمات</option>
                    <option value="request">طلب</option>
                    <option value="approval">موافقة</option>
                    <option value="rejection">رفض</option>
                    <option value="claim">مطالبة</option>
                    <option value="variation">أمر تغيير</option>
                    <option value="payment">دفعة</option>
                    <option value="contract">عقد</option>
                    <option value="tender">مناقصة</option>
                    <option value="report">تقرير</option>
                    <option value="minutes">محضر اجتماع</option>
                    <option value="other">أخرى</option>
                </select>
            </div>
            
            <div>
                <label style="display: block; margin-bottom: 5px; font-weight: 600;">الأولوية *</label>
                <select name="priority" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
                    <option value="normal">عادي</option>
                    <option value="urgent">عاجل</option>
                    <option value="very_urgent">عاجل جداً</option>
                    <option value="confidential">سري</option>
                </select>
            </div>
        </div>

        <div style="margin-bottom: 20px;">
            <label style="display: block; margin-bottom: 5px; font-weight: 600;">الموضوع *</label>
            <input type="text" name="subject" required value="{{ old('subject') }}" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
        </div>

        <div style="margin-bottom: 20px;">
            <label style="display: block; margin-bottom: 5px; font-weight: 600;">ملخص</label>
            <textarea name="summary" rows="2" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif; resize: vertical;">{{ old('summary') }}</textarea>
        </div>

        <div style="margin-bottom: 20px;">
            <label style="display: block; margin-bottom: 5px; font-weight: 600;">المحتوى</label>
            <textarea name="content" rows="5" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif; resize: vertical;">{{ old('content') }}</textarea>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
            <div style="background: #f5f5f7; padding: 20px; border-radius: 8px;">
                <h3 style="margin: 0 0 15px 0; font-size: 1rem; color: #1d1d1f;">{{ request('type') === 'incoming' ? 'المرسل' : 'من' }}</h3>
                
                <div style="margin-bottom: 15px;">
                    <label style="display: block; margin-bottom: 5px; font-weight: 600; font-size: 0.9rem;">الجهة *</label>
                    <input type="text" name="from_entity" required value="{{ old('from_entity') }}" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
                </div>
                
                <div style="margin-bottom: 15px;">
                    <label style="display: block; margin-bottom: 5px; font-weight: 600; font-size: 0.9rem;">الشخص</label>
                    <input type="text" name="from_person" value="{{ old('from_person') }}" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
                </div>
                
                <div>
                    <label style="display: block; margin-bottom: 5px; font-weight: 600; font-size: 0.9rem;">المنصب</label>
                    <input type="text" name="from_position" value="{{ old('from_position') }}" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
                </div>
            </div>

            <div style="background: #f5f5f7; padding: 20px; border-radius: 8px;">
                <h3 style="margin: 0 0 15px 0; font-size: 1rem; color: #1d1d1f;">{{ request('type') === 'incoming' ? 'المستلم' : 'إلى' }}</h3>
                
                <div style="margin-bottom: 15px;">
                    <label style="display: block; margin-bottom: 5px; font-weight: 600; font-size: 0.9rem;">الجهة *</label>
                    <input type="text" name="to_entity" required value="{{ old('to_entity') }}" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
                </div>
                
                <div style="margin-bottom: 15px;">
                    <label style="display: block; margin-bottom: 5px; font-weight: 600; font-size: 0.9rem;">الشخص</label>
                    <input type="text" name="to_person" value="{{ old('to_person') }}" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
                </div>
                
                <div>
                    <label style="display: block; margin-bottom: 5px; font-weight: 600; font-size: 0.9rem;">المنصب</label>
                    <input type="text" name="to_position" value="{{ old('to_position') }}" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
                </div>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px; margin-bottom: 20px;">
            <div>
                <label style="display: block; margin-bottom: 5px; font-weight: 600;">تاريخ المستند *</label>
                <input type="date" name="document_date" required value="{{ old('document_date', date('Y-m-d')) }}" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
            </div>
            
            @if(request('type') === 'incoming')
            <div>
                <label style="display: block; margin-bottom: 5px; font-weight: 600;">تاريخ الاستلام</label>
                <input type="date" name="received_date" value="{{ old('received_date') }}" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
            </div>
            @else
            <div>
                <label style="display: block; margin-bottom: 5px; font-weight: 600;">تاريخ الإرسال</label>
                <input type="date" name="sent_date" value="{{ old('sent_date') }}" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
            </div>
            @endif
            
            <div>
                <label style="display: block; margin-bottom: 5px; font-weight: 600;">تاريخ الرد المطلوب</label>
                <input type="date" name="response_required_date" value="{{ old('response_required_date') }}" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
            </div>
        </div>

        <div style="margin-bottom: 20px;">
            <label style="display: block; margin-bottom: 5px; font-weight: 600;">الرقم المرجعي (لديهم)</label>
            <input type="text" name="their_reference" value="{{ old('their_reference') }}" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
        </div>

        <div style="margin-bottom: 20px;">
            <label style="display: flex; align-items: center; gap: 10px;">
                <input type="checkbox" name="requires_response" value="1" {{ old('requires_response') ? 'checked' : '' }} style="width: 18px; height: 18px;">
                <span style="font-weight: 600;">يتطلب رداً</span>
            </label>
        </div>

        <div style="margin-bottom: 20px;">
            <label style="display: flex; align-items: center; gap: 10px;">
                <input type="checkbox" name="is_confidential" value="1" {{ old('is_confidential') ? 'checked' : '' }} style="width: 18px; height: 18px;">
                <span style="font-weight: 600;">سري</span>
            </label>
        </div>

        <div style="margin-bottom: 20px;">
            <label style="display: block; margin-bottom: 5px; font-weight: 600;">ملاحظات</label>
            <textarea name="notes" rows="3" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif; resize: vertical;">{{ old('notes') }}</textarea>
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
