@extends('layouts.app')

@section('content')
<div style="padding: 40px;">
    <div style="max-width: 1400px; margin: 0 auto;">
        <h1 style="font-size: 2rem; font-weight: 700; margin-bottom: 30px;">التنبيهات والإشعارات</h1>

        <div style="background: white; padding: 30px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
            @if($alerts->count() > 0)
            @foreach($alerts as $alert)
            <div style="border-bottom: 1px solid #f5f5f7; padding: 20px 0; {{ $loop->first ? 'padding-top: 0;' : '' }}">
                <div style="display: flex; justify-content: space-between; align-items: start;">
                    <div style="flex: 1;">
                        <div style="display: flex; align-items: center; gap: 15px; margin-bottom: 10px;">
                            @php
                            $alertColors = [
                                'first_warning' => '#0071e3',
                                'second_warning' => '#5856d6',
                                'urgent_warning' => '#ff9500',
                                'critical_warning' => '#ff3b30',
                                'final_warning' => '#ff3b30',
                                'expired' => '#8e8e93',
                            ];
                            @endphp
                            <span style="background: {{ $alertColors[$alert->alert_type] ?? '#86868b' }}; color: white; padding: 6px 15px; border-radius: 20px; font-size: 0.85rem; font-weight: 600;">
                                {{ $alert->getAlertTypeLabel() }}
                            </span>
                            <span style="color: #86868b; font-size: 0.9rem;">{{ $alert->sent_at->diffForHumans() }}</span>
                        </div>
                        
                        <h3 style="font-size: 1.1rem; font-weight: 700; margin-bottom: 5px;">
                            <a href="{{ route('time-bar.events.show', $alert->event->id) }}" style="color: #1d1d1f; text-decoration: none;">
                                {{ $alert->event->title }}
                            </a>
                        </h3>
                        
                        <p style="color: #86868b; margin-bottom: 10px;">
                            المشروع: {{ $alert->event->project->name }} | 
                            الأيام المتبقية: <strong style="color: {{ $alert->days_remaining <= 3 ? '#ff3b30' : '#ff9500' }}">{{ $alert->days_remaining }}</strong>
                        </p>

                        @if(!$alert->acknowledged)
                        <button style="background: #34c759; color: white; padding: 8px 16px; border: none; border-radius: 6px; cursor: pointer; font-family: 'Cairo', sans-serif; font-size: 0.85rem; font-weight: 600;">
                            تأكيد القراءة
                        </button>
                        @else
                        <span style="color: #34c759; font-size: 0.85rem;">
                            ✓ تم التأكيد بواسطة {{ $alert->acknowledgedBy->name }}
                        </span>
                        @endif
                    </div>
                    
                    <i data-lucide="bell" style="width: 24px; height: 24px; color: {{ $alertColors[$alert->alert_type] ?? '#86868b' }};"></i>
                </div>
            </div>
            @endforeach

            <div style="margin-top: 30px;">
                {{ $alerts->links() }}
            </div>
            @else
            <div style="text-align: center; padding: 60px 20px;">
                <i data-lucide="bell-off" style="width: 64px; height: 64px; color: #86868b; margin-bottom: 20px;"></i>
                <h3 style="font-size: 1.2rem; font-weight: 700; margin-bottom: 10px;">لا توجد تنبيهات</h3>
                <p style="color: #86868b;">لم يتم إرسال أي تنبيهات حتى الآن</p>
            </div>
            @endif
        </div>
    </div>
</div>

<script>
    lucide.createIcons();
</script>
@endsection
