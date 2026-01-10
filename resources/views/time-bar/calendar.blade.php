@extends('layouts.app')

@section('content')
<div style="padding: 40px;">
    <div style="max-width: 1400px; margin: 0 auto;">
        <h1 style="font-size: 2rem; font-weight: 700; margin-bottom: 30px;">تقويم المواعيد التعاقدية</h1>

        <div style="background: white; padding: 30px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
            <div id="calendar" style="min-height: 600px;"></div>
        </div>
    </div>
</div>

<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');
    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        locale: 'ar',
        direction: 'rtl',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,listWeek'
        },
        events: @json($events),
        eventClick: function(info) {
            info.jsEvent.preventDefault();
            if (info.event.url) {
                window.location.href = info.event.url;
            }
        },
        eventClassNames: function(arg) {
            return [arg.event.extendedProps.className || 'event-normal'];
        }
    });
    calendar.render();
});
</script>

<style>
.event-expired { background: #ff3b30 !important; border-color: #ff3b30 !important; }
.event-critical { background: #ff3b30 !important; border-color: #ff3b30 !important; }
.event-urgent { background: #ff9500 !important; border-color: #ff9500 !important; }
.event-warning { background: #ffcc00 !important; border-color: #ffcc00 !important; }
.event-normal { background: #34c759 !important; border-color: #34c759 !important; }
</style>

<script>
    lucide.createIcons();
</script>
@endsection
