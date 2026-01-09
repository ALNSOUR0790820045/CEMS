@extends('layouts.app')

@section('content')
<div style="padding: 40px;">
    <div style="max-width: 900px; margin: 0 auto;">
        <h1 style="font-size: 2rem; color: #1d1d1f; margin-bottom: 30px;">إضافة عامل جديد</h1>

        <form method="POST" action="{{ route('labor.store') }}" style="background: white; padding: 30px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
            @csrf

            <!-- Basic Information -->
            <h3 style="color: #1d1d1f; margin-bottom: 20px; border-bottom: 2px solid #f5f5f7; padding-bottom: 10px;">المعلومات الأساسية</h3>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                <div>
                    <label style="display: block; margin-bottom: 5px; font-weight: 600; color: #1d1d1f;">الاسم *</label>
                    <input type="text" name="name" required style="width: 100%; padding: 10px; border: 1px solid #d2d2d7; border-radius: 6px; font-family: 'Cairo', sans-serif;">
                </div>
                <div>
                    <label style="display: block; margin-bottom: 5px; font-weight: 600; color: #1d1d1f;">الاسم بالإنجليزية</label>
                    <input type="text" name="name_en" style="width: 100%; padding: 10px; border: 1px solid #d2d2d7; border-radius: 6px;">
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                <div>
                    <label style="display: block; margin-bottom: 5px; font-weight: 600; color: #1d1d1f;">الفئة *</label>
                    <select name="category_id" required style="width: 100%; padding: 10px; border: 1px solid #d2d2d7; border-radius: 6px; font-family: 'Cairo', sans-serif;">
                        <option value="">اختر الفئة</option>
                        @foreach($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label style="display: block; margin-bottom: 5px; font-weight: 600; color: #1d1d1f;">الجنسية</label>
                    <input type="text" name="nationality" style="width: 100%; padding: 10px; border: 1px solid #d2d2d7; border-radius: 6px; font-family: 'Cairo', sans-serif;">
                </div>
            </div>

            <!-- Employment Information -->
            <h3 style="color: #1d1d1f; margin: 30px 0 20px; border-bottom: 2px solid #f5f5f7; padding-bottom: 10px;">معلومات التوظيف</h3>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                <div>
                    <label style="display: block; margin-bottom: 5px; font-weight: 600; color: #1d1d1f;">نوع التوظيف *</label>
                    <select name="employment_type" required style="width: 100%; padding: 10px; border: 1px solid #d2d2d7; border-radius: 6px; font-family: 'Cairo', sans-serif;">
                        <option value="permanent">دائم</option>
                        <option value="temporary">مؤقت</option>
                        <option value="subcontractor">مقاول باطن</option>
                    </select>
                </div>
                <div>
                    <label style="display: block; margin-bottom: 5px; font-weight: 600; color: #1d1d1f;">مقاول الباطن</label>
                    <select name="subcontractor_id" style="width: 100%; padding: 10px; border: 1px solid #d2d2d7; border-radius: 6px; font-family: 'Cairo', sans-serif;">
                        <option value="">لا يوجد</option>
                        @foreach($subcontractors as $subcontractor)
                        <option value="{{ $subcontractor->id }}">{{ $subcontractor->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                <div>
                    <label style="display: block; margin-bottom: 5px; font-weight: 600; color: #1d1d1f;">تاريخ الالتحاق *</label>
                    <input type="date" name="joining_date" required style="width: 100%; padding: 10px; border: 1px solid #d2d2d7; border-radius: 6px;">
                </div>
                <div>
                    <label style="display: block; margin-bottom: 5px; font-weight: 600; color: #1d1d1f;">الأجر اليومي *</label>
                    <input type="number" name="daily_wage" required step="0.01" style="width: 100%; padding: 10px; border: 1px solid #d2d2d7; border-radius: 6px;">
                </div>
                <div>
                    <label style="display: block; margin-bottom: 5px; font-weight: 600; color: #1d1d1f;">أجر الساعة الإضافية</label>
                    <input type="number" name="overtime_rate" step="0.01" style="width: 100%; padding: 10px; border: 1px solid #d2d2d7; border-radius: 6px;">
                </div>
            </div>

            <!-- Contact Information -->
            <h3 style="color: #1d1d1f; margin: 30px 0 20px; border-bottom: 2px solid #f5f5f7; padding-bottom: 10px;">معلومات الاتصال</h3>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                <div>
                    <label style="display: block; margin-bottom: 5px; font-weight: 600; color: #1d1d1f;">رقم الهاتف</label>
                    <input type="text" name="phone" style="width: 100%; padding: 10px; border: 1px solid #d2d2d7; border-radius: 6px;">
                </div>
                <div>
                    <label style="display: block; margin-bottom: 5px; font-weight: 600; color: #1d1d1f;">جهة الاتصال للطوارئ</label>
                    <input type="text" name="emergency_contact" style="width: 100%; padding: 10px; border: 1px solid #d2d2d7; border-radius: 6px; font-family: 'Cairo', sans-serif;">
                </div>
            </div>

            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 5px; font-weight: 600; color: #1d1d1f;">المهارات</label>
                <textarea name="skills" rows="3" style="width: 100%; padding: 10px; border: 1px solid #d2d2d7; border-radius: 6px; font-family: 'Cairo', sans-serif;"></textarea>
            </div>

            <!-- Submit Buttons -->
            <div style="display: flex; gap: 10px; margin-top: 30px;">
                <button type="submit" style="background: linear-gradient(135deg, #0071e3 0%, #00c4cc 100%); color: white; padding: 12px 24px; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; font-family: 'Cairo', sans-serif;">
                    حفظ
                </button>
                <a href="{{ route('labor.index') }}" style="background: #e2e3e5; color: #383d41; padding: 12px 24px; border-radius: 8px; text-decoration: none; display: inline-block;">
                    إلغاء
                </a>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    lucide.createIcons();
</script>
@endpush
@endsection
