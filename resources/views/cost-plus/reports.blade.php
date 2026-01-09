@extends('layouts.app')

@section('content')
<div style="padding: 80px 20px 40px; max-width: 1400px; margin: 0 auto;">
    <div style="margin-bottom: 30px;">
        <h1 style="font-size: 32px; font-weight: 700; margin-bottom: 10px;">التقارير</h1>
        <p style="color: #666; font-size: 16px;">تقارير شاملة لعقود Cost Plus</p>
    </div>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(350px, 1fr)); gap: 24px;">
        <!-- GMP Status Report -->
        <a href="{{ route('cost-plus.gmp-status') }}" style="background: white; border-radius: 12px; padding: 32px; text-decoration: none; color: inherit; box-shadow: 0 2px 8px rgba(0,0,0,0.06); transition: transform 0.2s;" onmouseover="this.style.transform='translateY(-4px)'" onmouseout="this.style.transform='translateY(0)'">
            <div style="font-size: 48px; margin-bottom: 16px;">📊</div>
            <h3 style="font-size: 20px; font-weight: 700; margin-bottom: 8px;">تقرير حالة GMP</h3>
            <p style="color: #666; font-size: 14px; line-height: 1.6;">
                مراقبة السقف الأقصى المضمون للسعر لجميع العقود مع نسب الاستهلاك والمتبقي
            </p>
        </a>

        <!-- Open Book Report -->
        <a href="{{ route('cost-plus.open-book-report') }}" style="background: white; border-radius: 12px; padding: 32px; text-decoration: none; color: inherit; box-shadow: 0 2px 8px rgba(0,0,0,0.06); transition: transform 0.2s;" onmouseover="this.style.transform='translateY(-4px)'" onmouseout="this.style.transform='translateY(0)'">
            <div style="font-size: 48px; margin-bottom: 16px;">📖</div>
            <h3 style="font-size: 20px; font-weight: 700; margin-bottom: 8px;">تقرير الكتاب المفتوح</h3>
            <p style="color: #666; font-size: 14px; line-height: 1.6;">
                محاسبة شفافة 100% مع تفصيل كامل للتكاليف حسب النوع وحالة التوثيق
            </p>
        </a>

        <!-- Contracts List -->
        <a href="{{ route('cost-plus.contracts.index') }}" style="background: white; border-radius: 12px; padding: 32px; text-decoration: none; color: inherit; box-shadow: 0 2px 8px rgba(0,0,0,0.06); transition: transform 0.2s;" onmouseover="this.style.transform='translateY(-4px)'" onmouseout="this.style.transform='translateY(0)'">
            <div style="font-size: 48px; margin-bottom: 16px;">📋</div>
            <h3 style="font-size: 20px; font-weight: 700; margin-bottom: 8px;">جميع العقود</h3>
            <p style="color: #666; font-size: 14px; line-height: 1.6;">
                عرض قائمة بجميع عقود Cost Plus مع التفاصيل والحالات
            </p>
        </a>

        <!-- Transactions Report -->
        <a href="{{ route('cost-plus.transactions.index') }}" style="background: white; border-radius: 12px; padding: 32px; text-decoration: none; color: inherit; box-shadow: 0 2px 8px rgba(0,0,0,0.06); transition: transform 0.2s;" onmouseover="this.style.transform='translateY(-4px)'" onmouseout="this.style.transform='translateY(0)'">
            <div style="font-size: 48px; margin-bottom: 16px;">💰</div>
            <h3 style="font-size: 20px; font-weight: 700; margin-bottom: 8px;">المعاملات</h3>
            <p style="color: #666; font-size: 14px; line-height: 1.6;">
                جميع معاملات التكاليف مع حالة التوثيق والموافقة
            </p>
        </a>

        <!-- Invoices Report -->
        <a href="{{ route('cost-plus.invoices.index') }}" style="background: white; border-radius: 12px; padding: 32px; text-decoration: none; color: inherit; box-shadow: 0 2px 8px rgba(0,0,0,0.06); transition: transform 0.2s;" onmouseover="this.style.transform='translateY(-4px)'" onmouseout="this.style.transform='translateY(0)'">
            <div style="font-size: 48px; margin-bottom: 16px;">🧾</div>
            <h3 style="font-size: 20px; font-weight: 700; margin-bottom: 8px;">الفواتير</h3>
            <p style="color: #666; font-size: 14px; line-height: 1.6;">
                جميع فواتير Cost Plus مع التكاليف والربح والحالة
            </p>
        </a>

        <!-- Overhead Report -->
        <a href="{{ route('cost-plus.overhead.index') }}" style="background: white; border-radius: 12px; padding: 32px; text-decoration: none; color: inherit; box-shadow: 0 2px 8px rgba(0,0,0,0.06); transition: transform 0.2s;" onmouseover="this.style.transform='translateY(-4px)'" onmouseout="this.style.transform='translateY(0)'">
            <div style="font-size: 48px; margin-bottom: 16px;">🏢</div>
            <h3 style="font-size: 20px; font-weight: 700; margin-bottom: 8px;">المصاريف غير المباشرة</h3>
            <p style="color: #666; font-size: 14px; line-height: 1.6;">
                توزيع وتتبع المصاريف الإدارية والتشغيلية على المشاريع
            </p>
        </a>
    </div>

    <!-- Summary Section -->
    <div style="background: white; border-radius: 12px; padding: 32px; margin-top: 30px; box-shadow: 0 2px 8px rgba(0,0,0,0.06);">
        <h2 style="font-size: 22px; font-weight: 700; margin-bottom: 20px;">ملخص العقود</h2>
        
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="background: #f8f9fa; border-bottom: 2px solid #dee2e6;">
                        <th style="padding: 12px; text-align: right; font-weight: 600;">المشروع</th>
                        <th style="padding: 12px; text-align: right; font-weight: 600;">نوع الربح</th>
                        <th style="padding: 12px; text-align: right; font-weight: 600;">GMP</th>
                        <th style="padding: 12px; text-align: right; font-weight: 600;">العملة</th>
                        <th style="padding: 12px; text-align: center; font-weight: 600;">الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($contracts as $contract)
                    <tr style="border-bottom: 1px solid #dee2e6;">
                        <td style="padding: 12px;">{{ $contract->project->name }}</td>
                        <td style="padding: 12px;">
                            @switch($contract->fee_type)
                                @case('percentage') نسبة مئوية @break
                                @case('fixed_fee') مبلغ مقطوع @break
                                @case('incentive') حوافز أداء @break
                                @case('hybrid') هجين @break
                            @endswitch
                        </td>
                        <td style="padding: 12px;">
                            @if($contract->has_gmp)
                                {{ number_format($contract->guaranteed_maximum_price, 2) }}
                            @else
                                -
                            @endif
                        </td>
                        <td style="padding: 12px;">{{ $contract->currency }}</td>
                        <td style="padding: 12px; text-align: center;">
                            <a href="{{ route('cost-plus.contracts.show', $contract->id) }}" style="color: var(--accent); text-decoration: none;">عرض</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" style="padding: 20px; text-align: center; color: #666;">
                            لا توجد عقود
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
