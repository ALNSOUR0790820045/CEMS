@extends('layouts.app')

@section('content')
<div style="padding: 40px;">
    <div style="max-width: 1400px; margin: 0 auto;">
        <h1 style="margin-bottom: 10px; font-size: 2rem; font-weight: 700;">لوحة حماية المواعيد التعاقدية</h1>
        <p style="color: #86868b; margin-bottom: 30px;">مراقبة مواعيد الإشعارات وحماية حقوق المقاول من سقوط الحق</p>

        <!-- Statistics Cards -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 20px; margin-bottom: 30px;">
            <div style="background: white; padding: 25px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
                <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 15px;">
                    <div>
                        <p style="color: #86868b; font-size: 0.85rem; margin-bottom: 5px;">إجمالي الأحداث</p>
                        <h2 style="font-size: 2rem; font-weight: 700; color: #1d1d1f;">{{ $statistics['total_events'] }}</h2>
                    </div>
                    <i data-lucide="list" style="width: 24px; height: 24px; color: #0071e3;"></i>
                </div>
            </div>

            <div style="background: white; padding: 25px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
                <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 15px;">
                    <div>
                        <p style="color: #86868b; font-size: 0.85rem; margin-bottom: 5px;">أحداث نشطة</p>
                        <h2 style="font-size: 2rem; font-weight: 700; color: #34c759;">{{ $statistics['active_events'] }}</h2>
                    </div>
                    <i data-lucide="activity" style="width: 24px; height: 24px; color: #34c759;"></i>
                </div>
            </div>

            <div style="background: white; padding: 25px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
                <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 15px;">
                    <div>
                        <p style="color: #86868b; font-size: 0.85rem; margin-bottom: 5px;">قرب الانتهاء (7 أيام)</p>
                        <h2 style="font-size: 2rem; font-weight: 700; color: #ff9500;">{{ $statistics['expiring_soon'] }}</h2>
                    </div>
                    <i data-lucide="alert-triangle" style="width: 24px; height: 24px; color: #ff9500;"></i>
                </div>
            </div>

            <div style="background: white; padding: 25px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
                <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 15px;">
                    <div>
                        <p style="color: #86868b; font-size: 0.85rem; margin-bottom: 5px;">سقط الحق</p>
                        <h2 style="font-size: 2rem; font-weight: 700; color: #ff3b30;">{{ $statistics['expired_events'] }}</h2>
                    </div>
                    <i data-lucide="x-circle" style="width: 24px; height: 24px; color: #ff3b30;"></i>
                </div>
            </div>

            <div style="background: white; padding: 25px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
                <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 15px;">
                    <div>
                        <p style="color: #86868b; font-size: 0.85rem; margin-bottom: 5px;">تم الإشعار</p>
                        <h2 style="font-size: 2rem; font-weight: 700; color: #5856d6;">{{ $statistics['notice_sent'] }}</h2>
                    </div>
                    <i data-lucide="check-circle" style="width: 24px; height: 24px; color: #5856d6;"></i>
                </div>
            </div>
        </div>

        <!-- Expiring Events Table -->
        <div style="background: white; padding: 30px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); margin-bottom: 30px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h2 style="font-size: 1.3rem; font-weight: 700;">أحداث قرب الانتهاء</h2>
                <a href="{{ route('time-bar.expiring') }}" style="color: #0071e3; text-decoration: none; font-weight: 600;">عرض الكل →</a>
            </div>

            @if($expiringEvents->count() > 0)
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="border-bottom: 2px solid #f5f5f7;">
                            <th style="text-align: right; padding: 12px; font-weight: 600; color: #86868b; font-size: 0.85rem;">رقم الحدث</th>
                            <th style="text-align: right; padding: 12px; font-weight: 600; color: #86868b; font-size: 0.85rem;">العنوان</th>
                            <th style="text-align: right; padding: 12px; font-weight: 600; color: #86868b; font-size: 0.85rem;">المشروع</th>
                            <th style="text-align: right; padding: 12px; font-weight: 600; color: #86868b; font-size: 0.85rem;">آخر موعد</th>
                            <th style="text-align: right; padding: 12px; font-weight: 600; color: #86868b; font-size: 0.85rem;">الأيام المتبقية</th>
                            <th style="text-align: right; padding: 12px; font-weight: 600; color: #86868b; font-size: 0.85rem;">الإجراء</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($expiringEvents as $event)
                        <tr style="border-bottom: 1px solid #f5f5f7;">
                            <td style="padding: 15px;">
                                <span style="font-weight: 600;">{{ $event->event_number }}</span>
                            </td>
                            <td style="padding: 15px;">{{ $event->title }}</td>
                            <td style="padding: 15px;">{{ $event->project->name }}</td>
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
                                <a href="{{ route('time-bar.events.show', $event->id) }}" style="color: #0071e3; text-decoration: none; font-weight: 600;">عرض التفاصيل</a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <p style="text-align: center; color: #86868b; padding: 40px;">لا توجد أحداث قرب الانتهاء</p>
            @endif
        </div>

        <!-- Quick Actions -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 20px;">
            <a href="{{ route('time-bar.events.create') }}" style="background: #0071e3; color: white; padding: 25px; border-radius: 12px; text-decoration: none; display: flex; align-items: center; gap: 15px; transition: transform 0.2s;">
                <i data-lucide="plus-circle" style="width: 32px; height: 32px;"></i>
                <div>
                    <h3 style="font-size: 1.1rem; font-weight: 700; margin-bottom: 5px;">تسجيل حدث جديد</h3>
                    <p style="font-size: 0.85rem; opacity: 0.9;">إضافة حدث يستوجب إشعار</p>
                </div>
            </a>

            <a href="{{ route('time-bar.calendar') }}" style="background: white; color: #1d1d1f; padding: 25px; border-radius: 12px; text-decoration: none; display: flex; align-items: center; gap: 15px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); transition: transform 0.2s;">
                <i data-lucide="calendar" style="width: 32px; height: 32px; color: #5856d6;"></i>
                <div>
                    <h3 style="font-size: 1.1rem; font-weight: 700; margin-bottom: 5px;">تقويم المواعيد</h3>
                    <p style="font-size: 0.85rem; color: #86868b;">عرض المواعيد في التقويم</p>
                </div>
            </a>

            <a href="{{ route('time-bar.reports') }}" style="background: white; color: #1d1d1f; padding: 25px; border-radius: 12px; text-decoration: none; display: flex; align-items: center; gap: 15px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); transition: transform 0.2s;">
                <i data-lucide="file-text" style="width: 32px; height: 32px; color: #ff9500;"></i>
                <div>
                    <h3 style="font-size: 1.1rem; font-weight: 700; margin-bottom: 5px;">التقارير القانونية</h3>
                    <p style="font-size: 0.85rem; color: #86868b;">تقارير وإحصائيات</p>
                </div>
            </a>
        </div>
    </div>
</div>

<script>
    lucide.createIcons();
</script>
@endsection
