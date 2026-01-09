@extends('layouts.app')

@section('content')
<div style="padding: 20px; max-width: 1400px; margin: 0 auto;">
    <div style="margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center;">
        <h1 style="font-size: 28px; font-weight: 600; margin: 0;">قوائم الأسعار</h1>
        <a href="{{ route('price-lists.create') }}" style="background: var(--accent); color: white; padding: 12px 24px; border-radius: 8px; text-decoration: none; font-weight: 500;">
            إضافة قائمة جديدة
        </a>
    </div>

    @if(session('success'))
    <div style="background: #d4edda; color: #155724; padding: 12px 20px; border-radius: 8px; margin-bottom: 20px;">
        {{ session('success') }}
    </div>
    @endif

    <div style="background: white; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); overflow: hidden;">
        <table style="width: 100%; border-collapse: collapse;">
            <thead style="background: #f8f9fa;">
                <tr>
                    <th style="padding: 16px; text-align: right; font-weight: 600; border-bottom: 2px solid #dee2e6;">الكود</th>
                    <th style="padding: 16px; text-align: right; font-weight: 600; border-bottom: 2px solid #dee2e6;">الاسم</th>
                    <th style="padding: 16px; text-align: right; font-weight: 600; border-bottom: 2px solid #dee2e6;">النوع</th>
                    <th style="padding: 16px; text-align: right; font-weight: 600; border-bottom: 2px solid #dee2e6;">المصدر</th>
                    <th style="padding: 16px; text-align: right; font-weight: 600; border-bottom: 2px solid #dee2e6;">تاريخ السريان</th>
                    <th style="padding: 16px; text-align: right; font-weight: 600; border-bottom: 2px solid #dee2e6;">الحالة</th>
                    <th style="padding: 16px; text-align: right; font-weight: 600; border-bottom: 2px solid #dee2e6;">الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                @forelse($priceLists as $priceList)
                <tr style="border-bottom: 1px solid #dee2e6;">
                    <td style="padding: 16px;">{{ $priceList->code }}</td>
                    <td style="padding: 16px;">{{ $priceList->name }}</td>
                    <td style="padding: 16px;">
                        @switch($priceList->type)
                            @case('material') مواد @break
                            @case('labor') عمالة @break
                            @case('equipment') معدات @break
                            @case('subcontract') مقاولين @break
                            @case('composite') مركب @break
                        @endswitch
                    </td>
                    <td style="padding: 16px;">
                        @switch($priceList->source)
                            @case('internal') داخلي @break
                            @case('ministry') وزارة @break
                            @case('syndicate') نقابة @break
                            @case('market') سوق @break
                            @case('vendor') مورد @break
                        @endswitch
                    </td>
                    <td style="padding: 16px;">{{ $priceList->effective_date->format('Y-m-d') }}</td>
                    <td style="padding: 16px;">
                        <span style="padding: 4px 12px; border-radius: 12px; font-size: 13px; {{ $priceList->is_active ? 'background: #d4edda; color: #155724;' : 'background: #f8d7da; color: #721c24;' }}">
                            {{ $priceList->is_active ? 'نشط' : 'غير نشط' }}
                        </span>
                    </td>
                    <td style="padding: 16px;">
                        <a href="{{ route('price-lists.show', $priceList) }}" style="color: var(--accent); text-decoration: none; margin-left: 12px;">عرض</a>
                        <a href="{{ route('price-lists.edit', $priceList) }}" style="color: #ffc107; text-decoration: none; margin-left: 12px;">تعديل</a>
                        <a href="{{ route('price-lists.items', $priceList) }}" style="color: #17a2b8; text-decoration: none;">البنود</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="padding: 40px; text-align: center; color: #6c757d;">
                        لا توجد قوائم أسعار
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top: 20px;">
        {{ $priceLists->links() }}
    </div>
</div>
@endsection
