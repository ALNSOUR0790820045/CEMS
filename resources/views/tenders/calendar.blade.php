@extends('layouts.app')

@section('content')
<style>
    .container { max-width: 1200px; margin: 0 auto; padding: 20px; }
    .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
    .header h1 { font-size: 1.8rem; font-weight: 700; margin: 0; }
    .btn { padding: 10px 20px; border-radius: 8px; text-decoration: none; font-weight: 600; transition: all 0.2s; display: inline-flex; align-items: center; gap: 8px; }
    .btn-secondary { background: #f5f5f7; color: #1d1d1f; }
    .calendar { background: white; padding: 30px; border-radius: 12px; }
    .calendar-header { font-size: 1.3rem; font-weight: 700; text-align: center; margin-bottom: 30px; color: #0071e3; }
    .calendar-grid { display: grid; grid-template-columns: repeat(7, 1fr); gap: 10px; }
    .calendar-day { aspect-ratio: 1; display: flex; flex-direction: column; padding: 10px; border: 1px solid #f0f0f0; border-radius: 8px; position: relative; cursor: pointer; transition: all 0.2s; }
    .calendar-day:hover { background: #f8f9fa; }
    .day-number { font-weight: 600; font-size: 0.9rem; }
    .day-events { margin-top: 5px; display: flex; flex-direction: column; gap: 3px; }
    .day-event { font-size: 0.7rem; padding: 3px 6px; background: #0071e3; color: white; border-radius: 4px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .list-view { margin-top: 30px; }
    .event-item { background: white; padding: 20px; border-radius: 8px; margin-bottom: 15px; border-right: 4px solid #0071e3; }
    .event-title { font-weight: 700; font-size: 1.1rem; margin-bottom: 10px; }
    .event-meta { display: flex; gap: 20px; font-size: 0.9rem; color: #666; }
</style>

<div class="container">
    <div class="header">
        <h1>تقويم المواعيد</h1>
        <a href="{{ route('tenders.index') }}" class="btn btn-secondary">
            <i data-lucide="arrow-right" style="width: 18px; height: 18px;"></i>
            العودة للقائمة
        </a>
    </div>

    <div class="calendar">
        <div class="calendar-header">
            {{ now()->format('F Y') }}
        </div>
        
        <div class="list-view">
            <h3 style="margin-bottom: 20px; font-size: 1.2rem; font-weight: 700;">المواعيد القادمة</h3>
            
            @if($tenders->count() > 0)
                @foreach($tenders->sortBy('submission_deadline') as $tender)
                <div class="event-item">
                    <div class="event-title">
                        <a href="{{ route('tenders.show', $tender) }}" style="text-decoration: none; color: #0071e3;">
                            {{ $tender->name }}
                        </a>
                    </div>
                    <div class="event-meta">
                        <div>
                            <i data-lucide="calendar" style="width: 16px; height: 16px;"></i>
                            آخر موعد للتقديم: {{ $tender->submission_deadline->format('Y-m-d') }}
                        </div>
                        <div>
                            <i data-lucide="building-2" style="width: 16px; height: 16px;"></i>
                            {{ $tender->client?->name ?? $tender->client_name ?? '-' }}
                        </div>
                        @if($tender->estimated_value)
                        <div>
                            <i data-lucide="dollar-sign" style="width: 16px; height: 16px;"></i>
                            {{ number_format($tender->estimated_value, 0) }} {{ $tender->currency }}
                        </div>
                        @endif
                    </div>
                </div>
                @endforeach
            @else
                <div style="text-align: center; padding: 60px; color: #999;">
                    <i data-lucide="calendar-x" style="width: 48px; height: 48px;"></i>
                    <p>لا توجد مواعيد قادمة</p>
                </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
    lucide.createIcons();
</script>
@endpush
@endsection
