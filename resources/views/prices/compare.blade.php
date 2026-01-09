@extends('layouts.app')

@section('content')
<div style="padding: 20px; max-width: 1400px; margin: 0 auto;">
    <h1 style="font-size: 28px; font-weight: 600; margin-bottom: 20px;">مقارنة الأسعار</h1>

    <div style="background: white; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); padding: 24px; margin-bottom: 20px;">
        <form method="GET" action="{{ route('prices.compare') }}">
            <div style="display: flex; gap: 12px;">
                <input type="text" name="search" placeholder="ابحث عن بند أو مادة..." value="{{ request('search') }}" required
                       style="flex: 1; padding: 12px; border: 1px solid #dee2e6; border-radius: 8px; font-size: 14px;">
                <button type="submit"
                        style="background: var(--accent); color: white; padding: 12px 32px; border: none; border-radius: 8px; font-weight: 500; cursor: pointer;">
                    بحث ومقارنة
                </button>
            </div>
        </form>
    </div>

    @if(isset($items) && $items->count() > 0)
    <div style="background: white; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); overflow-x: auto;">
        @foreach($items as $itemCode => $itemGroup)
        <div style="padding: 24px; border-bottom: 2px solid #dee2e6;">
            <h3 style="font-size: 18px; font-weight: 600; margin-bottom: 16px;">
                {{ $itemGroup->first()->item_name }}
                <span style="color: #6c757d; font-size: 14px; font-weight: 400;">({{ $itemCode }})</span>
            </h3>

            <table style="width: 100%; border-collapse: collapse;">
                <thead style="background: #f8f9fa;">
                    <tr>
                        <th style="padding: 12px; text-align: right; font-weight: 600; border-bottom: 2px solid #dee2e6;">المصدر</th>
                        <th style="padding: 12px; text-align: right; font-weight: 600; border-bottom: 2px solid #dee2e6;">قائمة الأسعار</th>
                        <th style="padding: 12px; text-align: right; font-weight: 600; border-bottom: 2px solid #dee2e6;">الوحدة</th>
                        <th style="padding: 12px; text-align: right; font-weight: 600; border-bottom: 2px solid #dee2e6;">السعر</th>
                        <th style="padding: 12px; text-align: right; font-weight: 600; border-bottom: 2px solid #dee2e6;">تاريخ السريان</th>
                        <th style="padding: 12px; text-align: right; font-weight: 600; border-bottom: 2px solid #dee2e6;">الحالة</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $minPrice = $itemGroup->min('unit_price');
                        $maxPrice = $itemGroup->max('unit_price');
                    @endphp
                    @foreach($itemGroup as $item)
                    <tr style="border-bottom: 1px solid #dee2e6; {{ $item->unit_price == $minPrice ? 'background: #d4edda;' : '' }}">
                        <td style="padding: 12px;">
                            @switch($item->priceList->source)
                                @case('internal') داخلي @break
                                @case('ministry') وزارة @break
                                @case('syndicate') نقابة @break
                                @case('market') سوق @break
                                @case('vendor') مورد @break
                            @endswitch
                        </td>
                        <td style="padding: 12px;">{{ $item->priceList->name }}</td>
                        <td style="padding: 12px;">{{ $item->unit }}</td>
                        <td style="padding: 12px; font-weight: 600; font-size: 16px;">
                            {{ number_format($item->unit_price, 2) }} {{ $item->priceList->currency }}
                            @if($item->unit_price == $minPrice)
                                <span style="background: #28a745; color: white; padding: 2px 8px; border-radius: 8px; font-size: 11px; margin-right: 8px;">الأقل</span>
                            @endif
                            @if($item->unit_price == $maxPrice && $minPrice != $maxPrice)
                                <span style="background: #dc3545; color: white; padding: 2px 8px; border-radius: 8px; font-size: 11px; margin-right: 8px;">الأعلى</span>
                            @endif
                        </td>
                        <td style="padding: 12px;">{{ $item->priceList->effective_date->format('Y-m-d') }}</td>
                        <td style="padding: 12px;">
                            <span style="padding: 4px 12px; border-radius: 12px; font-size: 13px; {{ $item->is_active ? 'background: #d4edda; color: #155724;' : 'background: #f8d7da; color: #721c24;' }}">
                                {{ $item->is_active ? 'نشط' : 'غير نشط' }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            @if($itemGroup->count() > 1)
            <div style="margin-top: 16px; padding: 12px; background: #f8f9fa; border-radius: 8px;">
                <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; font-size: 14px;">
                    <div>
                        <span style="color: #6c757d;">أقل سعر:</span>
                        <strong style="margin-right: 8px; color: #28a745;">{{ number_format($minPrice, 2) }}</strong>
                    </div>
                    <div>
                        <span style="color: #6c757d;">أعلى سعر:</span>
                        <strong style="margin-right: 8px; color: #dc3545;">{{ number_format($maxPrice, 2) }}</strong>
                    </div>
                    <div>
                        <span style="color: #6c757d;">الفرق:</span>
                        <strong style="margin-right: 8px; color: #ffc107;">
                            {{ number_format($maxPrice - $minPrice, 2) }}
                            ({{ $minPrice > 0 ? number_format((($maxPrice - $minPrice) / $minPrice) * 100, 1) : 0 }}%)
                        </strong>
                    </div>
                </div>
            </div>
            @endif
        </div>
        @endforeach
    </div>
    @elseif(request('search'))
    <div style="background: white; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); padding: 60px; text-align: center;">
        <div style="font-size: 48px; margin-bottom: 16px;">🔍</div>
        <div style="font-size: 18px; font-weight: 600; margin-bottom: 8px; color: #6c757d;">لا توجد نتائج</div>
        <div style="color: #6c757d;">لم يتم العثور على أي بنود مطابقة للبحث</div>
    </div>
    @else
    <div style="background: white; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); padding: 60px; text-align: center;">
        <div style="font-size: 48px; margin-bottom: 16px;">⚖️</div>
        <div style="font-size: 18px; font-weight: 600; margin-bottom: 8px; color: #6c757d;">ابدأ المقارنة</div>
        <div style="color: #6c757d;">ابحث عن بند لمقارنة أسعاره من مصادر مختلفة</div>
    </div>
    @endif
</div>
@endsection
