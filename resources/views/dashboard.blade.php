@extends('layouts.app')

@section('content')
<div style="max-width: 1200px; margin: 0 auto;">
    <div style="text-align: center; padding: 40px 20px 20px;">
        <h1 style="font-size: 3rem; font-weight: 200; color: #0071e3; margin-bottom: 20px;">
            مرحباً بك في CEMS ERP
        </h1>
        <p style="font-size: 1.2rem; color: #86868b; margin-bottom: 50px;">
            نظام متكامل لإدارة شركات المقاولات والإنشاءات
        </p>
    </div>

    <!-- Dashboard Cards -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(350px, 1fr)); gap: 25px; margin-top: 40px;">
        <!-- Executive Dashboard Card -->
        <a href="{{ route('dashboards.executive') }}" style="text-decoration: none;">
            <div style="background: linear-gradient(135deg, #0071e3 0%, #00c4cc 100%); border-radius: 16px; padding: 35px; box-shadow: 0 4px 15px rgba(0,113,227,0.2); transition: transform 0.2s, box-shadow 0.2s; cursor: pointer;" onmouseover="this.style.transform='translateY(-5px)'; this.style.boxShadow='0 8px 20px rgba(0,113,227,0.3)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 15px rgba(0,113,227,0.2)';">
                <div style="display: flex; align-items: center; gap: 15px; margin-bottom: 15px;">
                    <i data-lucide="layout-dashboard" style="width: 32px; height: 32px; color: white;"></i>
                    <h3 style="font-size: 1.4rem; font-weight: 600; color: white; margin: 0;">
                        لوحة التحكم التنفيذية
                    </h3>
                </div>
                <p style="color: rgba(255,255,255,0.9); font-size: 0.95rem; line-height: 1.6; margin: 0;">
                    نظرة شاملة على مؤشرات الأداء الرئيسية للمالية والمشاريع والموارد البشرية
                </p>
            </div>
        </a>

        <!-- Project Dashboard Card -->
        <a href="{{ route('dashboards.project') }}" style="text-decoration: none;">
            <div style="background: linear-gradient(135deg, #34c759 0%, #30d158 100%); border-radius: 16px; padding: 35px; box-shadow: 0 4px 15px rgba(52,199,89,0.2); transition: transform 0.2s, box-shadow 0.2s; cursor: pointer;" onmouseover="this.style.transform='translateY(-5px)'; this.style.boxShadow='0 8px 20px rgba(52,199,89,0.3)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 15px rgba(52,199,89,0.2)';">
                <div style="display: flex; align-items: center; gap: 15px; margin-bottom: 15px;">
                    <i data-lucide="folder-kanban" style="width: 32px; height: 32px; color: white;"></i>
                    <h3 style="font-size: 1.4rem; font-weight: 600; color: white; margin: 0;">
                        لوحة تحكم المشاريع
                    </h3>
                </div>
                <p style="color: rgba(255,255,255,0.9); font-size: 0.95rem; line-height: 1.6; margin: 0;">
                    متابعة تفصيلية لحالة المشاريع والميزانية والجدول الزمني ومؤشرات القيمة المكتسبة
                </p>
            </div>
        </a>

        <!-- Financial Dashboard Card -->
        <a href="{{ route('dashboards.financial') }}" style="text-decoration: none;">
            <div style="background: linear-gradient(135deg, #ff9500 0%, #ff9f0a 100%); border-radius: 16px; padding: 35px; box-shadow: 0 4px 15px rgba(255,149,0,0.2); transition: transform 0.2s, box-shadow 0.2s; cursor: pointer;" onmouseover="this.style.transform='translateY(-5px)'; this.style.boxShadow='0 8px 20px rgba(255,149,0,0.3)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 15px rgba(255,149,0,0.2)';">
                <div style="display: flex; align-items: center; gap: 15px; margin-bottom: 15px;">
                    <i data-lucide="line-chart" style="width: 32px; height: 32px; color: white;"></i>
                    <h3 style="font-size: 1.4rem; font-weight: 600; color: white; margin: 0;">
                        لوحة التحكم المالية
                    </h3>
                </div>
                <p style="color: rgba(255,255,255,0.9); font-size: 0.95rem; line-height: 1.6; margin: 0;">
                    ملخص الأرباح والخسائر، التدفق النقدي، الذمم المدينة والدائنة، والإيرادات حسب المشاريع
                </p>
            </div>
        </a>
    </div>

    <!-- Features Grid -->
    <div style="margin-top: 60px; padding: 40px 20px; background: white; border-radius: 16px; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
        <h2 style="text-align: center; font-size: 1.8rem; font-weight: 600; color: #1d1d1f; margin-bottom: 40px;">
            المزايا الرئيسية
        </h2>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 30px;">
            <div style="text-align: center;">
                <div style="width: 60px; height: 60px; background: rgba(0,113,227,0.1); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 15px;">
                    <i data-lucide="bar-chart-3" style="width: 30px; height: 30px; color: #0071e3;"></i>
                </div>
                <h4 style="font-size: 1.1rem; font-weight: 600; color: #1d1d1f; margin-bottom: 10px;">
                    مؤشرات حية
                </h4>
                <p style="color: #86868b; font-size: 0.9rem; line-height: 1.5;">
                    بيانات فورية ومحدثة لجميع المؤشرات المالية والتشغيلية
                </p>
            </div>
            
            <div style="text-align: center;">
                <div style="width: 60px; height: 60px; background: rgba(52,199,89,0.1); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 15px;">
                    <i data-lucide="pie-chart" style="width: 30px; height: 30px; color: #34c759;"></i>
                </div>
                <h4 style="font-size: 1.1rem; font-weight: 600; color: #1d1d1f; margin-bottom: 10px;">
                    رسوم بيانية تفاعلية
                </h4>
                <p style="color: #86868b; font-size: 0.9rem; line-height: 1.5;">
                    تصورات واضحة للبيانات مع مخططات متنوعة
                </p>
            </div>
            
            <div style="text-align: center;">
                <div style="width: 60px; height: 60px; background: rgba(255,149,0,0.1); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 15px;">
                    <i data-lucide="activity" style="width: 30px; height: 30px; color: #ff9500;"></i>
                </div>
                <h4 style="font-size: 1.1rem; font-weight: 600; color: #1d1d1f; margin-bottom: 10px;">
                    تحليلات متقدمة
                </h4>
                <p style="color: #86868b; font-size: 0.9rem; line-height: 1.5;">
                    مؤشرات القيمة المكتسبة وتحليل الأداء المالي
                </p>
            </div>
            
            <div style="text-align: center;">
                <div style="width: 60px; height: 60px; background: rgba(175,82,222,0.1); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 15px;">
                    <i data-lucide="settings" style="width: 30px; height: 30px; color: #af52de;"></i>
                </div>
                <h4 style="font-size: 1.1rem; font-weight: 600; color: #1d1d1f; margin-bottom: 10px;">
                    لوحات قابلة للتخصيص
                </h4>
                <p style="color: #86868b; font-size: 0.9rem; line-height: 1.5;">
                    إمكانية حفظ التخصيصات والتخطيطات المفضلة
                </p>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    lucide.createIcons();
</script>
@endpush
@endsection