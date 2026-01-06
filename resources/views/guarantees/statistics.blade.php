@extends('layouts.app')

@section('content')
<div style="padding: 40px; max-width: 1400px; margin: 0 auto;">
    <h1 style="margin-bottom: 30px;">إحصائيات خطابات الضمان</h1>

    <!-- Summary Cards -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 30px;">
        <div style="background: white; padding: 25px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
            <div style="color: #666; font-size: 14px; margin-bottom: 10px;">إجمالي الخطابات</div>
            <div style="font-size: 32px; font-weight: 700; color: #0071e3;">{{ $stats['total'] }}</div>
        </div>

        <div style="background: white; padding: 25px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
            <div style="color: #666; font-size: 14px; margin-bottom: 10px;">خطابات نشطة</div>
            <div style="font-size: 32px; font-weight: 700; color: #28a745;">{{ $stats['active'] }}</div>
        </div>

        <div style="background: white; padding: 25px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
            <div style="color: #666; font-size: 14px; margin-bottom: 10px;">قريبة من الانتهاء (30 يوم)</div>
            <div style="font-size: 32px; font-weight: 700; color: #ffc107;">{{ $stats['expiring_30'] }}</div>
        </div>

        <div style="background: white; padding: 25px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
            <div style="color: #666; font-size: 14px; margin-bottom: 10px;">إجمالي المبالغ (ر.س)</div>
            <div style="font-size: 32px; font-weight: 700; color: #0071e3;">{{ number_format($stats['total_amount'], 0) }}</div>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
        <!-- By Type -->
        <div style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
            <h2 style="margin: 0 0 20px 0; font-size: 18px;">حسب النوع</h2>
            @foreach($stats['by_type'] as $item)
                <div style="padding: 15px 0; border-bottom: 1px solid #f0f0f0;">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 5px;">
                        <span style="font-weight: 600;">
                            @if($item->type == 'bid') ضمان ابتدائي
                            @elseif($item->type == 'performance') ضمان حسن التنفيذ
                            @elseif($item->type == 'advance_payment') ضمان الدفعة المقدمة
                            @elseif($item->type == 'maintenance') ضمان الصيانة
                            @else ضمان الاحتجاز
                            @endif
                        </span>
                        <span style="color: #0071e3; font-weight: 700;">{{ $item->count }}</span>
                    </div>
                    <div style="color: #666; font-size: 14px;">المبلغ: {{ number_format($item->total_amount, 2) }} ر.س</div>
                </div>
            @endforeach
        </div>

        <!-- By Bank -->
        <div style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
            <h2 style="margin: 0 0 20px 0; font-size: 18px;">حسب البنك</h2>
            @foreach($stats['by_bank'] as $item)
                <div style="padding: 15px 0; border-bottom: 1px solid #f0f0f0;">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 5px;">
                        <span style="font-weight: 600;">{{ $item->bank->name }}</span>
                        <span style="color: #0071e3; font-weight: 700;">{{ $item->count }}</span>
                    </div>
                    <div style="color: #666; font-size: 14px;">المبلغ: {{ number_format($item->total_amount, 2) }} ر.س</div>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
