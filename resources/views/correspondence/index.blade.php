@extends('layouts.app')

@section('content')
<div style="padding: 40px; max-width: 1400px; margin: 0 auto;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
        <h1 style="margin: 0;">جميع المراسلات</h1>
        <div style="display: flex; gap: 10px;">
            <a href="{{ route('correspondence.search') }}" style="background: #f5f5f7; color: #1d1d1f; padding: 12px 24px; border-radius: 8px; text-decoration: none; font-weight: 600;">
                <i data-lucide="search" style="width: 18px; height: 18px; vertical-align: middle;"></i>
                بحث متقدم
            </a>
            <a href="{{ route('correspondence.create') }}" style="background: #0071e3; color: white; padding: 12px 24px; border-radius: 8px; text-decoration: none; font-weight: 600;">
                <i data-lucide="plus" style="width: 18px; height: 18px; vertical-align: middle;"></i>
                مراسلة جديدة
            </a>
        </div>
    </div>

    <!-- Quick Filters -->
    <div style="display: flex; gap: 10px; margin-bottom: 20px; flex-wrap: wrap;">
        <a href="{{ route('correspondence.index') }}" style="padding: 8px 16px; background: #0071e3; color: white; border-radius: 6px; text-decoration: none; font-size: 0.9rem; font-weight: 500;">الكل</a>
        <a href="{{ route('correspondence.incoming') }}" style="padding: 8px 16px; background: white; color: #1d1d1f; border-radius: 6px; text-decoration: none; font-size: 0.9rem; font-weight: 500; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">الوارد</a>
        <a href="{{ route('correspondence.outgoing') }}" style="padding: 8px 16px; background: white; color: #1d1d1f; border-radius: 6px; text-decoration: none; font-size: 0.9rem; font-weight: 500; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">الصادر</a>
        <a href="{{ route('correspondence.pending') }}" style="padding: 8px 16px; background: white; color: #1d1d1f; border-radius: 6px; text-decoration: none; font-size: 0.9rem; font-weight: 500; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">بانتظار الرد</a>
        <a href="{{ route('correspondence.overdue') }}" style="padding: 8px 16px; background: white; color: #ff3b30; border-radius: 6px; text-decoration: none; font-size: 0.9rem; font-weight: 500; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">المتأخرة</a>
    </div>

    <div style="background: white; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); overflow: hidden;">
        @if($correspondences->isEmpty())
            <div style="padding: 60px; text-align: center; color: #86868b;">
                <i data-lucide="inbox" style="width: 64px; height: 64px; margin-bottom: 20px; opacity: 0.3;"></i>
                <p style="font-size: 1.1rem; margin: 0;">لا توجد مراسلات</p>
            </div>
        @else
            <table style="width: 100%; border-collapse: collapse;">
                <thead style="background: #f5f5f7;">
                    <tr>
                        <th style="padding: 15px; text-align: right; font-weight: 600; color: #1d1d1f;">الرقم المرجعي</th>
                        <th style="padding: 15px; text-align: right; font-weight: 600; color: #1d1d1f;">النوع</th>
                        <th style="padding: 15px; text-align: right; font-weight: 600; color: #1d1d1f;">الموضوع</th>
                        <th style="padding: 15px; text-align: right; font-weight: 600; color: #1d1d1f;">من/إلى</th>
                        <th style="padding: 15px; text-align: right; font-weight: 600; color: #1d1d1f;">التاريخ</th>
                        <th style="padding: 15px; text-align: right; font-weight: 600; color: #1d1d1f;">الحالة</th>
                        <th style="padding: 15px; text-align: center; font-weight: 600; color: #1d1d1f;">إجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($correspondences as $correspondence)
                    <tr style="border-bottom: 1px solid #f5f5f7;">
                        <td style="padding: 15px;">
                            <a href="{{ route('correspondence.show', $correspondence) }}" style="color: #0071e3; text-decoration: none; font-weight: 500;">
                                {{ $correspondence->reference_number }}
                            </a>
                        </td>
                        <td style="padding: 15px;">
                            <span style="display: inline-block; padding: 4px 12px; background: {{ $correspondence->type === 'incoming' ? '#e3f2fd' : '#e8f5e9' }}; color: {{ $correspondence->type === 'incoming' ? '#0071e3' : '#34c759' }}; border-radius: 12px; font-size: 0.85rem; font-weight: 500;">
                                {{ $correspondence->type === 'incoming' ? 'وارد' : 'صادر' }}
                            </span>
                        </td>
                        <td style="padding: 15px;">{{ Str::limit($correspondence->subject, 40) }}</td>
                        <td style="padding: 15px;">
                            {{ $correspondence->type === 'incoming' ? $correspondence->from_entity : $correspondence->to_entity }}
                        </td>
                        <td style="padding: 15px; color: #86868b; font-size: 0.9rem;">
                            {{ $correspondence->document_date->format('Y-m-d') }}
                        </td>
                        <td style="padding: 15px;">
                            @php
                                $statusColors = [
                                    'draft' => '#86868b',
                                    'pending_approval' => '#ff9500',
                                    'approved' => '#34c759',
                                    'sent' => '#0071e3',
                                    'received' => '#5856d6',
                                    'pending_response' => '#ff9500',
                                    'responded' => '#34c759',
                                    'closed' => '#1d1d1f',
                                    'cancelled' => '#ff3b30'
                                ];
                                $statusLabels = [
                                    'draft' => 'مسودة',
                                    'pending_approval' => 'بانتظار الاعتماد',
                                    'approved' => 'معتمد',
                                    'sent' => 'مرسل',
                                    'received' => 'مستلم',
                                    'pending_response' => 'بانتظار الرد',
                                    'responded' => 'تم الرد',
                                    'closed' => 'مغلق',
                                    'cancelled' => 'ملغي'
                                ];
                            @endphp
                            <span style="display: inline-block; padding: 4px 12px; background: {{ $statusColors[$correspondence->status] }}22; color: {{ $statusColors[$correspondence->status] }}; border-radius: 12px; font-size: 0.85rem; font-weight: 500;">
                                {{ $statusLabels[$correspondence->status] }}
                            </span>
                        </td>
                        <td style="padding: 15px; text-align: center;">
                            <a href="{{ route('correspondence.show', $correspondence) }}" style="color: #0071e3; text-decoration: none; padding: 8px; display: inline-block;">
                                <i data-lucide="eye" style="width: 18px; height: 18px;"></i>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <div style="padding: 20px;">
                {{ $correspondences->links() }}
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
    lucide.createIcons();
</script>
@endpush
@endsection
