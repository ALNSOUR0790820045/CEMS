@extends('layouts.app')

@section('content')
<div style="padding: 80px 20px 40px; max-width: 1200px; margin: 0 auto;">
    <div style="margin-bottom: 30px;">
        <h1 style="font-size: 32px; font-weight: 700; margin-bottom: 10px;">لوحة التحكم - Cost Plus</h1>
        <p style="color: #666; font-size: 16px;">محاسبة الكتاب المفتوح - إدارة عقود التكلفة + الربح</p>
    </div>

    <!-- Statistics Cards -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 30px;">
        <div style="background: white; border-radius: 12px; padding: 24px; box-shadow: 0 2px 8px rgba(0,0,0,0.06);">
            <div style="color: #666; font-size: 14px; margin-bottom: 8px;">إجمالي العقود</div>
            <div style="font-size: 32px; font-weight: 700; color: var(--accent);">{{ $stats['total_contracts'] }}</div>
        </div>

        <div style="background: white; border-radius: 12px; padding: 24px; box-shadow: 0 2px 8px rgba(0,0,0,0.06);">
            <div style="color: #666; font-size: 14px; margin-bottom: 8px;">العقود النشطة</div>
            <div style="font-size: 32px; font-weight: 700; color: #28a745;">{{ $stats['active_contracts'] }}</div>
        </div>

        <div style="background: white; border-radius: 12px; padding: 24px; box-shadow: 0 2px 8px rgba(0,0,0,0.06);">
            <div style="color: #666; font-size: 14px; margin-bottom: 8px;">المعاملات المعلقة</div>
            <div style="font-size: 32px; font-weight: 700; color: #ffc107;">{{ $stats['pending_transactions'] }}</div>
        </div>

        <div style="background: white; border-radius: 12px; padding: 24px; box-shadow: 0 2px 8px rgba(0,0,0,0.06);">
            <div style="color: #666; font-size: 14px; margin-bottom: 8px;">إجمالي الفواتير</div>
            <div style="font-size: 32px; font-weight: 700; color: var(--accent);">{{ $stats['total_invoices'] }}</div>
        </div>

        <div style="background: white; border-radius: 12px; padding: 24px; box-shadow: 0 2px 8px rgba(0,0,0,0.06);">
            <div style="color: #666; font-size: 14px; margin-bottom: 8px;">بانتظار الموافقة</div>
            <div style="font-size: 32px; font-weight: 700; color: #dc3545;">{{ $stats['pending_approvals'] }}</div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px;">
        <a href="{{ route('cost-plus.contracts.index') }}" style="background: white; border-radius: 12px; padding: 24px; text-decoration: none; color: inherit; box-shadow: 0 2px 8px rgba(0,0,0,0.06); transition: transform 0.2s;">
            <div style="font-size: 18px; font-weight: 600; margin-bottom: 8px;">📋 إدارة العقود</div>
            <div style="color: #666; font-size: 14px;">عرض وإدارة عقود Cost Plus</div>
        </a>

        <a href="{{ route('cost-plus.transactions.index') }}" style="background: white; border-radius: 12px; padding: 24px; text-decoration: none; color: inherit; box-shadow: 0 2px 8px rgba(0,0,0,0.06); transition: transform 0.2s;">
            <div style="font-size: 18px; font-weight: 600; margin-bottom: 8px;">💰 المعاملات</div>
            <div style="color: #666; font-size: 14px;">تسجيل ومتابعة التكاليف</div>
        </a>

        <a href="{{ route('cost-plus.invoices.index') }}" style="background: white; border-radius: 12px; padding: 24px; text-decoration: none; color: inherit; box-shadow: 0 2px 8px rgba(0,0,0,0.06); transition: transform 0.2s;">
            <div style="font-size: 18px; font-weight: 600; margin-bottom: 8px;">🧾 الفواتير</div>
            <div style="color: #666; font-size: 14px;">إنشاء وإدارة الفواتير</div>
        </a>

        <a href="{{ route('cost-plus.gmp-status') }}" style="background: white; border-radius: 12px; padding: 24px; text-decoration: none; color: inherit; box-shadow: 0 2px 8px rgba(0,0,0,0.06); transition: transform 0.2s;">
            <div style="font-size: 18px; font-weight: 600; margin-bottom: 8px;">📊 تتبع GMP</div>
            <div style="color: #666; font-size: 14px;">مراقبة السقف الأقصى</div>
        </a>

        <a href="{{ route('cost-plus.open-book-report') }}" style="background: white; border-radius: 12px; padding: 24px; text-decoration: none; color: inherit; box-shadow: 0 2px 8px rgba(0,0,0,0.06); transition: transform 0.2s;">
            <div style="font-size: 18px; font-weight: 600; margin-bottom: 8px;">📖 الكتاب المفتوح</div>
            <div style="color: #666; font-size: 14px;">تقرير المحاسبة الشفافة</div>
        </a>

        <a href="{{ route('cost-plus.overhead.index') }}" style="background: white; border-radius: 12px; padding: 24px; text-decoration: none; color: inherit; box-shadow: 0 2px 8px rgba(0,0,0,0.06); transition: transform 0.2s;">
            <div style="font-size: 18px; font-weight: 600; margin-bottom: 8px;">🏢 المصاريف غير المباشرة</div>
            <div style="color: #666; font-size: 14px;">توزيع التكاليف الإدارية</div>
        </a>
    </div>
</div>
@endsection
