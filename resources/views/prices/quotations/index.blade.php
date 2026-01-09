@extends('layouts.app')

@section('content')
<div style="padding: 20px; max-width: 1400px; margin: 0 auto;">
    <div style="margin-bottom: 20px;">
        <a href="{{ route('price-requests.show', $priceRequest) }}" style="color: var(--accent); text-decoration: none; font-size: 14px;">← العودة للطلب</a>
        <h1 style="font-size: 28px; font-weight: 600; margin: 10px 0;">عروض الأسعار</h1>
        <p style="color: #6c757d; margin: 5px 0;">طلب رقم: {{ $priceRequest->request_number }}</p>
    </div>

    @if(session('success'))
    <div style="background: #d4edda; color: #155724; padding: 12px 20px; border-radius: 8px; margin-bottom: 20px;">
        {{ session('success') }}
    </div>
    @endif

    <div style="background: white; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); overflow: hidden;">
        <div style="padding: 20px; border-bottom: 1px solid #dee2e6; display: flex; justify-content: space-between; align-items: center;">
            <h2 style="font-size: 20px; font-weight: 600; margin: 0;">العروض المستلمة ({{ $quotations->count() }})</h2>
            <a href="#" onclick="alert('يرجى إضافة نموذج إضافة عرض أسعار')"
               style="background: var(--accent); color: white; padding: 10px 20px; border-radius: 8px; text-decoration: none; font-weight: 500;">
                إضافة عرض أسعار
            </a>
        </div>

        @forelse($quotations as $quotation)
        <div style="border-bottom: 1px solid #dee2e6; padding: 20px;">
            <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 16px;">
                <div>
                    <div style="font-size: 18px; font-weight: 600; margin-bottom: 8px;">{{ $quotation->vendor->name }}</div>
                    <div style="color: #6c757d; font-size: 14px;">
                        رقم العرض: {{ $quotation->quotation_number ?? '-' }}
                    </div>
                </div>
                <div style="text-align: left;">
                    <div style="font-size: 24px; font-weight: 700; color: var(--accent); margin-bottom: 4px;">
                        {{ number_format($quotation->total_amount, 2) }} {{ $quotation->currency }}
                    </div>
                    @if($quotation->is_selected)
                    <span style="background: #28a745; color: white; padding: 4px 12px; border-radius: 12px; font-size: 13px;">
                        محدد
                    </span>
                    @endif
                </div>
            </div>

            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; margin-bottom: 16px;">
                <div>
                    <div style="font-size: 13px; color: #6c757d; margin-bottom: 4px;">تاريخ العرض</div>
                    <div style="font-weight: 600;">{{ $quotation->quotation_date->format('Y-m-d') }}</div>
                </div>
                <div>
                    <div style="font-size: 13px; color: #6c757d; margin-bottom: 4px;">صلاحية العرض</div>
                    <div style="font-weight: 600;">{{ $quotation->validity_date->format('Y-m-d') }}</div>
                </div>
                <div>
                    <div style="font-size: 13px; color: #6c757d; margin-bottom: 4px;">العملة</div>
                    <div style="font-weight: 600;">{{ $quotation->currency }}</div>
                </div>
            </div>

            @if($quotation->payment_terms || $quotation->delivery_terms)
            <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 16px; margin-bottom: 16px;">
                @if($quotation->payment_terms)
                <div>
                    <div style="font-size: 13px; color: #6c757d; margin-bottom: 4px;">شروط الدفع</div>
                    <div>{{ $quotation->payment_terms }}</div>
                </div>
                @endif
                @if($quotation->delivery_terms)
                <div>
                    <div style="font-size: 13px; color: #6c757d; margin-bottom: 4px;">شروط التسليم</div>
                    <div>{{ $quotation->delivery_terms }}</div>
                </div>
                @endif
            </div>
            @endif

            <div style="margin-top: 16px;">
                <table style="width: 100%; border-collapse: collapse; font-size: 14px;">
                    <thead style="background: #f8f9fa;">
                        <tr>
                            <th style="padding: 8px; text-align: right; border-bottom: 1px solid #dee2e6;">البند</th>
                            <th style="padding: 8px; text-align: right; border-bottom: 1px solid #dee2e6;">الوحدة</th>
                            <th style="padding: 8px; text-align: right; border-bottom: 1px solid #dee2e6;">الكمية</th>
                            <th style="padding: 8px; text-align: right; border-bottom: 1px solid #dee2e6;">سعر الوحدة</th>
                            <th style="padding: 8px; text-align: right; border-bottom: 1px solid #dee2e6;">المجموع</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($quotation->items as $item)
                        <tr>
                            <td style="padding: 8px; border-bottom: 1px solid #f0f0f0;">{{ $item->requestItem->item_description }}</td>
                            <td style="padding: 8px; border-bottom: 1px solid #f0f0f0;">{{ $item->requestItem->unit }}</td>
                            <td style="padding: 8px; border-bottom: 1px solid #f0f0f0;">{{ number_format($item->requestItem->quantity, 2) }}</td>
                            <td style="padding: 8px; border-bottom: 1px solid #f0f0f0;">{{ number_format($item->unit_price, 2) }}</td>
                            <td style="padding: 8px; border-bottom: 1px solid #f0f0f0; font-weight: 600;">{{ number_format($item->total_price, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div style="margin-top: 16px; display: flex; gap: 12px;">
                <a href="{{ route('price-quotations.show', $quotation) }}"
                   style="color: var(--accent); text-decoration: none; font-size: 14px;">عرض التفاصيل</a>
            </div>
        </div>
        @empty
        <div style="padding: 60px; text-align: center; color: #6c757d;">
            <div style="font-size: 48px; margin-bottom: 16px;">📭</div>
            <div style="font-size: 18px; font-weight: 600; margin-bottom: 8px;">لا توجد عروض أسعار</div>
            <div>لم يتم استلام أي عروض أسعار لهذا الطلب بعد</div>
        </div>
        @endforelse
    </div>

    @if($quotations->count() > 1)
    <div style="margin-top: 20px; text-align: center;">
        <a href="{{ route('price-comparisons.create', $priceRequest) }}"
           style="background: #ffc107; color: #000; padding: 12px 32px; border-radius: 8px; text-decoration: none; font-weight: 600; display: inline-block;">
            إجراء مقارنة الأسعار
        </a>
    </div>
    @endif
</div>
@endsection
