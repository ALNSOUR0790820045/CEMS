@extends('layouts.app')

@section('content')
<div style="padding: 40px; max-width: 1400px; margin: 0 auto;">
    <h1 style="margin-bottom: 30px;">خطابات الضمان القريبة من الانتهاء</h1>

    <div style="background: white; padding: 20px; border-radius: 10px; margin-bottom: 20px;">
        <form method="GET" style="display: flex; gap: 15px; align-items: end;">
            <div>
                <label style="display: block; margin-bottom: 5px; font-weight: 600;">عرض الخطابات المنتهية خلال</label>
                <select name="days" style="padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
                    <option value="30" {{ $days == 30 ? 'selected' : '' }}>30 يوم</option>
                    <option value="60" {{ $days == 60 ? 'selected' : '' }}>60 يوم</option>
                    <option value="90" {{ $days == 90 ? 'selected' : '' }}>90 يوم</option>
                </select>
            </div>
            <button type="submit" style="background: #0071e3; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; font-family: 'Cairo', sans-serif; font-weight: 600;">تطبيق</button>
        </form>
    </div>

    <div style="background: white; border-radius: 10px; overflow: hidden;">
        <table style="width: 100%; border-collapse: collapse;">
            <thead style="background: #f5f5f7;">
                <tr>
                    <th style="padding: 15px; text-align: right; font-weight: 600;">رقم الخطاب</th>
                    <th style="padding: 15px; text-align: right; font-weight: 600;">النوع</th>
                    <th style="padding: 15px; text-align: right; font-weight: 600;">البنك</th>
                    <th style="padding: 15px; text-align: right; font-weight: 600;">المستفيد</th>
                    <th style="padding: 15px; text-align: right; font-weight: 600;">المبلغ</th>
                    <th style="padding: 15px; text-align: right; font-weight: 600;">تاريخ الانتهاء</th>
                    <th style="padding: 15px; text-align: right; font-weight: 600;">الأيام المتبقية</th>
                    <th style="padding: 15px; text-align: right; font-weight: 600;">الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                @forelse($guarantees as $guarantee)
                    <tr style="border-bottom: 1px solid #f0f0f0; background: 
                        @if($guarantee->days_until_expiry <= 7) #fff3cd
                        @elseif($guarantee->days_until_expiry <= 15) #d1ecf1
                        @else white
                        @endif;">
                        <td style="padding: 15px;">
                            <a href="{{ route('guarantees.show', $guarantee) }}" style="color: #0071e3; text-decoration: none; font-weight: 600;">{{ $guarantee->guarantee_number }}</a>
                        </td>
                        <td style="padding: 15px;">{{ $guarantee->type_name }}</td>
                        <td style="padding: 15px;">{{ $guarantee->bank->name }}</td>
                        <td style="padding: 15px;">{{ $guarantee->beneficiary }}</td>
                        <td style="padding: 15px;">{{ number_format($guarantee->amount, 2) }} {{ $guarantee->currency }}</td>
                        <td style="padding: 15px;">{{ $guarantee->expiry_date->format('Y-m-d') }}</td>
                        <td style="padding: 15px;">
                            <span style="font-weight: 700; color: 
                                @if($guarantee->days_until_expiry <= 7) #856404
                                @elseif($guarantee->days_until_expiry <= 15) #0c5460
                                @else #155724
                                @endif;">
                                {{ $guarantee->days_until_expiry }} يوم
                            </span>
                        </td>
                        <td style="padding: 15px;">
                            <a href="{{ route('guarantees.renew', $guarantee) }}" style="background: #ffc107; color: #000; padding: 6px 12px; border-radius: 5px; text-decoration: none; font-size: 14px; font-weight: 600;">تجديد</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" style="padding: 40px; text-align: center; color: #666;">لا توجد خطابات قريبة من الانتهاء</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
