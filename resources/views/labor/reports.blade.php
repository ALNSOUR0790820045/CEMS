@extends('layouts.app')

@section('content')
<div style="padding: 40px;">
    <h1 style="font-size: 2rem; color: #1d1d1f; margin-bottom: 30px;">تقارير العمالة</h1>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px;">
        <!-- Report Card 1 -->
        <div style="background: white; padding: 30px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
            <h3 style="color: #1d1d1f; margin-bottom: 15px;">تقرير العمالة حسب المشروع</h3>
            <p style="color: #86868b; margin-bottom: 20px;">عرض توزيع العمالة على المشاريع النشطة</p>
            <button style="background: #0071e3; color: white; padding: 10px 20px; border: none; border-radius: 6px; cursor: pointer; font-family: 'Cairo', sans-serif;">
                عرض التقرير
            </button>
        </div>

        <!-- Report Card 2 -->
        <div style="background: white; padding: 30px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
            <h3 style="color: #1d1d1f; margin-bottom: 15px;">تقرير الحضور الشهري</h3>
            <p style="color: #86868b; margin-bottom: 20px;">ملخص حضور العمال خلال الشهر</p>
            <button style="background: #0071e3; color: white; padding: 10px 20px; border: none; border-radius: 6px; cursor: pointer; font-family: 'Cairo', sans-serif;">
                عرض التقرير
            </button>
        </div>

        <!-- Report Card 3 -->
        <div style="background: white; padding: 30px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
            <h3 style="color: #1d1d1f; margin-bottom: 15px;">تقرير الإنتاجية</h3>
            <p style="color: #86868b; margin-bottom: 20px;">تحليل معدلات إنتاجية العمالة</p>
            <button style="background: #0071e3; color: white; padding: 10px 20px; border: none; border-radius: 6px; cursor: pointer; font-family: 'Cairo', sans-serif;">
                عرض التقرير
            </button>
        </div>

        <!-- Report Card 4 -->
        <div style="background: white; padding: 30px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
            <h3 style="color: #1d1d1f; margin-bottom: 15px;">الوثائق منتهية الصلاحية</h3>
            <p style="color: #86868b; margin-bottom: 20px;">عرض الوثائق القريبة من الانتهاء</p>
            <a href="{{ route('labor.expiring-documents') }}" style="background: #ff9500; color: white; padding: 10px 20px; border: none; border-radius: 6px; text-decoration: none; display: inline-block; font-family: 'Cairo', sans-serif;">
                عرض التقرير
            </a>
        </div>

        <!-- Report Card 5 -->
        <div style="background: white; padding: 30px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
            <h3 style="color: #1d1d1f; margin-bottom: 15px;">تقرير التكاليف</h3>
            <p style="color: #86868b; margin-bottom: 20px;">ملخص تكاليف العمالة حسب المشروع</p>
            <button style="background: #0071e3; color: white; padding: 10px 20px; border: none; border-radius: 6px; cursor: pointer; font-family: 'Cairo', sans-serif;">
                عرض التقرير
            </button>
        </div>

        <!-- Report Card 6 -->
        <div style="background: white; padding: 30px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
            <h3 style="color: #1d1d1f; margin-bottom: 15px;">إحصائيات عامة</h3>
            <p style="color: #86868b; margin-bottom: 20px;">إحصائيات شاملة عن العمالة</p>
            <a href="{{ route('labor.statistics') }}" style="background: #34c759; color: white; padding: 10px 20px; border: none; border-radius: 6px; text-decoration: none; display: inline-block; font-family: 'Cairo', sans-serif;">
                عرض الإحصائيات
            </a>
        </div>
    </div>
</div>

@push('scripts')
<script>
    lucide.createIcons();
</script>
@endpush
@endsection
