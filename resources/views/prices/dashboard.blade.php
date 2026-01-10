@extends('layouts.app')

@section('content')
<div style="padding: 20px; max-width: 1400px; margin: 0 auto;">
    <h1 style="font-size: 32px; font-weight: 700; margin-bottom: 30px;">لوحة التحكم - قاعدة الأسعار المركزية</h1>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 20px; margin-bottom: 30px;">
        <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 16px; padding: 24px; color: white; box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);">
            <div style="font-size: 14px; opacity: 0.9; margin-bottom: 8px;">قوائم الأسعار النشطة</div>
            <div style="font-size: 36px; font-weight: 700; margin-bottom: 12px;">-</div>
            <a href="{{ route('price-lists.index') }}" style="color: white; text-decoration: none; font-size: 14px; opacity: 0.9;">عرض الكل →</a>
        </div>

        <div style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); border-radius: 16px; padding: 24px; color: white; box-shadow: 0 4px 12px rgba(245, 87, 108, 0.4);">
            <div style="font-size: 14px; opacity: 0.9; margin-bottom: 8px;">طلبات الأسعار</div>
            <div style="font-size: 36px; font-weight: 700; margin-bottom: 12px;">-</div>
            <a href="{{ route('price-requests.index') }}" style="color: white; text-decoration: none; font-size: 14px; opacity: 0.9;">عرض الكل →</a>
        </div>

        <div style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); border-radius: 16px; padding: 24px; color: white; box-shadow: 0 4px 12px rgba(79, 172, 254, 0.4);">
            <div style="font-size: 14px; opacity: 0.9; margin-bottom: 8px;">عروض الأسعار المستلمة</div>
            <div style="font-size: 36px; font-weight: 700; margin-bottom: 12px;">-</div>
            <a href="{{ route('price-requests.index') }}" style="color: white; text-decoration: none; font-size: 14px; opacity: 0.9;">عرض الكل →</a>
        </div>

        <div style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); border-radius: 16px; padding: 24px; color: white; box-shadow: 0 4px 12px rgba(67, 233, 123, 0.4);">
            <div style="font-size: 14px; opacity: 0.9; margin-bottom: 8px;">مقارنات الأسعار</div>
            <div style="font-size: 36px; font-weight: 700; margin-bottom: 12px;">-</div>
            <a href="{{ route('price-requests.index') }}" style="color: white; text-decoration: none; font-size: 14px; opacity: 0.9;">عرض الكل →</a>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-bottom: 30px;">
        <a href="{{ route('price-lists.index') }}" style="background: white; border-radius: 12px; padding: 24px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); text-decoration: none; color: inherit; transition: transform 0.2s;" onmouseover="this.style.transform='translateY(-4px)'" onmouseout="this.style.transform='translateY(0)'">
            <div style="font-size: 40px; margin-bottom: 12px;">📋</div>
            <div style="font-size: 18px; font-weight: 600; margin-bottom: 8px;">قوائم الأسعار</div>
            <div style="font-size: 14px; color: #6c757d;">إدارة قوائم الأسعار من مختلف المصادر</div>
        </a>

        <a href="{{ route('prices.search') }}" style="background: white; border-radius: 12px; padding: 24px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); text-decoration: none; color: inherit; transition: transform 0.2s;" onmouseover="this.style.transform='translateY(-4px)'" onmouseout="this.style.transform='translateY(0)'">
            <div style="font-size: 40px; margin-bottom: 12px;">🔍</div>
            <div style="font-size: 18px; font-weight: 600; margin-bottom: 8px;">البحث عن الأسعار</div>
            <div style="font-size: 14px; color: #6c757d;">بحث متقدم في قاعدة الأسعار</div>
        </a>

        <a href="{{ route('price-requests.index') }}" style="background: white; border-radius: 12px; padding: 24px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); text-decoration: none; color: inherit; transition: transform 0.2s;" onmouseover="this.style.transform='translateY(-4px)'" onmouseout="this.style.transform='translateY(0)'">
            <div style="font-size: 40px; margin-bottom: 12px;">📬</div>
            <div style="font-size: 18px; font-weight: 600; margin-bottom: 8px;">طلبات عروض الأسعار</div>
            <div style="font-size: 14px; color: #6c757d;">إدارة طلبات وعروض الأسعار</div>
        </a>

        <a href="{{ route('prices.compare') }}" style="background: white; border-radius: 12px; padding: 24px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); text-decoration: none; color: inherit; transition: transform 0.2s;" onmouseover="this.style.transform='translateY(-4px)'" onmouseout="this.style.transform='translateY(0)'">
            <div style="font-size: 40px; margin-bottom: 12px;">⚖️</div>
            <div style="font-size: 18px; font-weight: 600; margin-bottom: 8px;">مقارنة الأسعار</div>
            <div style="font-size: 14px; color: #6c757d;">مقارنة أسعار من مصادر مختلفة</div>
        </a>

        <a href="{{ route('prices.materials') }}" style="background: white; border-radius: 12px; padding: 24px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); text-decoration: none; color: inherit; transition: transform 0.2s;" onmouseover="this.style.transform='translateY(-4px)'" onmouseout="this.style.transform='translateY(0)'">
            <div style="font-size: 40px; margin-bottom: 12px;">🧱</div>
            <div style="font-size: 18px; font-weight: 600; margin-bottom: 8px;">أسعار المواد</div>
            <div style="font-size: 14px; color: #6c757d;">عرض أسعار مواد البناء</div>
        </a>

        <a href="{{ route('prices.labor') }}" style="background: white; border-radius: 12px; padding: 24px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); text-decoration: none; color: inherit; transition: transform 0.2s;" onmouseover="this.style.transform='translateY(-4px)'" onmouseout="this.style.transform='translateY(0)'">
            <div style="font-size: 40px; margin-bottom: 12px;">👷</div>
            <div style="font-size: 18px; font-weight: 600; margin-bottom: 8px;">أسعار العمالة</div>
            <div style="font-size: 14px; color: #6c757d;">عرض أسعار العمالة والمهن</div>
        </a>
    </div>

    <div style="background: white; border-radius: 12px; padding: 24px; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
        <h2 style="font-size: 20px; font-weight: 600; margin-bottom: 20px;">آخر التحديثات</h2>
        <div style="color: #6c757d; text-align: center; padding: 40px;">
            لا توجد تحديثات حديثة
        </div>
    </div>
</div>
@endsection
