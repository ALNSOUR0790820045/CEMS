@extends('layouts.app')

@section('content')
<div style="padding: 80px 20px 40px; max-width: 1400px; margin: 0 auto;">
    <div style="margin-bottom: 30px;">
        <h1 style="font-size: 32px; font-weight: 700; margin-bottom: 10px;">متتبع السقف الأقصى (GMP)</h1>
        <p style="color: #666; font-size: 16px;">مراقبة الحد الأقصى المضمون للسعر</p>
    </div>

    <div style="display: grid; gap: 24px;">
        @forelse($gmpData as $data)
        <div style="background: white; border-radius: 12px; padding: 24px; box-shadow: 0 2px 8px rgba(0,0,0,0.06);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <div>
                    <h3 style="font-size: 20px; font-weight: 700; margin-bottom: 4px;">{{ $data['project_name'] }}</h3>
                    <p style="color: #666; font-size: 14px;">عقد #{{ $data['contract_id'] }}</p>
                </div>
                @if($data['exceeded'])
                    <span style="background: #f8d7da; color: #842029; padding: 8px 16px; border-radius: 12px; font-weight: 600;">
                        ⚠️ GMP متجاوز
                    </span>
                @else
                    <span style="background: #d1e7dd; color: #0f5132; padding: 8px 16px; border-radius: 12px; font-weight: 600;">
                        ✓ ضمن الحدود
                    </span>
                @endif
            </div>

            <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; margin-bottom: 20px;">
                <div>
                    <div style="color: #666; font-size: 13px; margin-bottom: 4px;">السقف الأقصى (GMP)</div>
                    <div style="font-size: 20px; font-weight: 700; color: var(--accent);">
                        {{ number_format($data['gmp'], 2) }}
                    </div>
                </div>
                <div>
                    <div style="color: #666; font-size: 13px; margin-bottom: 4px;">إجمالي التكاليف</div>
                    <div style="font-size: 20px; font-weight: 700;">
                        {{ number_format($data['total_costs'], 2) }}
                    </div>
                </div>
                <div>
                    <div style="color: #666; font-size: 13px; margin-bottom: 4px;">المتبقي</div>
                    <div style="font-size: 20px; font-weight: 700; color: {{ $data['remaining'] >= 0 ? '#28a745' : '#dc3545' }};">
                        {{ number_format($data['remaining'], 2) }}
                    </div>
                </div>
                <div>
                    <div style="color: #666; font-size: 13px; margin-bottom: 4px;">نسبة الاستهلاك</div>
                    <div style="font-size: 20px; font-weight: 700; color: {{ $data['percentage_used'] > 100 ? '#dc3545' : ($data['percentage_used'] > 80 ? '#ffc107' : '#28a745') }};">
                        {{ number_format($data['percentage_used'], 1) }}%
                    </div>
                </div>
            </div>

            <!-- Progress Bar -->
            <div style="background: #f0f0f0; height: 24px; border-radius: 12px; overflow: hidden; position: relative;">
                <div style="background: {{ $data['percentage_used'] > 100 ? '#dc3545' : ($data['percentage_used'] > 80 ? '#ffc107' : 'linear-gradient(90deg, var(--accent), #28a745)') }}; height: 100%; width: {{ min($data['percentage_used'], 100) }}%; transition: width 0.3s;">
                </div>
                <div style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; display: flex; align-items: center; justify-content: center; color: {{ $data['percentage_used'] > 50 ? 'white' : '#333' }}; font-weight: 600; font-size: 13px;">
                    {{ number_format($data['percentage_used'], 1) }}% مستخدم
                </div>
            </div>

            @if($data['exceeded'])
            <div style="margin-top: 16px; padding: 12px; background: #fff3cd; border-right: 4px solid #ffc107; border-radius: 4px;">
                <strong>تنبيه:</strong> تم تجاوز السقف الأقصى المضمون. يرجى مراجعة التكاليف والإجراءات اللازمة.
            </div>
            @elseif($data['percentage_used'] > 80)
            <div style="margin-top: 16px; padding: 12px; background: #fff3cd; border-right: 4px solid #ffc107; border-radius: 4px;">
                <strong>تحذير:</strong> اقتربت التكاليف من السقف الأقصى ({{ number_format($data['percentage_used'], 1) }}%). يرجى الانتباه.
            </div>
            @endif
        </div>
        @empty
        <div style="background: white; border-radius: 12px; padding: 60px; text-align: center; box-shadow: 0 2px 8px rgba(0,0,0,0.06);">
            <div style="font-size: 48px; margin-bottom: 16px;">📊</div>
            <h3 style="font-size: 20px; font-weight: 600; margin-bottom: 8px;">لا توجد عقود GMP</h3>
            <p style="color: #666;">لا توجد عقود محددة بسقف أقصى مضمون حالياً</p>
        </div>
        @endforelse
    </div>
</div>
@endsection
