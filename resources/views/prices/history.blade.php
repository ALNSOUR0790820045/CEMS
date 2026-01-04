@extends('layouts.app')

@section('content')
<div style="padding: 20px; max-width: 1200px; margin: 0 auto;">
    <div style="margin-bottom: 20px;">
        <a href="{{ route('price-lists.items', $item->price_list_id) }}" style="color: var(--accent); text-decoration: none; font-size: 14px;">← العودة للبنود</a>
        <h1 style="font-size: 28px; font-weight: 600; margin: 10px 0;">تاريخ تغيرات السعر</h1>
        <p style="color: #6c757d; margin: 5px 0;">{{ $item->item_name }} ({{ $item->item_code }})</p>
    </div>

    <div style="background: white; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); padding: 24px; margin-bottom: 20px;">
        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px;">
            <div>
                <div style="font-size: 13px; color: #6c757d; margin-bottom: 4px;">السعر الحالي</div>
                <div style="font-size: 28px; font-weight: 700; color: var(--accent);">
                    {{ number_format($item->unit_price, 2) }} {{ $item->priceList->currency }}
                </div>
            </div>
            <div>
                <div style="font-size: 13px; color: #6c757d; margin-bottom: 4px;">عدد التغييرات</div>
                <div style="font-size: 28px; font-weight: 700;">{{ $history->total() }}</div>
            </div>
            <div>
                <div style="font-size: 13px; color: #6c757d; margin-bottom: 4px;">آخر تحديث</div>
                <div style="font-size: 16px; font-weight: 600;">
                    {{ $item->updated_at->diffForHumans() }}
                </div>
            </div>
        </div>
    </div>

    <div style="background: white; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); overflow: hidden;">
        <div style="padding: 20px; border-bottom: 1px solid #dee2e6;">
            <h2 style="font-size: 20px; font-weight: 600; margin: 0;">سجل التغييرات</h2>
        </div>

        <div style="padding: 20px;">
            @forelse($history as $record)
            <div style="position: relative; padding: 20px; padding-right: 50px; border-right: 3px solid {{ $record->new_price > ($record->old_price ?? 0) ? '#dc3545' : '#28a745' }}; margin-bottom: 20px;">
                <div style="position: absolute; right: -12px; top: 20px; width: 20px; height: 20px; border-radius: 50%; background: {{ $record->new_price > ($record->old_price ?? 0) ? '#dc3545' : '#28a745' }}; border: 3px solid white; box-shadow: 0 0 0 2px {{ $record->new_price > ($record->old_price ?? 0) ? '#dc3545' : '#28a745' }};"></div>
                
                <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 12px;">
                    <div>
                        <div style="font-size: 16px; font-weight: 600; margin-bottom: 4px;">
                            {{ $record->effective_date->format('Y-m-d') }}
                        </div>
                        <div style="font-size: 13px; color: #6c757d;">
                            بواسطة: {{ $record->updater->name }}
                        </div>
                    </div>
                    <div style="text-align: left;">
                        @if($record->old_price)
                        <div style="display: flex; align-items: center; gap: 12px;">
                            <div>
                                <div style="font-size: 13px; color: #6c757d; margin-bottom: 2px;">السعر القديم</div>
                                <div style="font-size: 18px; font-weight: 600; text-decoration: line-through; color: #6c757d;">
                                    {{ number_format($record->old_price, 2) }}
                                </div>
                            </div>
                            <div style="font-size: 24px; color: #6c757d;">→</div>
                            <div>
                                <div style="font-size: 13px; color: #6c757d; margin-bottom: 2px;">السعر الجديد</div>
                                <div style="font-size: 24px; font-weight: 700; color: var(--accent);">
                                    {{ number_format($record->new_price, 2) }}
                                </div>
                            </div>
                        </div>
                        @else
                        <div>
                            <div style="font-size: 13px; color: #6c757d; margin-bottom: 2px;">السعر</div>
                            <div style="font-size: 24px; font-weight: 700; color: var(--accent);">
                                {{ number_format($record->new_price, 2) }}
                            </div>
                        </div>
                        @endif
                    </div>
                </div>

                @if($record->change_percentage)
                <div style="margin-bottom: 12px;">
                    <span style="padding: 6px 12px; border-radius: 8px; font-size: 14px; font-weight: 600; {{ $record->change_percentage > 0 ? 'background: #f8d7da; color: #721c24;' : 'background: #d4edda; color: #155724;' }}">
                        {{ $record->change_percentage > 0 ? '▲' : '▼' }}
                        {{ number_format(abs($record->change_percentage), 2) }}%
                    </span>
                </div>
                @endif

                @if($record->change_reason)
                <div style="margin-bottom: 8px;">
                    <span style="font-size: 13px; color: #6c757d;">السبب:</span>
                    <span style="font-weight: 500; margin-right: 8px;">
                        @switch($record->change_reason)
                            @case('market_change') تغير السوق @break
                            @case('supplier_update') تحديث المورد @break
                            @case('inflation') تضخم @break
                            @case('currency') سعر الصرف @break
                            @case('seasonal') موسمي @break
                            @case('other') أخرى @break
                        @endswitch
                    </span>
                </div>
                @endif

                @if($record->notes)
                <div style="font-size: 14px; color: #495057; background: #f8f9fa; padding: 12px; border-radius: 8px;">
                    {{ $record->notes }}
                </div>
                @endif
            </div>
            @empty
            <div style="padding: 40px; text-align: center; color: #6c757d;">
                <div style="font-size: 48px; margin-bottom: 16px;">📊</div>
                <div style="font-size: 18px; font-weight: 600; margin-bottom: 8px;">لا يوجد سجل</div>
                <div>لم يتم تسجيل أي تغييرات على السعر بعد</div>
            </div>
            @endforelse
        </div>
    </div>

    <div style="margin-top: 20px;">
        {{ $history->links() }}
    </div>
</div>
@endsection
