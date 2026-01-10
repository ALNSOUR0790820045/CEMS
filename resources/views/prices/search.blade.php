@extends('layouts.app')

@section('content')
<div style="padding: 20px; max-width: 1400px; margin: 0 auto;">
    <h1 style="font-size: 28px; font-weight: 600; margin-bottom: 20px;">البحث عن الأسعار</h1>

    <div style="background: white; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); padding: 24px; margin-bottom: 20px;">
        <form method="GET" action="{{ route('prices.search') }}">
            <div style="display: grid; grid-template-columns: 2fr 1fr 1fr auto; gap: 12px;">
                <input type="text" name="search" placeholder="ابحث عن البند..." value="{{ request('search') }}"
                       style="padding: 12px; border: 1px solid #dee2e6; border-radius: 8px; font-size: 14px;">
                
                <select name="type"
                        style="padding: 12px; border: 1px solid #dee2e6; border-radius: 8px; font-size: 14px;">
                    <option value="">كل الأنواع</option>
                    <option value="material" {{ request('type') == 'material' ? 'selected' : '' }}>مواد</option>
                    <option value="labor" {{ request('type') == 'labor' ? 'selected' : '' }}>عمالة</option>
                    <option value="equipment" {{ request('type') == 'equipment' ? 'selected' : '' }}>معدات</option>
                    <option value="subcontract" {{ request('type') == 'subcontract' ? 'selected' : '' }}>مقاولين</option>
                </select>

                <select name="source"
                        style="padding: 12px; border: 1px solid #dee2e6; border-radius: 8px; font-size: 14px;">
                    <option value="">كل المصادر</option>
                    <option value="internal" {{ request('source') == 'internal' ? 'selected' : '' }}>داخلي</option>
                    <option value="ministry" {{ request('source') == 'ministry' ? 'selected' : '' }}>وزارة</option>
                    <option value="syndicate" {{ request('source') == 'syndicate' ? 'selected' : '' }}>نقابة</option>
                    <option value="market" {{ request('source') == 'market' ? 'selected' : '' }}>سوق</option>
                    <option value="vendor" {{ request('source') == 'vendor' ? 'selected' : '' }}>مورد</option>
                </select>

                <button type="submit"
                        style="background: var(--accent); color: white; padding: 12px 24px; border: none; border-radius: 8px; font-weight: 500; cursor: pointer;">
                    بحث
                </button>
            </div>
        </form>
    </div>

    <div style="background: white; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); overflow: hidden;">
        @if(isset($items))
        <table style="width: 100%; border-collapse: collapse;">
            <thead style="background: #f8f9fa;">
                <tr>
                    <th style="padding: 16px; text-align: right; font-weight: 600; border-bottom: 2px solid #dee2e6;">الكود</th>
                    <th style="padding: 16px; text-align: right; font-weight: 600; border-bottom: 2px solid #dee2e6;">الاسم</th>
                    <th style="padding: 16px; text-align: right; font-weight: 600; border-bottom: 2px solid #dee2e6;">القائمة</th>
                    <th style="padding: 16px; text-align: right; font-weight: 600; border-bottom: 2px solid #dee2e6;">المصدر</th>
                    <th style="padding: 16px; text-align: right; font-weight: 600; border-bottom: 2px solid #dee2e6;">الوحدة</th>
                    <th style="padding: 16px; text-align: right; font-weight: 600; border-bottom: 2px solid #dee2e6;">السعر</th>
                    <th style="padding: 16px; text-align: right; font-weight: 600; border-bottom: 2px solid #dee2e6;">تاريخ السريان</th>
                </tr>
            </thead>
            <tbody>
                @forelse($items as $item)
                <tr style="border-bottom: 1px solid #dee2e6;">
                    <td style="padding: 16px;">{{ $item->item_code }}</td>
                    <td style="padding: 16px;">
                        <div style="font-weight: 600;">{{ $item->item_name }}</div>
                        @if($item->description)
                        <div style="font-size: 13px; color: #6c757d; margin-top: 4px;">{{ Str::limit($item->description, 60) }}</div>
                        @endif
                    </td>
                    <td style="padding: 16px;">{{ $item->priceList->name }}</td>
                    <td style="padding: 16px;">
                        @switch($item->priceList->source)
                            @case('internal') داخلي @break
                            @case('ministry') وزارة @break
                            @case('syndicate') نقابة @break
                            @case('market') سوق @break
                            @case('vendor') مورد @break
                        @endswitch
                    </td>
                    <td style="padding: 16px;">{{ $item->unit }}</td>
                    <td style="padding: 16px; font-weight: 600;">{{ number_format($item->unit_price, 2) }} {{ $item->priceList->currency }}</td>
                    <td style="padding: 16px;">{{ $item->priceList->effective_date->format('Y-m-d') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="padding: 40px; text-align: center; color: #6c757d;">
                        لا توجد نتائج للبحث
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        @if(isset($items) && $items->hasPages())
        <div style="padding: 20px;">
            {{ $items->links() }}
        </div>
        @endif
        @else
        <div style="padding: 60px; text-align: center; color: #6c757d;">
            <div style="font-size: 48px; margin-bottom: 16px;">🔍</div>
            <div style="font-size: 18px; font-weight: 600; margin-bottom: 8px;">ابدأ البحث</div>
            <div>استخدم النموذج أعلاه للبحث عن الأسعار في قاعدة البيانات</div>
        </div>
        @endif
    </div>
</div>
@endsection
