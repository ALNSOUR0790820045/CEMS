@extends('layouts.app')

@section('content')
<div style="padding: 40px; max-width: 1400px; margin: 0 auto;">
    <h1 style="margin-bottom: 30px;">البحث المتقدم</h1>

    <form method="GET" action="{{ route('correspondence.search') }}" style="background: white; padding: 30px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); margin-bottom: 20px;">
        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-bottom: 20px;">
            <div>
                <label style="display: block; margin-bottom: 5px; font-weight: 600;">الرقم المرجعي</label>
                <input type="text" name="reference_number" value="{{ request('reference_number') }}" placeholder="OUT-2026-0001" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
            </div>
            
            <div>
                <label style="display: block; margin-bottom: 5px; font-weight: 600;">النوع</label>
                <select name="type" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
                    <option value="">الكل</option>
                    <option value="incoming" {{ request('type') === 'incoming' ? 'selected' : '' }}>وارد</option>
                    <option value="outgoing" {{ request('type') === 'outgoing' ? 'selected' : '' }}>صادر</option>
                </select>
            </div>
            
            <div>
                <label style="display: block; margin-bottom: 5px; font-weight: 600;">التصنيف</label>
                <select name="category" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
                    <option value="">الكل</option>
                    <option value="letter" {{ request('category') === 'letter' ? 'selected' : '' }}>خطاب</option>
                    <option value="memo" {{ request('category') === 'memo' ? 'selected' : '' }}>مذكرة</option>
                    <option value="email" {{ request('category') === 'email' ? 'selected' : '' }}>بريد إلكتروني</option>
                    <option value="fax" {{ request('category') === 'fax' ? 'selected' : '' }}>فاكس</option>
                    <option value="notice" {{ request('category') === 'notice' ? 'selected' : '' }}>إشعار</option>
                    <option value="instruction" {{ request('category') === 'instruction' ? 'selected' : '' }}>تعليمات</option>
                    <option value="request" {{ request('category') === 'request' ? 'selected' : '' }}>طلب</option>
                    <option value="approval" {{ request('category') === 'approval' ? 'selected' : '' }}>موافقة</option>
                    <option value="rejection" {{ request('category') === 'rejection' ? 'selected' : '' }}>رفض</option>
                </select>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-bottom: 20px;">
            <div>
                <label style="display: block; margin-bottom: 5px; font-weight: 600;">الحالة</label>
                <select name="status" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
                    <option value="">الكل</option>
                    <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>مسودة</option>
                    <option value="pending_approval" {{ request('status') === 'pending_approval' ? 'selected' : '' }}>بانتظار الاعتماد</option>
                    <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>معتمد</option>
                    <option value="sent" {{ request('status') === 'sent' ? 'selected' : '' }}>مرسل</option>
                    <option value="received" {{ request('status') === 'received' ? 'selected' : '' }}>مستلم</option>
                    <option value="pending_response" {{ request('status') === 'pending_response' ? 'selected' : '' }}>بانتظار الرد</option>
                    <option value="responded" {{ request('status') === 'responded' ? 'selected' : '' }}>تم الرد</option>
                </select>
            </div>
            
            <div>
                <label style="display: block; margin-bottom: 5px; font-weight: 600;">من (الجهة)</label>
                <input type="text" name="from_entity" value="{{ request('from_entity') }}" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
            </div>
            
            <div>
                <label style="display: block; margin-bottom: 5px; font-weight: 600;">إلى (الجهة)</label>
                <input type="text" name="to_entity" value="{{ request('to_entity') }}" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
            </div>
        </div>

        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-bottom: 20px;">
            <div>
                <label style="display: block; margin-bottom: 5px; font-weight: 600;">التاريخ من</label>
                <input type="date" name="date_from" value="{{ request('date_from') }}" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
            </div>
            
            <div>
                <label style="display: block; margin-bottom: 5px; font-weight: 600;">التاريخ إلى</label>
                <input type="date" name="date_to" value="{{ request('date_to') }}" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
            </div>
            
            <div>
                <label style="display: block; margin-bottom: 5px; font-weight: 600;">الموضوع</label>
                <input type="text" name="subject" value="{{ request('subject') }}" placeholder="ابحث في الموضوع..." style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
            </div>
        </div>

        <div style="display: flex; gap: 10px;">
            <button type="submit" style="background: #0071e3; color: white; padding: 12px 30px; border: none; border-radius: 8px; cursor: pointer; font-family: 'Cairo', sans-serif; font-weight: 600;">
                <i data-lucide="search" style="width: 18px; height: 18px; vertical-align: middle;"></i>
                بحث
            </button>
            <a href="{{ route('correspondence.search') }}" style="background: #f5f5f7; color: #1d1d1f; padding: 12px 30px; border-radius: 8px; text-decoration: none; font-weight: 600; display: inline-block;">
                إعادة تعيين
            </a>
        </div>
    </form>

    @if(isset($correspondences))
        <div style="background: white; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); overflow: hidden;">
            @if($correspondences->isEmpty())
                <div style="padding: 60px; text-align: center; color: #86868b;">
                    <i data-lucide="search" style="width: 64px; height: 64px; margin-bottom: 20px; opacity: 0.3;"></i>
                    <p style="font-size: 1.1rem; margin: 0;">لم يتم العثور على نتائج</p>
                </div>
            @else
                <div style="padding: 20px; background: #f5f5f7; border-bottom: 1px solid #ddd;">
                    <p style="margin: 0; color: #1d1d1f; font-weight: 600;">تم العثور على {{ $correspondences->total() }} نتيجة</p>
                </div>

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
                    {{ $correspondences->appends(request()->query())->links() }}
                </div>
            @endif
        </div>
    @endif
</div>

@push('scripts')
<script>
    lucide.createIcons();
</script>
@endpush
@endsection
