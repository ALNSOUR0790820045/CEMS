@extends('layouts.app')

@section('content')
<div style="padding: 40px; max-width: 1000px; margin: 0 auto;">
    <h1 style="margin-bottom: 30px;">استيراد WBS</h1>
    <p style="color: #86868b; margin-bottom: 30px;">{{ $tender->name }} - {{ $tender->reference_number }}</p>
    
    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px;">
        <!-- Import from Template -->
        <div style="background: white; border-radius: 12px; padding: 30px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); text-align: center;">
            <div style="width: 64px; height: 64px; margin: 0 auto 20px; background: linear-gradient(135deg, #0071e3, #00c4cc); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                <i data-lucide="layout-template" style="width: 32px; height: 32px; color: white;"></i>
            </div>
            <h3 style="margin-bottom: 10px; color: #1d1d1f;">استيراد من قالب</h3>
            <p style="color: #86868b; font-size: 0.9rem; margin-bottom: 20px;">اختر قالب WBS جاهز من المكتبة</p>
            <button onclick="showTemplateModal()" style="background: #0071e3; color: white; padding: 10px 20px; border: none; border-radius: 8px; cursor: pointer; font-family: 'Cairo', sans-serif; font-weight: 600;">اختيار قالب</button>
        </div>

        <!-- Import from Excel -->
        <div style="background: white; border-radius: 12px; padding: 30px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); text-align: center;">
            <div style="width: 64px; height: 64px; margin: 0 auto 20px; background: linear-gradient(135deg, #34c759, #30d158); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                <i data-lucide="file-spreadsheet" style="width: 32px; height: 32px; color: white;"></i>
            </div>
            <h3 style="margin-bottom: 10px; color: #1d1d1f;">استيراد من Excel</h3>
            <p style="color: #86868b; font-size: 0.9rem; margin-bottom: 20px;">رفع ملف Excel يحتوي على بيانات WBS</p>
            <button onclick="showExcelModal()" style="background: #34c759; color: white; padding: 10px 20px; border: none; border-radius: 8px; cursor: pointer; font-family: 'Cairo', sans-serif; font-weight: 600;">رفع ملف</button>
        </div>

        <!-- Import from Previous Project -->
        <div style="background: white; border-radius: 12px; padding: 30px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); text-align: center;">
            <div style="width: 64px; height: 64px; margin: 0 auto 20px; background: linear-gradient(135deg, #ff9500, #ffb340); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                <i data-lucide="copy" style="width: 32px; height: 32px; color: white;"></i>
            </div>
            <h3 style="margin-bottom: 10px; color: #1d1d1f;">نسخ من مشروع سابق</h3>
            <p style="color: #86868b; font-size: 0.9rem; margin-bottom: 20px;">استخدم WBS من مشروع مماثل</p>
            <button onclick="showProjectModal()" style="background: #ff9500; color: white; padding: 10px 20px; border: none; border-radius: 8px; cursor: pointer; font-family: 'Cairo', sans-serif; font-weight: 600;">اختيار مشروع</button>
        </div>
    </div>

    <!-- Information Box -->
    <div style="background: #e3f2fd; border: 1px solid #90caf9; border-radius: 12px; padding: 20px; margin-top: 30px;">
        <h4 style="color: #0071e3; margin-bottom: 10px; display: flex; align-items: center; gap: 10px;">
            <i data-lucide="info" style="width: 20px; height: 20px;"></i>
            معلومات هامة
        </h4>
        <ul style="color: #1d1d1f; line-height: 1.8;">
            <li>يدعم النظام تسلسل هرمي حتى 5 مستويات</li>
            <li>يتم حساب التكاليف تلقائياً للعناصر التجميعية</li>
            <li>يمكن ربط عناصر WBS بعناصر BOQ</li>
            <li>صيغة ملف Excel المدعومة: .xlsx, .xls</li>
        </ul>
    </div>

    <!-- Excel Template Download -->
    <div style="background: white; border-radius: 12px; padding: 20px; margin-top: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
        <div style="display: flex; align-items: center; justify-content: space-between;">
            <div>
                <h4 style="color: #1d1d1f; margin-bottom: 5px;">تحميل قالب Excel</h4>
                <p style="color: #86868b; font-size: 0.9rem;">قم بتحميل القالب، املأ البيانات، ثم قم برفع الملف</p>
            </div>
            <a href="#" style="background: #0071e3; color: white; padding: 10px 20px; border-radius: 8px; text-decoration: none; font-weight: 600; display: inline-flex; align-items: center; gap: 8px;">
                <i data-lucide="download" style="width: 18px; height: 18px;"></i>
                تحميل القالب
            </a>
        </div>
    </div>

    <!-- Back Button -->
    <div style="margin-top: 30px;">
        <a href="{{ route('tender-wbs.index', $tender->id) }}" style="color: #0071e3; text-decoration: none; font-weight: 600; display: inline-flex; align-items: center; gap: 5px;">
            <i data-lucide="arrow-right" style="width: 18px; height: 18px;"></i>
            العودة إلى قائمة WBS
        </a>
    </div>
</div>

<!-- Modal Placeholders (to be implemented) -->
<div id="templateModal" style="display: none;">
    <!-- Template selection modal -->
</div>

<div id="excelModal" style="display: none;">
    <!-- Excel upload modal -->
</div>

<div id="projectModal" style="display: none;">
    <!-- Project selection modal -->
</div>

@push('scripts')
<script>
    lucide.createIcons();

    function showTemplateModal() {
        alert('قريباً: اختيار قالب WBS من المكتبة');
    }

    function showExcelModal() {
        alert('قريباً: رفع ملف Excel');
    }

    function showProjectModal() {
        alert('قريباً: نسخ من مشروع سابق');
    }
</script>
@endpush
@endsection
