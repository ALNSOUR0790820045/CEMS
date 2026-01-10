@extends('layouts.app')

@section('content')
<div style="padding: 40px;">
    <div style="max-width: 1200px; margin: 0 auto;">
        <!-- Header -->
        <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 30px;">
            <div>
                <div style="display: flex; align-items: center; gap: 15px; margin-bottom: 10px;">
                    <h1 style="font-size: 2rem; font-weight: 700;">{{ $event->title }}</h1>
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
                    <span style="background: {{ $statusColors[$event->status] ?? '#86868b' }}; color: white; padding: 6px 15px; border-radius: 20px; font-size: 0.85rem; font-weight: 600;">
                        {{ $statusLabels[$event->status] ?? $event->status }}
                    </span>
                </div>
                <p style="color: #86868b;">رقم الحدث: <strong>{{ $event->event_number }}</strong></p>
            </div>
            <div style="display: flex; gap: 10px;">
                <a href="{{ route('time-bar.events.edit', $event->id) }}" style="background: #0071e3; color: white; padding: 12px 24px; border-radius: 8px; text-decoration: none; font-weight: 600; display: flex; align-items: center; gap: 8px;">
                    <i data-lucide="edit" style="width: 18px; height: 18px;"></i>
                    تعديل
                </a>
                <a href="{{ route('time-bar.events.index') }}" style="padding: 12px 24px; text-decoration: none; color: #666; border: 1px solid #ddd; border-radius: 8px; font-weight: 600; display: flex; align-items: center;">
                    العودة للقائمة
                </a>
            </div>
        </div>

        <!-- Alert Banner -->
        @if($event->days_remaining <= 7 && !$event->notice_sent)
        <div style="background: {{ $event->days_remaining <= 3 ? '#ff3b30' : '#ff9500' }}; color: white; padding: 20px 25px; border-radius: 12px; margin-bottom: 30px; display: flex; align-items: center; gap: 15px;">
            <i data-lucide="alert-triangle" style="width: 32px; height: 32px;"></i>
            <div>
                <h3 style="font-size: 1.1rem; font-weight: 700; margin-bottom: 5px;">
                    @if($event->days_remaining <= 0)
                    تحذير! انتهى موعد الإشعار
                    @else
                    تحذير! يتبقى {{ $event->days_remaining }} يوم فقط
                    @endif
                </h3>
                <p style="font-size: 0.9rem; opacity: 0.95;">
                    @if($event->days_remaining <= 0)
                    فات الموعد لإرسال الإشعار وقد يسقط الحق في المطالبة
                    @else
                    يجب إرسال الإشعار التعاقدي قبل {{ $event->notice_deadline->format('Y-m-d') }}
                    @endif
                </p>
            </div>
        </div>
        @endif

        <!-- Main Grid -->
        <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 30px;">
            <!-- Left Column -->
            <div>
                <!-- Event Details Card -->
                <div style="background: white; padding: 30px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); margin-bottom: 20px;">
                    <h2 style="font-size: 1.3rem; font-weight: 700; margin-bottom: 20px;">تفاصيل الحدث</h2>
                    
                    <div style="margin-bottom: 20px;">
                        <h4 style="color: #86868b; font-size: 0.85rem; margin-bottom: 8px;">الوصف</h4>
                        <p style="line-height: 1.6;">{{ $event->description }}</p>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-top: 25px;">
                        <div>
                            <h4 style="color: #86868b; font-size: 0.85rem; margin-bottom: 5px;">المشروع</h4>
                            <p style="font-weight: 600;">{{ $event->project->name }}</p>
                        </div>
                        @if($event->contract)
                        <div>
                            <h4 style="color: #86868b; font-size: 0.85rem; margin-bottom: 5px;">العقد</h4>
                            <p style="font-weight: 600;">{{ $event->contract->title }}</p>
                        </div>
                        @endif
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-top: 20px;">
                        <div>
                            <h4 style="color: #86868b; font-size: 0.85rem; margin-bottom: 5px;">نوع الحدث</h4>
                            <p style="font-weight: 600;">{{ $event->event_type }}</p>
                        </div>
                        <div>
                            <h4 style="color: #86868b; font-size: 0.85rem; margin-bottom: 5px;">الأولوية</h4>
                            @php
                            $priorityColors = ['low' => '#86868b', 'medium' => '#0071e3', 'high' => '#ff9500', 'critical' => '#ff3b30'];
                            $priorityLabels = ['low' => 'منخفضة', 'medium' => 'متوسطة', 'high' => 'عالية', 'critical' => 'حرجة'];
                            @endphp
                            <span style="background: {{ $priorityColors[$event->priority] ?? '#86868b' }}; color: white; padding: 4px 12px; border-radius: 12px; font-size: 0.85rem; font-weight: 600;">
                                {{ $priorityLabels[$event->priority] ?? $event->priority }}
                            </span>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-top: 20px;">
                        <div>
                            <h4 style="color: #86868b; font-size: 0.85rem; margin-bottom: 5px;">تاريخ وقوع الحدث</h4>
                            <p style="font-weight: 600;">{{ $event->event_date->format('Y-m-d') }}</p>
                        </div>
                        <div>
                            <h4 style="color: #86868b; font-size: 0.85rem; margin-bottom: 5px;">تاريخ الاكتشاف</h4>
                            <p style="font-weight: 600;">{{ $event->discovery_date->format('Y-m-d') }}</p>
                        </div>
                    </div>

                    @if($event->estimated_delay_days > 0 || $event->estimated_cost_impact > 0)
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-top: 20px;">
                        @if($event->estimated_delay_days > 0)
                        <div>
                            <h4 style="color: #86868b; font-size: 0.85rem; margin-bottom: 5px;">التأخير المتوقع</h4>
                            <p style="font-weight: 600;">{{ $event->estimated_delay_days }} يوم</p>
                        </div>
                        @endif
                        @if($event->estimated_cost_impact > 0)
                        <div>
                            <h4 style="color: #86868b; font-size: 0.85rem; margin-bottom: 5px;">التأثير المالي المتوقع</h4>
                            <p style="font-weight: 600;">{{ number_format($event->estimated_cost_impact, 2) }} {{ $event->currency }}</p>
                        </div>
                        @endif
                    </div>
                    @endif

                    @if($event->notes)
                    <div style="margin-top: 25px; padding-top: 20px; border-top: 1px solid #f5f5f7;">
                        <h4 style="color: #86868b; font-size: 0.85rem; margin-bottom: 8px;">ملاحظات</h4>
                        <p style="line-height: 1.6;">{{ $event->notes }}</p>
                    </div>
                    @endif
                </div>

                <!-- Notice Status Card -->
                @if($event->notice_sent)
                <div style="background: white; padding: 30px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
                    <h2 style="font-size: 1.3rem; font-weight: 700; margin-bottom: 20px; color: #34c759;">✓ تم إرسال الإشعار</h2>
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                        <div>
                            <h4 style="color: #86868b; font-size: 0.85rem; margin-bottom: 5px;">تاريخ الإرسال</h4>
                            <p style="font-weight: 600;">{{ $event->notice_sent_date->format('Y-m-d') }}</p>
                        </div>
                        <div>
                            <h4 style="color: #86868b; font-size: 0.85rem; margin-bottom: 5px;">رقم المرجع</h4>
                            <p style="font-weight: 600;">{{ $event->notice_reference }}</p>
                        </div>
                    </div>
                </div>
                @else
                <div style="background: white; padding: 30px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
                    <h2 style="font-size: 1.3rem; font-weight: 700; margin-bottom: 20px;">إرسال الإشعار التعاقدي</h2>
                    
                    <form method="POST" action="{{ route('time-bar.events.send-notice', $event->id) }}">
                        @csrf
                        
                        <div style="margin-bottom: 20px;">
                            <label style="display: block; margin-bottom: 8px; font-weight: 600;">رقم المرجع *</label>
                            <input type="text" name="notice_reference" required style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-family: 'Cairo', sans-serif;">
                        </div>

                        <div style="margin-bottom: 20px;">
                            <label style="display: block; margin-bottom: 8px; font-weight: 600;">تاريخ الإرسال *</label>
                            <input type="date" name="notice_date" required value="{{ date('Y-m-d') }}" style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-family: 'Cairo', sans-serif;">
                        </div>

                        <button type="submit" style="background: #34c759; color: white; padding: 12px 24px; border: none; border-radius: 8px; cursor: pointer; font-family: 'Cairo', sans-serif; font-weight: 600; width: 100%;">
                            تأكيد إرسال الإشعار
                        </button>
                    </form>
                </div>
                @endif
            </div>

            <!-- Right Column -->
            <div>
                <!-- Timeline Card -->
                <div style="background: white; padding: 30px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); margin-bottom: 20px;">
                    <h2 style="font-size: 1.3rem; font-weight: 700; margin-bottom: 20px;">المواعيد الحرجة</h2>
                    
                    <div style="margin-bottom: 20px;">
                        <h4 style="color: #86868b; font-size: 0.85rem; margin-bottom: 5px;">فترة الإشعار</h4>
                        <p style="font-weight: 600; font-size: 1.5rem;">{{ $event->notice_period_days }} يوم</p>
                    </div>

                    <div style="margin-bottom: 20px;">
                        <h4 style="color: #86868b; font-size: 0.85rem; margin-bottom: 5px;">آخر موعد للإشعار</h4>
                        <p style="font-weight: 600; font-size: 1.2rem; color: #ff3b30;">{{ $event->notice_deadline->format('Y-m-d') }}</p>
                    </div>

                    <div>
                        <h4 style="color: #86868b; font-size: 0.85rem; margin-bottom: 5px;">الأيام المتبقية</h4>
                        @if($event->days_remaining <= 0)
                        <p style="font-weight: 700; font-size: 2rem; color: #ff3b30;">انتهى!</p>
                        @elseif($event->days_remaining <= 3)
                        <p style="font-weight: 700; font-size: 2rem; color: #ff3b30;">{{ $event->days_remaining }} يوم</p>
                        @elseif($event->days_remaining <= 7)
                        <p style="font-weight: 700; font-size: 2rem; color: #ff9500;">{{ $event->days_remaining }} يوم</p>
                        @else
                        <p style="font-weight: 700; font-size: 2rem; color: #34c759;">{{ $event->days_remaining }} يوم</p>
                        @endif
                    </div>
                </div>

                <!-- Responsibility Card -->
                <div style="background: white; padding: 30px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
                    <h2 style="font-size: 1.3rem; font-weight: 700; margin-bottom: 20px;">المسؤولية</h2>
                    
                    <div style="margin-bottom: 20px;">
                        <h4 style="color: #86868b; font-size: 0.85rem; margin-bottom: 5px;">تم التحديد بواسطة</h4>
                        <p style="font-weight: 600;">{{ $event->identifiedBy->name }}</p>
                    </div>

                    @if($event->assignedTo)
                    <div>
                        <h4 style="color: #86868b; font-size: 0.85rem; margin-bottom: 5px;">المسؤول</h4>
                        <p style="font-weight: 600;">{{ $event->assignedTo->name }}</p>
                    </div>
                    @endif

                    <div style="margin-top: 20px; padding-top: 20px; border-top: 1px solid #f5f5f7;">
                        <h4 style="color: #86868b; font-size: 0.85rem; margin-bottom: 5px;">تاريخ الإنشاء</h4>
                        <p style="font-weight: 600;">{{ $event->created_at->format('Y-m-d H:i') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    lucide.createIcons();
</script>
@endsection
