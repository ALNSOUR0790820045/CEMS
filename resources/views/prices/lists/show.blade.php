@extends('layouts.app')

@section('content')
<div style="padding: 20px; max-width: 1400px; margin: 0 auto;">
    <div style="margin-bottom: 20px;">
        <a href="{{ route('price-lists.index') }}" style="color: var(--accent); text-decoration: none; font-size: 14px;">← العودة للقوائم</a>
        <h1 style="font-size: 28px; font-weight: 600; margin: 10px 0;">{{ $priceList->name }}</h1>
    </div>

    @if(session('success'))
    <div style="background: #d4edda; color: #155724; padding: 12px 20px; border-radius: 8px; margin-bottom: 20px;">
        {{ session('success') }}
    </div>
    @endif

    <div style="background: white; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); padding: 24px; margin-bottom: 20px;">
        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px;">
            <div>
                <div style="font-size: 13px; color: #6c757d; margin-bottom: 4px;">الكود</div>
                <div style="font-weight: 600;">{{ $priceList->code }}</div>
            </div>
            <div>
                <div style="font-size: 13px; color: #6c757d; margin-bottom: 4px;">النوع</div>
                <div style="font-weight: 600;">
                    @switch($priceList->type)
                        @case('material') مواد @break
                        @case('labor') عمالة @break
                        @case('equipment') معدات @break
                        @case('subcontract') مقاولين @break
                        @case('composite') مركب @break
                    @endswitch
                </div>
            </div>
            <div>
                <div style="font-size: 13px; color: #6c757d; margin-bottom: 4px;">المصدر</div>
                <div style="font-weight: 600;">
                    @switch($priceList->source)
                        @case('internal') داخلي @break
                        @case('ministry') وزارة الأشغال @break
                        @case('syndicate') النقابة @break
                        @case('market') السوق @break
                        @case('vendor') مورد @break
                    @endswitch
                </div>
            </div>
            <div>
                <div style="font-size: 13px; color: #6c757d; margin-bottom: 4px;">تاريخ السريان</div>
                <div style="font-weight: 600;">{{ $priceList->effective_date->format('Y-m-d') }}</div>
            </div>
            <div>
                <div style="font-size: 13px; color: #6c757d; margin-bottom: 4px;">تاريخ الانتهاء</div>
                <div style="font-weight: 600;">{{ $priceList->expiry_date ? $priceList->expiry_date->format('Y-m-d') : '-' }}</div>
            </div>
            <div>
                <div style="font-size: 13px; color: #6c757d; margin-bottom: 4px;">العملة</div>
                <div style="font-weight: 600;">{{ $priceList->currency }}</div>
            </div>
            @if($priceList->region)
            <div>
                <div style="font-size: 13px; color: #6c757d; margin-bottom: 4px;">المنطقة</div>
                <div style="font-weight: 600;">{{ $priceList->region->name }}</div>
            </div>
            @endif
            <div>
                <div style="font-size: 13px; color: #6c757d; margin-bottom: 4px;">الحالة</div>
                <span style="padding: 4px 12px; border-radius: 12px; font-size: 13px; {{ $priceList->is_active ? 'background: #d4edda; color: #155724;' : 'background: #f8d7da; color: #721c24;' }}">
                    {{ $priceList->is_active ? 'نشط' : 'غير نشط' }}
                </span>
            </div>
        </div>
        
        @if($priceList->notes)
        <div style="margin-top: 20px; padding-top: 20px; border-top: 1px solid #dee2e6;">
            <div style="font-size: 13px; color: #6c757d; margin-bottom: 4px;">ملاحظات</div>
            <div>{{ $priceList->notes }}</div>
        </div>
        @endif
    </div>

    <div style="background: white; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); overflow: hidden;">
        <div style="padding: 20px; border-bottom: 1px solid #dee2e6; display: flex; justify-content: space-between; align-items: center;">
            <h2 style="font-size: 20px; font-weight: 600; margin: 0;">البنود ({{ $priceList->items->count() }})</h2>
            <a href="{{ route('price-list-items.create', $priceList) }}" style="background: var(--accent); color: white; padding: 10px 20px; border-radius: 8px; text-decoration: none; font-weight: 500;">
                إضافة بند
            </a>
        </div>
        
        <table style="width: 100%; border-collapse: collapse;">
            <thead style="background: #f8f9fa;">
                <tr>
                    <th style="padding: 12px; text-align: right; font-weight: 600; border-bottom: 2px solid #dee2e6;">الكود</th>
                    <th style="padding: 12px; text-align: right; font-weight: 600; border-bottom: 2px solid #dee2e6;">الاسم</th>
                    <th style="padding: 12px; text-align: right; font-weight: 600; border-bottom: 2px solid #dee2e6;">الوحدة</th>
                    <th style="padding: 12px; text-align: right; font-weight: 600; border-bottom: 2px solid #dee2e6;">السعر</th>
                    <th style="padding: 12px; text-align: right; font-weight: 600; border-bottom: 2px solid #dee2e6;">الحالة</th>
                </tr>
            </thead>
            <tbody>
                @forelse($priceList->items as $item)
                <tr style="border-bottom: 1px solid #dee2e6;">
                    <td style="padding: 12px;">{{ $item->item_code }}</td>
                    <td style="padding: 12px;">{{ $item->item_name }}</td>
                    <td style="padding: 12px;">{{ $item->unit }}</td>
                    <td style="padding: 12px;">{{ number_format($item->unit_price, 2) }} {{ $priceList->currency }}</td>
                    <td style="padding: 12px;">
                        <span style="padding: 4px 12px; border-radius: 12px; font-size: 13px; {{ $item->is_active ? 'background: #d4edda; color: #155724;' : 'background: #f8d7da; color: #721c24;' }}">
                            {{ $item->is_active ? 'نشط' : 'غير نشط' }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="padding: 40px; text-align: center; color: #6c757d;">
                        لا توجد بنود في هذه القائمة
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
