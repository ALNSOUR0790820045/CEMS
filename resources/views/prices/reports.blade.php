@extends('layouts.app')

@section('content')
<div style="padding: 20px; max-width: 1400px; margin: 0 auto;">
    <h1 style="font-size: 28px; font-weight: 600; margin-bottom: 20px;">التقارير والتحليلات</h1>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 20px; margin-bottom: 30px;">
        <div style="background: white; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); padding: 24px;">
            <div style="display: flex; align-items: center; gap: 16px; margin-bottom: 20px;">
                <div style="font-size: 48px;">📊</div>
                <div>
                    <h3 style="font-size: 18px; font-weight: 600; margin: 0 0 4px 0;">تقرير الأسعار الشامل</h3>
                    <p style="font-size: 13px; color: #6c757d; margin: 0;">عرض جميع الأسعار حسب النوع والمصدر</p>
                </div>
            </div>
            <button style="width: 100%; background: var(--accent); color: white; padding: 12px; border: none; border-radius: 8px; font-weight: 500; cursor: pointer;">
                إنشاء التقرير
            </button>
        </div>

        <div style="background: white; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); padding: 24px;">
            <div style="display: flex; align-items: center; gap: 16px; margin-bottom: 20px;">
                <div style="font-size: 48px;">📈</div>
                <div>
                    <h3 style="font-size: 18px; font-weight: 600; margin: 0 0 4px 0;">تحليل تغيرات الأسعار</h3>
                    <p style="font-size: 13px; color: #6c757d; margin: 0;">تتبع التغيرات في الأسعار عبر الزمن</p>
                </div>
            </div>
            <button style="width: 100%; background: var(--accent); color: white; padding: 12px; border: none; border-radius: 8px; font-weight: 500; cursor: pointer;">
                إنشاء التقرير
            </button>
        </div>

        <div style="background: white; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); padding: 24px;">
            <div style="display: flex; align-items: center; gap: 16px; margin-bottom: 20px;">
                <div style="font-size: 48px;">⚖️</div>
                <div>
                    <h3 style="font-size: 18px; font-weight: 600; margin: 0 0 4px 0;">مقارنة المصادر</h3>
                    <p style="font-size: 13px; color: #6c757d; margin: 0;">مقارنة الأسعار من مصادر مختلفة</p>
                </div>
            </div>
            <button style="width: 100%; background: var(--accent); color: white; padding: 12px; border: none; border-radius: 8px; font-weight: 500; cursor: pointer;">
                إنشاء التقرير
            </button>
        </div>

        <div style="background: white; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); padding: 24px;">
            <div style="display: flex; align-items: center; gap: 16px; margin-bottom: 20px;">
                <div style="font-size: 48px;">🧱</div>
                <div>
                    <h3 style="font-size: 18px; font-weight: 600; margin: 0 0 4px 0;">تقرير أسعار المواد</h3>
                    <p style="font-size: 13px; color: #6c757d; margin: 0;">قائمة شاملة بأسعار مواد البناء</p>
                </div>
            </div>
            <button style="width: 100%; background: var(--accent); color: white; padding: 12px; border: none; border-radius: 8px; font-weight: 500; cursor: pointer;">
                إنشاء التقرير
            </button>
        </div>

        <div style="background: white; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); padding: 24px;">
            <div style="display: flex; align-items: center; gap: 16px; margin-bottom: 20px;">
                <div style="font-size: 48px;">👷</div>
                <div>
                    <h3 style="font-size: 18px; font-weight: 600; margin: 0 0 4px 0;">تقرير أسعار العمالة</h3>
                    <p style="font-size: 13px; color: #6c757d; margin: 0;">قائمة شاملة بأسعار العمالة</p>
                </div>
            </div>
            <button style="width: 100%; background: var(--accent); color: white; padding: 12px; border: none; border-radius: 8px; font-weight: 500; cursor: pointer;">
                إنشاء التقرير
            </button>
        </div>

        <div style="background: white; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); padding: 24px;">
            <div style="display: flex; align-items: center; gap: 16px; margin-bottom: 20px;">
                <div style="font-size: 48px;">🚜</div>
                <div>
                    <h3 style="font-size: 18px; font-weight: 600; margin: 0 0 4px 0;">تقرير أسعار المعدات</h3>
                    <p style="font-size: 13px; color: #6c757d; margin: 0;">قائمة شاملة بأسعار المعدات</p>
                </div>
            </div>
            <button style="width: 100%; background: var(--accent); color: white; padding: 12px; border: none; border-radius: 8px; font-weight: 500; cursor: pointer;">
                إنشاء التقرير
            </button>
        </div>

        <div style="background: white; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); padding: 24px;">
            <div style="display: flex; align-items: center; gap: 16px; margin-bottom: 20px;">
                <div style="font-size: 48px;">📋</div>
                <div>
                    <h3 style="font-size: 18px; font-weight: 600; margin: 0 0 4px 0;">تقرير عروض الأسعار</h3>
                    <p style="font-size: 13px; color: #6c757d; margin: 0;">ملخص عروض الأسعار المستلمة</p>
                </div>
            </div>
            <button style="width: 100%; background: var(--accent); color: white; padding: 12px; border: none; border-radius: 8px; font-weight: 500; cursor: pointer;">
                إنشاء التقرير
            </button>
        </div>

        <div style="background: white; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); padding: 24px;">
            <div style="display: flex; align-items: center; gap: 16px; margin-bottom: 20px;">
                <div style="font-size: 48px;">💰</div>
                <div>
                    <h3 style="font-size: 18px; font-weight: 600; margin: 0 0 4px 0;">تقرير مقارنات الأسعار</h3>
                    <p style="font-size: 13px; color: #6c757d; margin: 0;">سجل مقارنات واختيارات الأسعار</p>
                </div>
            </div>
            <button style="width: 100%; background: var(--accent); color: white; padding: 12px; border: none; border-radius: 8px; font-weight: 500; cursor: pointer;">
                إنشاء التقرير
            </button>
        </div>
    </div>

    <div style="background: white; border-radius: 12px; padding: 24px; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
        <h2 style="font-size: 20px; font-weight: 600; margin-bottom: 20px;">تقرير مخصص</h2>
        
        <form>
            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; margin-bottom: 20px;">
                <div>
                    <label style="display: block; font-weight: 600; margin-bottom: 8px; font-size: 14px;">نوع التقرير</label>
                    <select style="width: 100%; padding: 12px; border: 1px solid #dee2e6; border-radius: 8px; font-size: 14px;">
                        <option>اختر النوع</option>
                        <option>أسعار المواد</option>
                        <option>أسعار العمالة</option>
                        <option>أسعار المعدات</option>
                        <option>عروض الأسعار</option>
                        <option>مقارنات الأسعار</option>
                    </select>
                </div>

                <div>
                    <label style="display: block; font-weight: 600; margin-bottom: 8px; font-size: 14px;">من تاريخ</label>
                    <input type="date" style="width: 100%; padding: 12px; border: 1px solid #dee2e6; border-radius: 8px; font-size: 14px;">
                </div>

                <div>
                    <label style="display: block; font-weight: 600; margin-bottom: 8px; font-size: 14px;">إلى تاريخ</label>
                    <input type="date" style="width: 100%; padding: 12px; border: 1px solid #dee2e6; border-radius: 8px; font-size: 14px;">
                </div>
            </div>

            <div style="display: flex; gap: 12px;">
                <button type="submit" style="background: var(--accent); color: white; padding: 12px 32px; border: none; border-radius: 8px; font-weight: 600; cursor: pointer;">
                    إنشاء التقرير
                </button>
                <button type="button" style="background: #28a745; color: white; padding: 12px 32px; border: none; border-radius: 8px; font-weight: 600; cursor: pointer;">
                    تصدير Excel
                </button>
                <button type="button" style="background: #dc3545; color: white; padding: 12px 32px; border: none; border-radius: 8px; font-weight: 600; cursor: pointer;">
                    تصدير PDF
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
