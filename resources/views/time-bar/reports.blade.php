@extends('layouts.app')

@section('content')
<div style="padding: 40px;">
    <div style="max-width: 1400px; margin: 0 auto;">
        <h1 style="font-size: 2rem; font-weight: 700; margin-bottom: 30px;">التقارير والإحصائيات</h1>

        <div style="background: white; padding: 40px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); text-align: center;">
            <i data-lucide="file-text" style="width: 80px; height: 80px; color: #86868b; margin-bottom: 20px;"></i>
            <h2 style="font-size: 1.5rem; font-weight: 700; margin-bottom: 10px;">التقارير القانونية</h2>
            <p style="color: #86868b; margin-bottom: 30px;">تقارير شاملة عن حالة المواعيد التعاقدية والإشعارات</p>
            
            <div style="display: flex; gap: 15px; justify-content: center; flex-wrap: wrap;">
                <button style="background: #0071e3; color: white; padding: 12px 24px; border: none; border-radius: 8px; cursor: pointer; font-family: 'Cairo', sans-serif; font-weight: 600;">
                    تقرير الأحداث النشطة
                </button>
                <button style="background: #ff9500; color: white; padding: 12px 24px; border: none; border-radius: 8px; cursor: pointer; font-family: 'Cairo', sans-serif; font-weight: 600;">
                    تقرير الأحداث قرب الانتهاء
                </button>
                <button style="background: #ff3b30; color: white; padding: 12px 24px; border: none; border-radius: 8px; cursor: pointer; font-family: 'Cairo', sans-serif; font-weight: 600;">
                    تقرير الأحداث المنتهية
                </button>
                <button style="background: #34c759; color: white; padding: 12px 24px; border: none; border-radius: 8px; cursor: pointer; font-family: 'Cairo', sans-serif; font-weight: 600;">
                    تقرير الإشعارات المرسلة
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    lucide.createIcons();
</script>
@endsection
