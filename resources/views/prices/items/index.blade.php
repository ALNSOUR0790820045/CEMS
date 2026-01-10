@extends('layouts.app')

@section('content')
<div style="padding: 20px; max-width: 1400px; margin: 0 auto;">
    <div style="margin-bottom: 20px;">
        <a href="{{ route('price-lists.show', $priceList) }}" style="color: var(--accent); text-decoration: none; font-size: 14px;">← العودة للقائمة</a>
        <h1 style="font-size: 28px; font-weight: 600; margin: 10px 0;">بنود الأسعار</h1>
        <p style="color: #6c757d; margin: 5px 0;">{{ $priceList->name }}</p>
    </div>

    @if(session('success'))
    <div style="background: #d4edda; color: #155724; padding: 12px 20px; border-radius: 8px; margin-bottom: 20px;">
        {{ session('success') }}
    </div>
    @endif

    <div style="background: white; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); overflow: hidden;">
        <div style="padding: 20px; border-bottom: 1px solid #dee2e6; display: flex; justify-content: space-between; align-items: center;">
            <h2 style="font-size: 20px; font-weight: 600; margin: 0;">البنود ({{ $items->total() }})</h2>
            <a href="{{ route('price-list-items.create', $priceList) }}" style="background: var(--accent); color: white; padding: 10px 20px; border-radius: 8px; text-decoration: none; font-weight: 500;">
                إضافة بند
            </a>
        </div>

        <table style="width: 100%; border-collapse: collapse;">
            <thead style="background: #f8f9fa;">
                <tr>
                    <th style="padding: 16px; text-align: right; font-weight: 600; border-bottom: 2px solid #dee2e6;">الكود</th>
                    <th style="padding: 16px; text-align: right; font-weight: 600; border-bottom: 2px solid #dee2e6;">الاسم</th>
                    <th style="padding: 16px; text-align: right; font-weight: 600; border-bottom: 2px solid #dee2e6;">الوحدة</th>
                    <th style="padding: 16px; text-align: right; font-weight: 600; border-bottom: 2px solid #dee2e6;">السعر</th>
                    <th style="padding: 16px; text-align: right; font-weight: 600; border-bottom: 2px solid #dee2e6;">النطاق</th>
                    <th style="padding: 16px; text-align: right; font-weight: 600; border-bottom: 2px solid #dee2e6;">الحالة</th>
                    <th style="padding: 16px; text-align: right; font-weight: 600; border-bottom: 2px solid #dee2e6;">الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                @forelse($items as $item)
                <tr style="border-bottom: 1px solid #dee2e6;">
                    <td style="padding: 16px;">{{ $item->item_code }}</td>
                    <td style="padding: 16px;">
                        <div style="font-weight: 600;">{{ $item->item_name }}</div>
                        @if($item->material)
                            <div style="font-size: 13px; color: #6c757d; margin-top: 2px;">{{ $item->material->name }}</div>
                        @elseif($item->laborCategory)
                            <div style="font-size: 13px; color: #6c757d; margin-top: 2px;">{{ $item->laborCategory->name }}</div>
                        @elseif($item->equipmentCategory)
                            <div style="font-size: 13px; color: #6c757d; margin-top: 2px;">{{ $item->equipmentCategory->name }}</div>
                        @endif
                    </td>
                    <td style="padding: 16px;">{{ $item->unit }}</td>
                    <td style="padding: 16px; font-weight: 600;">{{ number_format($item->unit_price, 2) }}</td>
                    <td style="padding: 16px; font-size: 13px; color: #6c757d;">
                        @if($item->min_price || $item->max_price)
                            {{ $item->min_price ? number_format($item->min_price, 2) : '-' }} -
                            {{ $item->max_price ? number_format($item->max_price, 2) : '-' }}
                        @else
                            -
                        @endif
                    </td>
                    <td style="padding: 16px;">
                        <span style="padding: 4px 12px; border-radius: 12px; font-size: 13px; {{ $item->is_active ? 'background: #d4edda; color: #155724;' : 'background: #f8d7da; color: #721c24;' }}">
                            {{ $item->is_active ? 'نشط' : 'غير نشط' }}
                        </span>
                    </td>
                    <td style="padding: 16px;">
                        <a href="{{ route('price-list-items.history', $item) }}" style="color: var(--accent); text-decoration: none;">التاريخ</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="padding: 40px; text-align: center; color: #6c757d;">
                        لا توجد بنود في هذه القائمة
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top: 20px;">
        {{ $items->links() }}
    </div>
</div>
@endsection
