@extends('layouts.app')

@section('content')
<div style="padding: 80px 20px 40px; max-width: 1400px; margin: 0 auto;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
        <div>
            <h1 style="font-size: 32px; font-weight: 700; margin-bottom: 10px;">عقود Cost Plus</h1>
            <p style="color: #666; font-size: 16px;">إدارة عقود التكلفة + الربح</p>
        </div>
        <a href="{{ route('cost-plus.contracts.create') }}" style="background: var(--accent); color: white; padding: 12px 24px; border-radius: 8px; text-decoration: none; font-weight: 600;">
            + إضافة عقد جديد
        </a>
    </div>

    <div style="background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.06);">
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="background: #f8f9fa; border-bottom: 2px solid #dee2e6;">
                    <th style="padding: 16px; text-align: right; font-weight: 600;">#</th>
                    <th style="padding: 16px; text-align: right; font-weight: 600;">رقم العقد</th>
                    <th style="padding: 16px; text-align: right; font-weight: 600;">المشروع</th>
                    <th style="padding: 16px; text-align: right; font-weight: 600;">نوع الربح</th>
                    <th style="padding: 16px; text-align: right; font-weight: 600;">نسبة/مبلغ الربح</th>
                    <th style="padding: 16px; text-align: right; font-weight: 600;">GMP</th>
                    <th style="padding: 16px; text-align: right; font-weight: 600;">العملة</th>
                    <th style="padding: 16px; text-align: center; font-weight: 600;">الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                @forelse($contracts as $contract)
                <tr style="border-bottom: 1px solid #dee2e6;">
                    <td style="padding: 16px;">{{ $contract->id }}</td>
                    <td style="padding: 16px;">{{ $contract->contract->contract_number }}</td>
                    <td style="padding: 16px;">{{ $contract->project->name }}</td>
                    <td style="padding: 16px;">
                        @switch($contract->fee_type)
                            @case('percentage')
                                <span style="background: #e3f2fd; color: #1976d2; padding: 4px 12px; border-radius: 12px; font-size: 13px;">نسبة مئوية</span>
                                @break
                            @case('fixed_fee')
                                <span style="background: #e8f5e9; color: #388e3c; padding: 4px 12px; border-radius: 12px; font-size: 13px;">مبلغ مقطوع</span>
                                @break
                            @case('incentive')
                                <span style="background: #fff3e0; color: #f57c00; padding: 4px 12px; border-radius: 12px; font-size: 13px;">حوافز أداء</span>
                                @break
                            @case('hybrid')
                                <span style="background: #f3e5f5; color: #7b1fa2; padding: 4px 12px; border-radius: 12px; font-size: 13px;">هجين</span>
                                @break
                        @endswitch
                    </td>
                    <td style="padding: 16px;">
                        @if($contract->fee_type === 'percentage' || $contract->fee_type === 'hybrid')
                            {{ number_format($contract->fee_percentage, 2) }}%
                        @endif
                        @if($contract->fee_type === 'fixed_fee' || $contract->fee_type === 'hybrid')
                            {{ number_format($contract->fixed_fee_amount, 2) }}
                        @endif
                    </td>
                    <td style="padding: 16px;">
                        @if($contract->has_gmp)
                            <span style="color: #28a745; font-weight: 600;">✓ {{ number_format($contract->guaranteed_maximum_price, 2) }}</span>
                        @else
                            <span style="color: #666;">-</span>
                        @endif
                    </td>
                    <td style="padding: 16px;">{{ $contract->currency }}</td>
                    <td style="padding: 16px; text-align: center;">
                        <a href="{{ route('cost-plus.contracts.show', $contract->id) }}" style="color: var(--accent); text-decoration: none; margin: 0 8px;">عرض</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" style="padding: 40px; text-align: center; color: #666;">
                        لا توجد عقود Cost Plus مسجلة
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
