@extends('layouts.app')

@section('content')
<div style="padding: 40px;">
    <div style="max-width: 1400px; margin: 0 auto;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
            <div>
                <h1 style="font-size: 2rem; font-weight: 700; margin-bottom: 5px;">أحداث قرب الانتهاء</h1>
                <p style="color: #86868b;">الأحداث التي يتبقى لها {{ $days }} يوم أو أقل</p>
            </div>
            <a href="{{ route('time-bar.events.create') }}" style="background: #0071e3; color: white; padding: 12px 24px; border-radius: 8px; text-decoration: none; font-weight: 600; display: flex; align-items: center; gap: 8px;">
                <i data-lucide="plus" style="width: 18px; height: 18px;"></i>
                تسجيل حدث جديد
            </a>
        </div>

        <!-- Filters -->
        <div style="background: white; padding: 20px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); margin-bottom: 20px;">
            <form method="GET" style="display: flex; gap: 15px; flex-wrap: wrap;">
                <select name="status" style="padding: 10px; border: 1px solid #ddd; border-radius: 6px; font-family: 'Cairo', sans-serif;">
                    <option value="">جميع الحالات</option>
                    <option value="identified">تم تحديده</option>
                    <option value="notice_pending">بانتظار الإشعار</option>
                    <option value="notice_sent">تم الإشعار</option>
                    <option value="claim_submitted">تم تقديم المطالبة</option>
                    <option value="resolved">تمت التسوية</option>
                    <option value="time_barred">سقط الحق</option>
                    <option value="cancelled">ملغي</option>
                </select>

                <select name="priority" style="padding: 10px; border: 1px solid #ddd; border-radius: 6px; font-family: 'Cairo', sans-serif;">
                    <option value="">جميع الأولويات</option>
                    <option value="low">منخفضة</option>
                    <option value="medium">متوسطة</option>
                    <option value="high">عالية</option>
                    <option value="critical">حرجة</option>
                </select>

                <button type="submit" style="background: #0071e3; color: white; padding: 10px 20px; border: none; border-radius: 6px; cursor: pointer; font-family: 'Cairo', sans-serif; font-weight: 600;">تصفية</button>
                <a href="{{ route('time-bar.events.index') }}" style="padding: 10px 20px; text-decoration: none; color: #666; border: 1px solid #ddd; border-radius: 6px; display: flex; align-items: center;">إعادة تعيين</a>
            </form>
        </div>

        <!-- Events Table -->
        <div style="background: white; padding: 30px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
            @if($events->count() > 0)
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="border-bottom: 2px solid #f5f5f7;">
                            <th style="text-align: right; padding: 12px; font-weight: 600; color: #86868b; font-size: 0.85rem;">رقم الحدث</th>
                            <th style="text-align: right; padding: 12px; font-weight: 600; color: #86868b; font-size: 0.85rem;">العنوان</th>
                            <th style="text-align: right; padding: 12px; font-weight: 600; color: #86868b; font-size: 0.85rem;">المشروع</th>
                            <th style="text-align: right; padding: 12px; font-weight: 600; color: #86868b; font-size: 0.85rem;">تاريخ الحدث</th>
                            <th style="text-align: right; padding: 12px; font-weight: 600; color: #86868b; font-size: 0.85rem;">آخر موعد</th>
                            <th style="text-align: right; padding: 12px; font-weight: 600; color: #86868b; font-size: 0.85rem;">الأيام المتبقية</th>
                            <th style="text-align: right; padding: 12px; font-weight: 600; color: #86868b; font-size: 0.85rem;">الحالة</th>
                            <th style="text-align: right; padding: 12px; font-weight: 600; color: #86868b; font-size: 0.85rem;">الأولوية</th>
                            <th style="text-align: center; padding: 12px; font-weight: 600; color: #86868b; font-size: 0.85rem;">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($events as $event)
                        <tr style="border-bottom: 1px solid #f5f5f7;">
                            <td style="padding: 15px;">
                                <span style="font-weight: 600;">{{ $event->event_number }}</span>
                            </td>
                            <td style="padding: 15px;">{{ $event->title }}</td>
                            <td style="padding: 15px;">{{ $event->project->name }}</td>
                            <td style="padding: 15px;">{{ $event->event_date->format('Y-m-d') }}</td>
                            <td style="padding: 15px;">{{ $event->notice_deadline->format('Y-m-d') }}</td>
                            <td style="padding: 15px;">
                                @if($event->days_remaining <= 0)
                                <span style="background: #ff3b30; color: white; padding: 4px 12px; border-radius: 12px; font-size: 0.85rem; font-weight: 600;">انتهى</span>
                                @elseif($event->days_remaining <= 3)
                                <span style="background: #ff3b30; color: white; padding: 4px 12px; border-radius: 12px; font-size: 0.85rem; font-weight: 600;">{{ $event->days_remaining }} يوم</span>
                                @elseif($event->days_remaining <= 7)
                                <span style="background: #ff9500; color: white; padding: 4px 12px; border-radius: 12px; font-size: 0.85rem; font-weight: 600;">{{ $event->days_remaining }} يوم</span>
                                @else
                                <span style="background: #34c759; color: white; padding: 4px 12px; border-radius: 12px; font-size: 0.85rem; font-weight: 600;">{{ $event->days_remaining }} يوم</span>
                                @endif
                            </td>
                            <td style="padding: 15px;">
                                @php
                                $statusColors = [
                                    'identified' => '#5856d6',
                                    'notice_pending' => '#ff9500',
                                    'notice_sent' => '#34c759',
                                    'claim_submitted' => '#0071e3',
                                    'resolved' => '#34c759',
                                    'time_barred' => '#ff3b30',
                                    'cancelled' => '#86868b',
                                ];
                                $statusLabels = [
                                    'identified' => 'تم تحديده',
                                    'notice_pending' => 'بانتظار الإشعار',
                                    'notice_sent' => 'تم الإشعار',
                                    'claim_submitted' => 'تم تقديم المطالبة',
                                    'resolved' => 'تمت التسوية',
                                    'time_barred' => 'سقط الحق',
                                    'cancelled' => 'ملغي',
                                ];
                                @endphp
                                <span style="background: {{ $statusColors[$event->status] ?? '#86868b' }}; color: white; padding: 4px 12px; border-radius: 12px; font-size: 0.85rem; font-weight: 600;">
                                    {{ $statusLabels[$event->status] ?? $event->status }}
                                </span>
                            </td>
                            <td style="padding: 15px;">
                                @php
                                $priorityColors = [
                                    'low' => '#86868b',
                                    'medium' => '#0071e3',
                                    'high' => '#ff9500',
                                    'critical' => '#ff3b30',
                                ];
                                $priorityLabels = [
                                    'low' => 'منخفضة',
                                    'medium' => 'متوسطة',
                                    'high' => 'عالية',
                                    'critical' => 'حرجة',
                                ];
                                @endphp
                                <span style="background: {{ $priorityColors[$event->priority] ?? '#86868b' }}; color: white; padding: 4px 12px; border-radius: 12px; font-size: 0.85rem; font-weight: 600;">
                                    {{ $priorityLabels[$event->priority] ?? $event->priority }}
                                </span>
                            </td>
                            <td style="padding: 15px; text-align: center;">
                                <a href="{{ route('time-bar.events.show', $event->id) }}" style="color: #0071e3; text-decoration: none; font-weight: 600; margin-left: 10px;">عرض</a>
                                <a href="{{ route('time-bar.events.edit', $event->id) }}" style="color: #ff9500; text-decoration: none; font-weight: 600;">تعديل</a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div style="margin-top: 30px;">
                {{ $events->links() }}
            </div>
            @else
            <div style="text-align: center; padding: 60px 20px;">
                <i data-lucide="inbox" style="width: 64px; height: 64px; color: #86868b; margin-bottom: 20px;"></i>
                <h3 style="font-size: 1.2rem; font-weight: 700; margin-bottom: 10px;">لا توجد أحداث</h3>
                <p style="color: #86868b; margin-bottom: 20px;">لم يتم تسجيل أي أحداث بعد</p>
                <a href="{{ route('time-bar.events.create') }}" style="background: #0071e3; color: white; padding: 12px 24px; border-radius: 8px; text-decoration: none; font-weight: 600; display: inline-flex; align-items: center; gap: 8px;">
                    <i data-lucide="plus" style="width: 18px; height: 18px;"></i>
                    تسجيل حدث جديد
                </a>
            </div>
            @endif
        </div>
    </div>
</div>

<script>
    lucide.createIcons();
</script>
@endsection
