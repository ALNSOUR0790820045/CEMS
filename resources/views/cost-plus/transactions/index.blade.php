@extends('layouts.app')

@section('content')
<div style="padding: 80px 20px 40px; max-width: 1400px; margin: 0 auto;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
        <div>
            <h1 style="font-size: 32px; font-weight: 700; margin-bottom: 10px;">معاملات التكاليف</h1>
            <p style="color: #666; font-size: 16px;">تسجيل ومتابعة تكاليف المشاريع</p>
        </div>
        <a href="{{ route('cost-plus.transactions.create') }}" style="background: var(--accent); color: white; padding: 12px 24px; border-radius: 8px; text-decoration: none; font-weight: 600;">
            + إضافة معاملة جديدة
        </a>
    </div>

    <div style="background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.06);">
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="background: #f8f9fa; border-bottom: 2px solid #dee2e6;">
                    <th style="padding: 16px; text-align: right; font-weight: 600;">رقم المعاملة</th>
                    <th style="padding: 16px; text-align: right; font-weight: 600;">المشروع</th>
                    <th style="padding: 16px; text-align: right; font-weight: 600;">التاريخ</th>
                    <th style="padding: 16px; text-align: right; font-weight: 600;">نوع التكلفة</th>
                    <th style="padding: 16px; text-align: right; font-weight: 600;">المبلغ الصافي</th>
                    <th style="padding: 16px; text-align: center; font-weight: 600;">التوثيق</th>
                    <th style="padding: 16px; text-align: center; font-weight: 600;">الحالة</th>
                    <th style="padding: 16px; text-align: center; font-weight: 600;">الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                @forelse($transactions as $transaction)
                <tr style="border-bottom: 1px solid #dee2e6;">
                    <td style="padding: 16px; font-weight: 600;">{{ $transaction->transaction_number }}</td>
                    <td style="padding: 16px;">{{ $transaction->project->name }}</td>
                    <td style="padding: 16px;">{{ $transaction->transaction_date->format('Y-m-d') }}</td>
                    <td style="padding: 16px;">
                        @switch($transaction->cost_type)
                            @case('material')
                                <span>مواد</span>
                                @break
                            @case('labor')
                                <span>عمالة</span>
                                @break
                            @case('equipment')
                                <span>معدات</span>
                                @break
                            @case('subcontract')
                                <span>مقاولي باطن</span>
                                @break
                            @case('overhead')
                                <span>مصاريف غير مباشرة</span>
                                @break
                            @case('other')
                                <span>أخرى</span>
                                @break
                        @endswitch
                    </td>
                    <td style="padding: 16px; font-weight: 600;">{{ number_format($transaction->net_amount, 2) }} {{ $transaction->currency }}</td>
                    <td style="padding: 16px; text-align: center;">
                        @if($transaction->documentation_complete)
                            <span style="color: #28a745; font-weight: 600;">✓ مكتمل</span>
                        @else
                            <span style="color: #dc3545; font-weight: 600;">✗ ناقص</span>
                        @endif
                        <div style="font-size: 12px; color: #666; margin-top: 4px;">
                            {{ $transaction->has_original_invoice ? '✓' : '✗' }} فاتورة
                            {{ $transaction->has_payment_receipt ? '✓' : '✗' }} إيصال
                            {{ $transaction->has_grn ? '✓' : '✗' }} GRN
                            {{ $transaction->has_photo_evidence ? '✓' : '✗' }} صورة
                        </div>
                    </td>
                    <td style="padding: 16px; text-align: center;">
                        @switch($transaction->status)
                            @case('pending')
                                <span style="background: #fff3cd; color: #856404; padding: 4px 12px; border-radius: 12px; font-size: 13px;">بانتظار التوثيق</span>
                                @break
                            @case('documented')
                                <span style="background: #cfe2ff; color: #084298; padding: 4px 12px; border-radius: 12px; font-size: 13px;">موثق</span>
                                @break
                            @case('approved')
                                <span style="background: #d1e7dd; color: #0f5132; padding: 4px 12px; border-radius: 12px; font-size: 13px;">معتمد</span>
                                @break
                            @case('rejected')
                                <span style="background: #f8d7da; color: #842029; padding: 4px 12px; border-radius: 12px; font-size: 13px;">مرفوض</span>
                                @break
                            @case('invoiced')
                                <span style="background: #e2e3e5; color: #41464b; padding: 4px 12px; border-radius: 12px; font-size: 13px;">مفوتر</span>
                                @break
                            @case('paid')
                                <span style="background: #d1e7dd; color: #0f5132; padding: 4px 12px; border-radius: 12px; font-size: 13px;">مدفوع</span>
                                @break
                        @endswitch
                    </td>
                    <td style="padding: 16px; text-align: center;">
                        <a href="{{ route('cost-plus.transactions.show', $transaction->id) }}" style="color: var(--accent); text-decoration: none; margin: 0 8px;">عرض</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" style="padding: 40px; text-align: center; color: #666;">
                        لا توجد معاملات مسجلة
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
